<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Scubawhere\Context;

class EquipmentCategoryController extends Controller {

	public function getIndex()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			return Context::get()->equipmentCategories()->with('prices', 'equipment')->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The equipment category could not be found.')), 404 ); // 404 Not Found
		}
	}

	public function getAll()
	{
		return Context::get()->equipmentCategories()->with('equipment', 'prices')->get(); // prices and equipment not working
	}

	public function postAdd()
	{
		$data = Input::only(
			'name',
			'description'
		);

		$category = new EquipmentCategory($data);

		if( !$category->validate() )
		{
			return Response::json( array('errors' => $category->errors()->all()), 406 ); // 406 Not Acceptable
		}
        
        $category = Context::get()->equipmentCategories()->save($category);
        
        $equipmentItems = array();
        
        if(Input::has('equipment'))
        {
            $equipment = Input::only('equipment')['equipment'];
        
            foreach($equipment as $obj)
            {
                $equipment = new Equipment($obj);
                if( !$equipment->validate() )
                {
                    return Response::json( array('errors' => $equipment->errors()->all()), 406 ); // 406 Not Acceptable
                }
                array_push($equipmentItems, $equipment);
            }
            $category->equipment()->saveMany($equipmentItems); // use saveMany to save sql queries
        }
        
        $equipmentPrices = array();
        
        if(Input::has('prices'))
        {
            $prices = Input::only('prices')['prices'];
            
            foreach($prices as $obj)
            {
                $equipment_price = new EquipmentPrice($obj);
                if( !$equipment_price->validate() )
                {
                    return Response::json( array('errors' => $equipment_price->errors()->all()), 406 ); // 406 Not Acceptable
                }
                array_push($equipmentPrices, $equipment_price);
            }
            $category->prices()->saveMany($equipmentPrices);
        }

		return Response::json( array('status' => 'OK. Equipment Category created', 'id' => $category->id), 201 ); // 201 Created
	}

	public function postEdit()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$category = Context::get()->equipmentCategories()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The equipment category could not be found.')), 404 ); // 404 Not Found
		}

		$data = Input::only(
			'name',
			'description'
		);

		if( !$category->update($data) )
		{
			return Response::json( array('errors' => $category->errors()->all()), 406 ); // 406 Not Acceptable
		}
        // Need to find a way to still run if no equipment or prices are given as they could be deleted
        // or make atleast one equipment and price required
        if(Input::has('equipment'))
        {
            $equipment = Input::only('equipment')['equipment'];
            $oldEquipment = $category->equipment;
            $newEquipment = array();
            
            // check for new or updated equipment
            foreach($equipment as $newObj)
            {
                $object_added = true;
                
                if(array_key_exists('id', $newObj)) // if there is no id then the object must be new
                {
                    foreach($oldEquipment as $oldObj)
                    {
                        if($newObj['id'] == $oldObj->id) // if the key was found in old equipment it must be updated
                        {
                            $object_added = false;
                            $equipmentItem = $category->equipment()->where('id', '=', $newObj['id'])->first();
                            if( !$equipmentItem->update($newObj) )
                            {
                                return Response::json( array('errors' => $equipmentItem->errors()->all()), 406 ); // 406 Not Acceptable
                            }
                            break;
                        }
                    }
                }
                
                if($object_added)
                {
                    $equipmentItem = new Equipment($newObj);
                    if( !$equipmentItem->validate() )
                    {
                        return Response::json( array('errors' => $equipmentItem->errors()->all()), 406 ); // 406 Not Acceptable
                    }
                    array_push($newEquipment, $equipmentItem);
                }
            }
            
            $category->equipment()->saveMany($newEquipment);
            
            // check for equipment not in new equipment but in old (therefore they must have been deleted)
            foreach($oldEquipment as $oldObj)
            {
                $object_deleted = true;
                foreach($equipment as $newObj)
                {
                    if(array_key_exists('id', $newObj)) // not a new equipment
                    {
                        if($newObj['id'] == $oldObj->id)
                        {
                            $object_deleted = false;
                            break;
                        }
                    }
                }
                if($object_deleted)
                {
                    $equipmentItem = $category->equipment()->where('id', '=', $oldObj->id)->first();
                    try 
                    {
                        $equipmentItem->forceDelete();
                    }
                    catch(QueryException $e)
                    {
                        return Response::json( array('status' => 'Error, equipment could not be deleted.'), 406 ); 
                    }
                    
                }
            }
        }
        
        if(Input::has('prices'))
        {
            $prices = Input::only('prices')['prices'];
            $oldPrices = $category->prices;
            $newPrices = array();
            
            foreach($prices as $newObj)
            {
                $price_added = true;
                if(array_key_exists('id', $newObj)) // if there is no id then the object must be new
                {
                    foreach($oldPrices as $oldObj)
                    {
                        if($newObj['id'] == $oldObj->id) // if the key was found in old equipment it must be updated
                        {
                            $price_added = false;
                            $equipmentPrice = $category->prices()->where('id', '=', $newObj['id'])->first();
                            if( !$equipmentPrice->update($newObj) )
                            {
                                return Response::json( array('errors' => $equipmentPrice->errors()->all()), 406 ); // 406 Not Acceptable
                            }
                            break;
                        }
                    }
                }
                if($price_added)
                {
                    $equipmentPrice = new EquipmentPrice($newObj);
                    if( !$equipmentPrice->validate() )
                    {
                        return Response::json( array('errors' => $equipmentPrice->errors()->all()), 406 ); // 406 Not Acceptable
                    }
                    array_push($newPrices, $equipmentPrice);
                }
            }
            
            $category->prices()->saveMany($newPrices);
            
            // check for equipment not in new equipment but in old (therefore they must have been deleted)
            foreach($oldPrices as $oldObj)
            {
                $price_deleted = true;
                foreach($prices as $newObj)
                {
                    if(array_key_exists('id', $newObj)) // not new prices
                    {
                        if($newObj['id'] == $oldObj->id)
                        {
                            $price_deleted = false;
                            break;
                        }
                    }
                }
                if($price_deleted)
                {
                    $equipmentPrice = $category->prices()->where('id', '=', $oldObj->id)->first();
                    try 
                    {
                        $equipmentPrice->forceDelete();
                    }
                    catch(QueryException $e)
                    {
                        return Response::json( array('status' => 'Error, equipment price could not be deleted.'), 406 ); 
                    }
                    
                }
            }
        }

		return Response::json( array('status' => 'OK. Equipment Category updated.'), 200 ); // 200 OK
	}
    
    public function postDelete()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$equipment_category = Context::get()->equipmentCategories()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The boat could not be found.')), 404 ); // 404 Not Found
		}
        
        $prices = $equipment_category->prices();
        $equipment = $equipment_category->equipment();
        
        foreach($prices as $obj)
        {
            $obj->forceDelete();
        }
        
        foreach($equipment as $obj)
        {
            $obj->forceDelete();
        }
        
        //$equipment_category->prices()->detach();
        //$equipment_category->equipment()->detach();
        
        $equipment_category->forceDelete();
        

		return array('status' => 'Ok. Boat deleted');
	}
}