<?php

namespace Scubawhere\Entities;

use Scubawhere\Exceptions\Http\HttpUnprocessableEntity;
use Scubawhere\Repositories\PackageRepo;

trait Bookable
{
    /**
     * Helper function to determine if the entity is viable to include agent commission
     * 
     * @return bool
     */
    public function isCommisioned()
    {
        return (bool) $this->commisionable;
    }

    /**
     * Calculate the price of the entity by determing which seasonal / base price applies
     * 
     * @param $start
     * @param bool $limitBefore
     */
    public function calculatePrice($start, $limitBefore = false) 
    {
        $price = Price::where(Price::$owner_id_column_name, $this->id)
            ->where(Price::$owner_type_column_name, get_class($this))
            ->where('from', '<=', $start)
            ->where(function($query) use ($start)
            {
                $query->whereNull('until')
                    ->orWhere('until', '>=', $start);
            })
            ->where(function($query) use ($limitBefore)
            {
                if($limitBefore)
                    $query->where('created_at', '<=', $limitBefore);
            })
            ->orderBy('id', 'DESC')
            ->withTrashed()
            ->first();

        $this->decimal_price = $price->decimal_price;
    }

    /**
     * Get all bookings that are saved as quotes.
     * 
     * Quotes are just bookings that have the booking status 'saved'.
     * 
     * @return mixed
     */
    public function getQuotes()
    {
        if (isset($this->bookings)) {
            $this->load('bookings');
        }
        return $this->bookings
            ->filter(function ($obj) {
                return $obj->status === 'saved';
            });
    }

    public function deleteQuotes()
    {
        $this->getQuotes()->each(function ($course) { $course->delete(); });
        return $this;
    }

    /**
     * Get all the bookings associated to the entity that are considered 'active'.
     * 
     * Active bookings are bookings that are either initialised, temporary (edited) or confirmed.
     * 
     * @return mixed
     */
    public function getActiveBookings()
    {
        if (isset($this->bookings)) {
            $this->load('bookings');
        }
        return $this->bookings
            ->filter(function($obj) {
                return Booking::isActive($obj->status);
            });
    }

    /**
     * Sync all prices to a given.
     *
     * This function will create any prices given in the prices array not in the model,
     * update any prices in the model and the array aswell as deleting any prices in
     * the model that are not present in the prices array. This is mainly used when
     * editing bookable items such as packages, tickets etc.
     *
     * After more investigation i found this, http://stackoverflow.com/questions/14157586/php-type-hinting-traits/14157842#14157842,
     * it basically discussing using an interface for all objects that use the trait, then that way we can type hint
     * the interface. Now there still exists the challenge of enforcing that the object inherits the interface when
     * using the trait, but it atleast would give us some more protection?
     * 
     * @todo Move this to the price service
     *
     * @param array $prices
     * @return mixed
     * @throws HttpUnprocessableEntity
     */
    public function syncPrices(array $prices)
    {
        foreach ($prices as &$price) {
            $price['new_decimal_price'] = $price['decimal_price'];
            unset($price['decimal_price']);
        }
        // Go through prices and remove any that do not have an amount
        $prices = array_filter($prices, function ($obj) {
            return ! empty($obj['new_decimal_price']); 
        });
        
        // If the model's prices arent loaded, lazy load them
        if (!isset($this->prices)) {
            $this->load('prices');
        }
        if (!isset($this->basePrices)) {
            $this->load('basePrices');
        }
        
        $existing_prices = $this->basePrices->getDictionary();
        $existing_prices += $this->prices->getDictionary();

        // Calculate deleted prices
        $deleted_prices = array_diff_key($existing_prices, $prices);

        // Calculate new prices
        $new_prices = array_diff_key($prices, $existing_prices);

        // Calculate existing / updated prices
        $updated_prices = array_intersect_key($existing_prices, $prices);
        
        // Delete prices
        Price::where('owner_type', get_class($this))
            ->whereIn('id', array_keys($deleted_prices))
            ->delete();

        // Create new prices
        $new_prices_objs = [];
        foreach ($new_prices as $price) {
            // validate the price
            $new_price = new Price($price);
            if (!$new_price->validate()) {
                throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, $new_price->errors()->all());
            }
            array_push($new_prices_objs, new Price($price));
        }
        // Save the prices to 'prices', to reduce the number of prices that used the base price
        // so that when we finally remove base prices, there is less data to move
        $this->prices()->saveMany($new_prices_objs);

