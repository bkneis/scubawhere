<?php namespace Repositories\Boat;

use ScubaWhere\Context;
use ScubaWhere\Exceptions;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BoatRepository implements BoatInterface
{
    protected $model;
    
    public function __construct()
    {
        $this->model = Context::get()->boats();
    }

    public function all()
    {
        return $this->model->with('boatrooms')->get();
    }

    public function allWithTrashed()
    {
        return $this->model->withTrashed()->with('boatrooms')->get();
    }

    public function get($id)
    {
        if(!$id) throw new ModelNotFoundException();
        return $this->model->with('boatrooms')->findOrFail( $id );
    }

    public function getWhere($column, $value) {}

    public function create($data)
    {
        $boat = new Boat($data);

        if( !$boat->validate() )
        {
            throw new InputNotValidException($boat->errors());
        }

        return $this->model->save($boat);

    }

    public function update($id, $data, $boatrooms)
    {
        $boat = $this->get($id);

        if( !$boat->update($data) )
        {
            throw new InputNotValidException($boat->errors()->all());
            //return Response::json( array('errors' => $boat->errors()->all()), 406 ); // 406 Not Acceptable
        }

        return $boat;

    }

    public function delete($id) {}

    public function deleteWhere($column, $value) {}

}