<?php

namespace Scubawhere\Entities;

trait Owneable
{
    public static function bootOwneable()
    {
        static::addGlobalScope(new OwnableScope);
    }
}