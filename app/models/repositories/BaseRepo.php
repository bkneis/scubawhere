<?php

namespace ScubaWhere\Repositories;

/**
 * BaseRepo Class is an abstract class used to extend all other
 * repositories so that they share similiar functionality.
 * In addition to this, some static functions are provided as utilities such as
 * dealing with DB transactions and associating models relationships.
 */
abstract class BaseRepo {

    public abstract function all();

    public abstract function allWithTrashed();

    public abstract function get($id);

    public abstract function getWhere($column, $value);

    public abstract function getWith($id, $relations);

    public abstract function delete($id);

    public abstract function deleteWhere($column, $value);

    public static function begin() 
    {
    	\DB::beginTransaction();
    }

    public static function undo() 
    {
    	\DB::rollback();
    }

    public static function finish() 
    {
    	\DB::commit();
    }

    public static function associate($model, $relations)
    {
    	try {
    		$model->sync($relations);
    	}
    	catch(\Exception $e) {
    		throw $e;	
    	}
    }

}
