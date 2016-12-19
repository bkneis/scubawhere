<?php

namespace Scubawhere\Repositories;

use Scubawhere\Exceptions\Http\HttpNotFound;

/**
 * A generic repository for performing common DOA queries on Eloquent
 * models that inherit the Ownable trait.
 * 
 * @package Scubawhere\Repositories
 */
abstract class EloquentRepo
{
    /*
     * The class name of the eloquent model
     */
    private $model;

    public function __construct($model)
    {
        $this->model = $model; 
    }

    /**
     * Get all models including any requested relations
     * 
     * Models will be filtered by the global 'Ownable' scope so that only
     * models that are authorized by the current user will be returned.
     *
     * @param array $relations
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(array $relations = []) 
    {
        return call_user_func(array($this->model, 'with'), $relations)->get();
    }

    /**
     * Get all models, including soft deleted ones.
     * 
     * Soft deleted models are any where the deleted_at timestamp is NOT null
     *
     * @param array $relations
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allWithTrashed(array $relations = []) 
    {
        return call_user_func(array($this->model, 'with'), $relations)->withTrashed()->get();
    }

    /**
     * Get a model by its primary key, with any requested relationships.
     * 
     * If the fail flag is set and no model could be found, the http request
     * will fail and a response with return a 404 NOT FOUND
     *
     * @param int $id
     * @param array $relations
     * @param bool $fail
     * @return \ScubaWhere\Entities\Course
     * @throws HttpNotFound
     */
    public function get($id, array $relations = [], $fail = true) 
    {
        $model = call_user_func(array($this->model, 'with'), $relations)->find($id);

        if(is_null($model) && $fail) {
            throw new HttpNotFound(__CLASS__ . __METHOD__, ['The model could not be found']);
        }

        return $model;
    }

    /**
     * Get all models, and their requested relations that match the given query scopes.
     * 
     * The query scopes are listed in the array and applied individually.
     *
     * @param array $query
     * @param array $relations
     * @param bool  $fail
     * @throws \Scubawhere\Exceptions\Http\HttpNotFound
     * @return \ScubaWhere\Entities\Course
     */
    public function getWhere(array $query, array $relations = [], $fail = true) 
    {
        $model = call_user_func(array($this->model, 'where'), $query)->with($relations)->find();

        if(is_null($model) && $fail) {
            throw new HttpNotFound(__CLASS__ . __METHOD__, ['The model could not be found']);
        }

        return $model;
    }
}