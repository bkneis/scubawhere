<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use ScubaWhere\Context;

class EquipmentPriceController extends Controller {

	public function getIndex()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			return Context::get()->equipmentPrices()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The equipment price could not be found.')), 404 ); // 404 Not Found
		}
	}

	public function getAll()
	{
		return Context::get()->equipmentPrices()->get();
	}

	public function postAdd()
	{
		$data = Input::only('prices'); // duration, price, category_id
        
        foreach($data as $item)
        {
            $price = new EquipmentPrice($item);
            if( !$price->validate() )
            {
                return Response::json( array('errors' => $price->errors()->all()), 406 ); // 406 Not Acceptable
            }
            $price = Context::get()->equipmentPrices()->save($price);
        }

		return Response::json( array('status' => 'OK. Equipment prices created'), 201 ); // 201 Created
	}

	public function postEdit()
	{
        $data = Input::only('prices'); // id, duration, price
        foreach($data as $new_price)
        {
            try
            {
                if( !$new_price['id'] ) throw new ModelNotFoundException();
                $price = Context::get()->equipmentPrices()->findOrFail( $new_price['id'] );
            }
            catch(ModelNotFoundException $e)
            {
                return Response::json( array('errors' => array('The equipment price could not be found.')), 404 ); // 404 Not Found
            }
            
            $new_price_data['duration'] = $new_price['duration'];
            $new_price_data['price'] = $new_price['price'];
            
            if( !$price->update($new_price_data) )
            {
                return Response::json( array('errors' => $price->errors()->all()), 406 ); // 406 Not Acceptable
            }
        }

		return Response::json( array('status' => 'OK. Equipment prices updated.'), 200 ); // 200 OK
	}
}
