<?php


namespace Scubawhere\Strategies;

use Scubawhere\Entities\Booking;

class CancellationsReportGenerator implements ReportGeneratorInterface
{
    protected $type;

    public function __construct($type)
    {
        $this->type = $type;
    }

    public function createReport($before, $after)
    {
        $type = $this->type;

        return Booking::onlyOwners()
            ->whereBetween('created_at', [$after, $before])
            ->where(function ($q) use ($type) {
               if($type === 'cancellation') {
                   $q->where('status', '=', 'cancelled');
               } elseif($type === 'discount') {
                   $q->whereNotNull('discount');
               }
            })
            ->select('id', 'reference', 'source', 'cancel_reason', 'cancellation_fee', 'lead_customer_id', 'cancelled_at', 'created_at')
            ->with('lead_customer')
            ->get();
    }
}