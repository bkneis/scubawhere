<?php

namespace Scubawhere\Entities;

trait Owneable
{
    public static function bootOwneable()
    {
        static::addGlobalScope(new OwnableScope);
    }
    
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}