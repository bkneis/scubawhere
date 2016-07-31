<?php
namespace ScubaWhere\Repositories\Boat;

use ScubaWhere\Context;
use ScubaWhere\Exceptions;
use ScubaWhere\Repositories;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BoatRoomRepository implements BoatRoomInterface
{
    protected $model;

    public function __construct()
    {
        $this->model = Context::get()->boatrooms();
    }

    public function all()
    {
        return $this->model->get();
    }

    public function allWithTrashed()
    {
        return $this->model->withTrashed()->get();
    }

    public function get($id)
    {
        if(!$id) throw new ModelNotFoundException();
        return $this->model->findOrFail( $id );
    }

    public function getWhere($column, $value) {}

    public function create($data)
    {
        $boatroom = new Boatroom($data);

        if(!$boatroom->validate()) throw new InputNotValidException($boatroom->errors());

        return $this->model->save($boatroom);

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
