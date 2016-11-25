<?php

namespace Scubawhere\Strategies;

use Scubawhere\Entities\Booking;

class DiscountsReportGenerator
{
    public function createReport($before, $after)
    {
        return Booking::onlyOwners()
            ->whereBetween('created_at', [$after, $before])
            ->whereNotNull('discount')
            ->where('discount', '>', '0')
            ->select('id', 'reference', 'source', 'discount_reason', 'discount', 'lead_customer_id', 'created_at', 'price')
            ->with('lead_customer')
            ->get();
    }
}