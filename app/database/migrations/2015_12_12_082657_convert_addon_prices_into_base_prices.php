<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use PhilipBrown\Money\Currency;
use Scubawhere\Context;

class ConvertAddonPricesIntoBasePrices extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$addons = Addon::with('company')->withTrashed()->get();

		foreach($addons as $addon)
		{
			Context::set($addon->company);

			$currency = new Currency( Context::get()->currency->code );
			$decimal_price = number_format(
				$addon->price / $currency->getSubunitToUnit(),
				strlen( $currency->getSubunitToUnit() ) - 1,
				'.',
				''
			);

			$data = [
				'new_decimal_price' => $decimal_price,
				'from'              => '0000-00-00',
				'created_at'        => '2000-01-01'
			];

			print_r($data);

			$price = new Price($data);

			if( !$price->validate() )
			{
				print_r($price->errors()->all());
				exit();
			}

			$addon->basePrices()->save($price);
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Price::where(Price::$owner_type_column_name, 'Addon')->delete();
	}

}
