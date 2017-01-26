<?php

namespace Scubawhere\Values;

class BookingStatus
{
    public $status;
    
    private $availableStatuses = ['confirmed', 'cancelled', 'initialised', 'reserved', 'saved'];
    
    private $activeStatuses = ['confirmed', 'cancelled', 'initialised'];

    public function __construct($status)
    {
        $this->status = $status;
    }

    public function isActive()
    {
        return in_array($this->status, $this->activeStatuses);
    }

    private function isValid($status)
    {
        return in_array($status, $this->availableStatuses);
    }

    public function __call($method, $args)
    {
        $status = lcfirst(substr($method, 2));
        if ($this->isValid($status)) {
            return $this->status === $status;
        }
    }

    public function isConfirmed()
    {
        return $this->status === 'confirmed';
    }

}