<?php

namespace Scubawhere\Strategies;

use Scubawhere\Repositories\BookingRepo;
use Scubawhere\Contracts\BookingRepoInterface;

class SurchargesReportGenerator extends BaseReportGenerator implements ReportGeneratorInterface
{
    protected $bookings;

    public function __construct(/*BookingRepoInterface $bookings*/)
    {
        $this->bookings = new BookingRepo();
    }
    
    public function createReport($before, $after)
    {
        $bookings = $this->bookings->onlySurcharged($before, $after);
        $transactions = array();
        
        foreach ($bookings as $booking) {
            foreach ($booking->payments as $payment) {
                $payment['booking_ref'] = $booking->reference;
                $payment['type'] = 'payment';
                $payment['customer'] = array(
                    'firstname' => $booking->lead_customer->firstname,
                    'lastname'  => $booking->lead_customer->lastname
                );
            }
            foreach ($booking->refunds as $refund) {
                $refund['booking_ref'] = $booking->reference;
                $refund['type'] = 'refund';
                $refund['customer'] = array(
                    'firstname' => $booking->lead_customer->firstname,
                    'lastname'  => $booking->lead_customer->lastname
                );
            }
            $transactions = array_merge($transactions, $booking->payments->toArray());
            $transactions = array_merge($transactions, $booking->refunds->toArray());
        }

        $total = 0;
        foreach($transactions as $transaction) {
            $total += $transaction['surcharge'];
        }

        return array(
            'date_range'   => $this->getDates($before, $after),
            'transactions' => $transactions,
            'total'        => $total
        );
    }
}