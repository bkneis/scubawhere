<?php

namespace ScubaWhere\Services;

use ScubaWhere\Helper;
use ScubaWhere\Exceptions\BadRequestException;
use ScubaWhere\Repositories\PriceRepoInterface;

class PriceService {

	/** 
	 *	Repository to access the accommodation models
	 *	@var \ScubaWhere\Repositories\AccommodationRepo
	 */
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
	 * @param  array $base_prices
	 * @param  array $prices
	 * @throws \ScubaWhere\BadRequestException
	 * @return array The formatted base and seasonal prices
	 */
	public function validatePrices($base_prices, $prices) 
	{
		if($base_prices)
		{
	        if(!is_array($base_prices)) throw new BadRequestException(['The "base_prices" value must be of type array!']);

	        // Filter out empty and existing prices
	        $base_prices = Helper::cleanPriceArray($base_prices);

	        // Check if 'prices' input array is now empty
	        if (empty($base_prices)) throw new BadRequestException(['You must submit at least one base price!']);
    	}
    	else
    	{
    		$base_prices = false;
    	}

        if ($prices) 
        {
            if(!is_array($prices)) throw new BadRequestException(['The "prices" value must be of type array!']);

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
	 * @param  \Illuminate\Database\Eloquent\Model $accommodation Accommodation to associate prices to
	 * @param  array $prices The prices to associate
	 * @param  bool $is_base True if the prices are base prices
	 * @return \Illuminate\Database\Eloquent\Model $accommodation
	 */
	public function associatePrices($model, $prices) 
	{
		$prices = Helper::normaliseArray($prices);
        foreach ($prices as &$price) 
        {
        	$price = $this->price_repo->create($price);
        }
        $model->saveMany($prices);
        return $model;
	}

	public function delete($type, $id)
	{
		\Price::where(\Price::$owner_id_column_name, $id)
			->where(\Price::$owner_type_column_name, $type)
			->delete();
	}

}