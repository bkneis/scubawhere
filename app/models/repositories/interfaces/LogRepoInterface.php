<?php namespace ScubaWhere\Repositories;

/**
 * Interface: LogRepoInterface
 *
 * Interface for the LogRepo Class
 *
 */
interface LogRepoInterface
{
    public function get($id);

    public function getAll();

    public function create($data);

    public function update($id, $data);

    public function delete($id);
}
