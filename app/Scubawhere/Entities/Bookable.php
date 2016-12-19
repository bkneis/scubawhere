<?php

namespace Scubawhere\Entities;

use Scubawhere\Exceptions\Http\HttpUnprocessableEntity;

trait Bookable
{
    public function isCommisioned()
    {
        return (bool) $this->commisionable;
    }

    public function getBookingPrice($query, $date)
    {
        return $query->where('owner_id', '=', $this->id)
            ->where('owner_type', '=', $this->getMorphClass())
            ->where('from', '>', $date)
            ->where('to', '<', $date);
    }

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
     * @note Ok I like this function bar one thing, there is no current way to check
     * that the model given to the function has access to a 'prices' relationship :/
     * What if instead we used this function in the 'bookable' trait so that we can access
     * the method via the model, i.e. $package->syncPrices($prices). I really like how this
     * reads but should the model be responsible for updating its prices. I feel that this
     * could be seen as bad design but in my eyes, the model can update its own variables such
     * as the name etc. so why not its relationships.
     *
     * After more investigation i found this, http://stackoverflow.com/questions/14157586/php-type-hinting-traits/14157842#14157842,
     * it basically discussing using an interface for all objects that use the trait, then that way we can type hint
     * the interface. Now there still exists the challenge of enforcing that the object inherits the interface when
     * using the trait, but it atleast would give us some more protection?
     * 
     * @note Should these be in the price service??
     *
     * @param array $prices
     * @return mixed
     * @throws HttpUnprocessableEntity
     */
    public function syncPrices(array $prices)
    {
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
        /*
         * Fade in. It was a cold morning, there sat 2 young and nieve developers
         * who didnt know there head from their arse. Programming away without a care
         * in the world. 3 years later there is no a system with somewhat questioanble
         * design decisions. Ok, joking aside, this really bugs me. Basically, when making the
         * system originally there were 'base prices' and 'prices' where the later acted as
         * seasonal price changes. It wasnt until we implmented them through the system we
         * realised they were essentially the same :/ And would have been way better to just use
         * a bool in the price such as 'is_base'.
         * 
         * So long story short, whenever dealing with prices, you must use basePrices and
         * prices then combine them, due to the morph many relationship using the name
         * of the calling function.
         */
        $existing_prices = $this->basePrices->getDictionary();
        $existing_prices += $this->prices->getDictionary();
        //$existing_prices = $this->prices->getDictionary();

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

    public function booking()
    {
        return $this->hasMany('\Scubawhere\Entities\Booking');
    }

    public function bookingdetails()
    {
        return $this->hasMany('\Scubawhere\Entities\Bookingdetail');
    }

    public function prices()
    {
        return $this->morphMany('\Scubawhere\Entities\Price', 'owner')->whereNotNull('until')->orderBy('from');
    }
    
    public function basePrices()
    {
        return $this->morphMany('\Scubawhere\Entities\Price', 'owner')->whereNull('until')->orderBy('from');
    }

    public function customers()
    {
        return $this->hasManyThrough('\Scubawhere\Entities\Customer', 'Bookingdetail');
    }
}