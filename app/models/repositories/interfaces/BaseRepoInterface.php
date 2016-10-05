<?php
namespace ScubaWhere\Repositories;

interface BaseRepoInterface
{
    public function all();

    public function allWithTrashed();

    public function get($id);

    public function getWhere($column, $value);

    public function create($data);

    public function update($id, $data);

    public function delete($id);

    public function deleteWhere($column, $value);
}
