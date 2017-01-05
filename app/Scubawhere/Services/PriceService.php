<?php

namespace Scubawhere\Services;

use Illuminate\Database\Eloquent\Model;
use Scubawhere\Helper;
use Scubawhere\Entities\Price;
use Scubawhere\Exceptions\Http\HttpBadRequest;
use Scubawhere\Repositories\PriceRepoInterface;

class PriceService {

	/** @var \Scubawhere\Repositories\AccommodationRepo */
	protected $price_repo;

	public function __construct(PriceRepoInterface $price_repo) {
		$this->price_repo = $price_repo;
	}

	public function cleanPriceArray($prices)
	{
		return array_filter($prices, function($element, $id)
		{
			// Filter out every price that is either empty or has a numeric ID (already exists)
			return !($element['new_decimal_price'] === '' || is_numeric($id));
		}, ARRAY_FILTER_USE_BOTH);
	}

	/**
	 * Validate the base prices and seasonal prices and ensure they are in correct format
	 *
	 * @param  array $base_prices
	 * @param  array $prices
	 *
	 * @throws \Scubawhere\Exceptions\Http\HttpBadRequest
	 *
	 * @return array The formatted base and seasonal prices
	 */
	public function validatePrices($base_prices, $prices) 
	{
		if($base_prices)
		{
	        if(!is_array($base_prices)) throw new HttpBadRequest(__CLASS__.__METHOD__, ['The "base_prices" value must be of type array!']);

	        // Filter out empty and existing prices
	        $base_prices = Helper::cleanPriceArray($base_prices);

	        // Check if 'prices' input array is now empty
	        if (empty($base_prices)) throw new HttpBadRequest(__CLASS__.__METHOD__, ['You must submit at least one base price!']);
    	}
    	else
    	{
    		$base_prices = false;
    	}

        if ($prices) 
        {
            if(!is_array($prices)) throw new HttpBadRequest(__CLASS__.__METHOD__, ['The "prices" value must be of type array!']);

            // Filter out empty and existing prices
            $prices = $this->cleanPriceArray($prices);
            // Check if 'prices' input array is now empty
            if (empty($prices)) $prices = false;
        } 
        else 
        {
            $prices = false;
        }

        return array('base' => $base_prices, 'seasonal' => $prices);
	}

	/**
	 * Save the base or seasonal price and associate it to a model
	 *
	 * @param  \Illuminate\Database\Eloquent\Model $model
	 * @param  array $prices The prices to associate
	 *
	 * @return \Illuminate\Database\Eloquent\Model $accommodation
	 */
	public function associatePrices($model, $prices) 
	{
		$prices = Helper::normaliseArray($prices);
		// Loop through the prices and categorize them into new, updated and deleted
		// go through each and do with which you wish and always use prices for the relationship
		// add to bookable trait the prices relationship and mark baseprice as redudant
        foreach ($prices as &$price) 
        {
        	$price = $this->price_repo->create($price);
        }
        $model->saveMany($prices);
        return $model;
	}

	public function delete($type, $id)
	{
		Price::where(Price::$owner_id_column_name, $id)
			->where(Price::$owner_type_column_name, $type)
			->delete();
	}

	public function sync(Model $model, array $prices)
	{
		// Go through prices and remove any that do not have an amount
		$prices = array_filter($prices, function ($obj) {
			return ! empty($obj['new_decimal_price']);
		});

		// If the model's prices arent loaded, lazy load them
		if (!isset($model->prices)) {
			$model->load('prices');
		}
		if (!isset($model->basePrices)) {
			$model->load('basePrices');
		}

		$existing_prices = $model->basePrices->getDictionary();
		$existing_prices += $model->prices->getDictionary();

		// Calculate deleted prices
		$deleted_prices = array_diff_key($existing_prices, $prices);

		// Calculate new prices
		$new_prices = array_diff_key($prices, $existing_prices);

		// Calculate existing / updated prices
		$updated_prices = array_intersect_key($existing_prices, $prices);

		// Delete prices
		Price::where('owner_type', get_class($model))
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
		$model->prices()->saveMany($new_prices_objs);

		// Update existing prices
		foreach ($updated_prices as $price) {
			if (!$price->update($prices[$price->id])) {
				throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, $price->errors()->all());
			}
		}

		return $model;
	}

}
