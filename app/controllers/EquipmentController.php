<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Scubawhere\Context;

class EquipmentController extends Controller {

	public function getIndex()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			return Context::get()->equipment()->with('equipmentCategory.prices')->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The equipment could not be found.')), 404 ); // 404 Not Found
		}
	}

	public function getAll()
	{
		return Context::get()->equipment()->with('equipmentCategory.prices')->get();
	}

	public function postAdd()
	{
		$data = Input::only('uuids', 'sizes', 'category_id'); // uuid, size, category_id
        $equipment_data = [];
        $equipment = null;
        
        for($i = 0; $i < sizeof($data['uuids']); $i++)
        {
            $equipment_data['uuid'] = $data['uuids'][$i];
            $equipment_data['size'] = $data['sizes'][$i];
            $equipment_data['category_id'] = $data['category_id'];
            $equipment = new Equipment($equipment_data);
            if( !$equipment->validate() )
            {
                return Response::json( array('errors' => $equipment->errors()->all()), 406 ); // 406 Not Acceptable
            }
            $equipment = Context::get()->equipment()->save($equipment);
        }

		return Response::json( array('status' => 'OK. Equipment created'), 201 ); // 201 Created
	}

	public function postEdit()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$equipment = Context::get()->equipment()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The equipment item could not be found.')), 404 ); // 404 Not Found
		}

		$data = Input::only(
			'uuid',
			'size'
		);

		if( !$equipment->update($data) )
		{
			return Response::json( array('errors' => $equipment->errors()->all()), 406 ); // 406 Not Acceptable
		}

		return Response::json( array('status' => 'OK. Equipment item updated.'), 200 ); // 200 OK
	}
    
    public function postDelete()
    {
        try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$equipment = Context::get()->equipment()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The equipment item could not be found.')), 404 ); // 404 Not Found
		}
        
        try 
        {
            $equipment->forceDelete();
        }
        catch(QueryException $e)
        {
            return Response::json( array('errors' => array('The equipment item could not be deleted.')), 406 );
        }
        
        return Response::json( array('status' => 'OK. Equipment item deleted.'), 200 ); // 200 OK
    }
}