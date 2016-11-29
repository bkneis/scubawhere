<?php

namespace Scubawhere\Repositories;

/**
 * BaseRepo Class is an abstract class used to extend all other
 * repositories so that they share similiar functionality.
 * In addition to this, some static functions are provided as utilities such as
 * dealing with DB transactions and associating models relationships.
 */
abstract class BaseRepo {

    public abstract function all(array $relations = []);

    public abstract function allWithTrashed(array $relations = []);

    public abstract function get($id, array $relations = [], $fail = true);

    public abstract function getWhere(array $query, array $relations = [], $fail = true);

}