        // Update existing prices
        foreach ($updated_prices as $price) {
            if (!$price->update($prices[$price->id])) {
                throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, $price->errors()->all());
            }
        }

        return $this;
    }

    /**
     * New sync prices method, needed as the way the front end sends
     * the prices needs to change. Affecting accommodations so far
     */
    public function syncPrices_new(array $prices)
    {
        // Go through prices and remove any that do not have an amount
        $prices = array_filter($prices, function ($obj) {
            return ! empty($obj['decimal_price']);
        });

        // If the model's prices aren't loaded, lazy load them
        if (!isset($this->allPrices)) {
            $this->load('allPrices');
        }
        
        $newPrices = $updatedPrices = [];
        
        // If a price is submitted with an ID, then perform an update, otherwise
        // it is a new price and should be created
        foreach ($prices as $price) {
            if (isset($price->id)) {
                $updatedPrices[] = $price;
            } else {
                $newPrices[] = $price;
            }
        }

        $updatedPrices = $this->allPrices->filter(function ($obj) use ($updatedPrices) {
            return in_array($obj->id, $updatedPrices);
        });
        
        // Determine any missing prices that were deleted
        $deletedPrices = array_diff(
            array_pluck($this->allPrices, 'id'),
            array_pluck($updatedPrices, 'id')
        );

        // Delete prices
        Price::where('owner_type', get_class($this))
            ->whereIn('id', $deletedPrices)
            ->delete();

        // Create new prices
        $new_prices_objs = [];
        foreach ($newPrices as $price) {
            $price['new_decimal_price'] = $price['decimal_price'];
            unset($price['decimal_price']);
            if (empty($price['until'])) {
                unset($price['until']);
            }
            // validate the price
            $new_price = new Price($price);
            if (!$new_price->validate()) {
                throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, $new_price->errors()->all());
            }
            array_push($new_prices_objs, new Price($price));
        }
        // Save the prices to 'prices', to reduce the number of prices that used the base price
        // so that when we finally remove base prices, there is less data to move
        $this->prices()->saveMany($new_prices_objs);

        $prices = $this->allPrices->getDictionary();
        // Update existing prices
        foreach ($updatedPrices as $price) {
            $price['new_decimal_price'] = $price['decimal_price'];
            unset($price['decimal_price']);
            if (empty($price['until'])) {
                unset($price['until']);
            }
            //if (!$price->update($prices[$price->id])) {
            if (!$prices[$price->id]->update($price)) {
                throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, $price->errors()->all());
            }
        }

        return $this;
    }
    
    public function bookings()
    {
		return $this->belongsToMany(Booking::class, 'booking_details');
    }

    /**
     * @return mixed
     */
    public function bookingdetails()
    {
        return $this->hasMany(Bookingdetail::class);
    }

    /**
     * @todo rename this seasonalPrices
     * @return mixed
     */
    public function prices()
    {
        return $this->morphMany(Price::class, 'owner')
            ->whereNotNull('until')
            ->orderBy('from');
    }

    /**
     * @return mixed
     */
    public function basePrices()
    {
        return $this->morphMany(Price::class, 'owner')
            ->whereNull('until')
            ->orderBy('from');
    }

    /**
     * @todo when prices() gets renamed, rename this to prices
     * @return mixed
     */
    public function allPrices()
    {
        return $this->morphMany(Price::class, 'owner')->orderBy('from');
    }

    /**
     * @return mixed
     */
    public function customers()
    {
        return $this->hasManyThrough(Customer::class, Bookingdetail::class); // 'Bookingdetail'
    }
}