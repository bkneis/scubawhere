<?php
namespace ScubaWhere\Repositories\Boat;

interface BoatRoomInterface
{
    public function all();

    public function allWithTrashed();

    public function get($id);

    public function getWhere($column, $value);

    public function create($input);

    public function update($id, $data, $boatrooms);

    public function delete($id);

    public function deleteWhere($column, $value);
}
