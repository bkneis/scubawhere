<?php


namespace Scubawhere\Strategies;

use Scubawhere\Entities\Booking;

class CancellationsReportGenerator implements ReportGeneratorInterface
{
    public function createReport($before, $after)
    {
        return Booking::onlyOwners()
            ->whereBetween('created_at', [$after, $before])
            ->where('status', '=', 'cancelled')
            ->select('id', 'reference', 'source', 'cancel_reason', 'cancellation_fee', 'lead_customer_id', 'cancelled_at', 'created_at', 'price')
            ->with('lead_customer')
            ->get();
    }
}