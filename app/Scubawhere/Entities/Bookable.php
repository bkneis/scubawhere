<?php


namespace Scubawhere\Entities;


trait Bookable
{
    public function isCommisioned()
    {
        return (bool) $this->commisionable;
    }

    public function getBookingPrice($query, $date)
    {
        return $query->where('owner_id', '=', $this->id)
            ->where('owner_type', '=', $this->getMorphClass())
            ->where('from', '>', $date)
            ->where('to', '<', $date);
    }

    public function bookingdetails()
    {
        return $this->hasMany('\Scubawhere\Entities\Bookingdetail');
    }

    public function basePrices()
    {
        return $this->morphMany('\Scubawhere\Entities\Price', 'owner')->whereNull('until')->orderBy('from');
    }

    public function customers()
    {
        return $this->hasManyThrough('\Scubawhere\Entities\Customer', 'Bookingdetail');
    }
}