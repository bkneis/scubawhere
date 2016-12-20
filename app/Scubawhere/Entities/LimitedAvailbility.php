<?php

namespace Scubawhere\Entities;

trait LimitedAvailability {

    public function setAvailableFromAttribute($value)
    {
        $value = trim($value);
        $this->attributes['available_from'] = $value ?: null;
    }

    public function setAvailableUntilAttribute($value)
    {
        $value = trim($value);
        $this->attributes['available_until'] = $value ?: null;
    }

    public function setAvailableForFromAttribute($value)
    {
        $value = trim($value);
        $this->attributes['available_for_from'] = $value ?: null;
    }

    public function setAvailableForUntilAttribute($value)
    {
        $value = trim($value);
        $this->attributes['available_for_until'] = $value ?: null;
    }
    
}
