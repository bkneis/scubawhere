<?php
namespace ScubaWhere\Repositories;

interface BaseRepoInterface
{
    public function all();

    public function allWithTrashed();

    public function get($id);

    public function getWhere($column, $value);

    public function create($input);

    public function update($id);

    public function delete($id);

    public function deleteWhere($column, $value);
}
