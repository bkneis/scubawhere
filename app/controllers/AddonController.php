<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use ScubaWhere\Helper;
use ScubaWhere\Context;

class AddonController extends Controller {

	public function getIndex()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			return Context::get()->addons()->withTrashed()->with('basePrices')->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The addon could not be found.')), 404 ); // 404 Not Found
		}
	}

	public function getAll()
	{
		return Context::get()->addons()->with('basePrices')->get();
	}

	public function getAllWithTrashed()
	{
		return Context::get()->addons()->withTrashed()->with('basePrices')->get();
	}

	public function getCompulsory()
	{
		return Context::get()->addons()->where('compulsory', true)->with('basePrices')->get();
	}

	public function postAdd()
	{
		$data = Input::only(
			'name',
			'description',
			'compulsory',
			'parent_id' // Please NEVER use parent_id in the front-end!
		);

		//Check compulsory field.....
		if (empty($data['compulsory'])) {
			$data['compulsory'] = 0;
		}

		// ####################### Prices #######################
		$base_prices = Input::get('base_prices');
		if( !is_array($base_prices) )
			return Response::json( array( 'errors' => array('The "base_prices" value must be of type array!')), 400 ); // 400 Bad Request

		// Filter out empty and existing prices
		$base_prices = Helper::cleanPriceArray($base_prices);

		// Check if 'prices' input array is now empty
		if( empty($base_prices) )
			return Response::json( array( 'errors' => array('You must submit at least one base price!')), 400 ); // 400 Bad Request
		// ##################### End Prices #####################

		$addon = new Addon($data);

		if( !$addon->validate() )
		{
			return Response::json( array('errors' => $addon->errors()->all()), 406 ); // 406 Not Acceptable
		}

		$addon = Context::get()->addons()->save($addon);

		// ####################### Prices #######################
		// Normalise base_prices array
		$base_prices = Helper::normaliseArray($base_prices);
		// Create new base_prices
		foreach($base_prices as &$base_price)
		{
			$base_price = new Price($base_price);

			if( !$base_price->validate() )
				return Response::json( array('errors' => $base_price->errors()->all()), 406 ); // 406 Not Acceptable
		}

		$addon->basePrices()->saveMany($base_prices);
		// ##################### End Prices #####################

		$addon->load('basePrices');

		return Response::json( ['status' => 'OK. Addon created', 'model' => $addon], 201 ); // 201 Created
	}

	public function postEdit()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$addon = Context::get()->addons()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The addon could not be found.')), 404 ); // 404 Not Found
		}

		$data = Input::only(
			'name',
			'description',
			'compulsory'
		);

		// Check compulsory field
		if (empty($data['compulsory'])) {
			$data['compulsory'] = 0;
		}

		// ####################### Prices #######################
		if( Input::has('base_prices') )
		{
			$base_prices = Input::get('base_prices');
			if( !is_array($base_prices) )
				return Response::json( array( 'errors' => array('The "base_prices" value must be of type array!')), 400 ); // 400 Bad Request

			// Filter out empty and existing prices
			$base_prices = Helper::cleanPriceArray($base_prices);

			// Check if 'base_prices' input array is now empty
			if( empty($base_prices) )
				$base_prices = false;
		}
		else
			$base_prices = false;
		// ####################### End Prices #######################

		if( !$addon->update($data) )
		{
			return Response::json( array('errors' => $addon->errors()->all()), 406 ); // 406 Not Acceptable
		}

		if($base_prices)
		{
			// Normalise base_prices array
			$base_prices = Helper::normaliseArray($base_prices);
			// Create new base_prices
			foreach($base_prices as &$base_price)
			{
				$base_price = new Price($base_price);

				if( !$base_price->validate() )
					return Response::json( array('errors' => $base_price->errors()->all()), 406 ); // 406 Not Acceptable
			}

			$base_prices = $addon->basePrices()->saveMany($base_prices);
		}

		$addon->load('basePrices');

		return Response::json( ['status' => 'OK. Addon updated.', 'model' => $addon], 200 ); // 200 OK
	}

	public function postDelete()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$addon = Context::get()->addons()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The addon could not be found.')), 404 ); // 404 Not Found
		}

		if($addon->packages()->exists())
			return Response::json( array('errors' => array('The addon can not be removed currently because it is used in packages.')), 409); // 409 Conflict

		try
		{
			$addon->forceDelete();

			// If deletion worked, delete associated prices
			Price::where(Price::$owner_id_column_name, $addon->id)->where(Price::$owner_type_column_name, 'Addon')->delete();
		}
		catch(QueryException $e)
		{
			$addon = Context::get()->addons()->find( Input::get('id') );
			$addon->delete();
		}

		return array('status' => 'Ok. Addon deleted');
	}
}
