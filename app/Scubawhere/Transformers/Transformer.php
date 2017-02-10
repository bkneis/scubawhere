<?php

namespace Scubawhere\Transformers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Abstract transformer to be extended by specific entity transformers.
 * 
 * This class is used to provide a means of changing the data before being
 * returned as JSON by the API. This reduces the likelihood of versioning
 * the API just to change internal variable names.
 * 
 * @package Scubawhere\Transformers
 */
abstract class Transformer
{
    /**
     * Transform an entity by removing unwanted data before returned as JSON
     * 
     * @param Model $agent
     * @return mixed
     */
    public abstract function transform($agent);

    /**
     * Perform transformation on an array of models
     * 
     * @param $models
     * @return array
     */
    public function transformMany($models) 
    {
        if ($models instanceof Collection) {
            $models = $models->toArray();
        }
        return array_map(function ($obj) { 
            return $this->transform($obj); 
        }, $models);    
    }

}