<?php


namespace Scubawhere\Entities;

use LaravelBook\Ardent\Ardent;

class Hotelstay extends Ardent
{
    protected $fillable = array('name', 'address', 'arrival', 'departure');

    public static $rules = array(
        'name'      => 'required',
        'arrival'   => 'date',
        'departure' => 'date'
    );

    public function customers()
    {
        return $this->hasMany('\Scubawhere\Entities\Customer');
    }
}