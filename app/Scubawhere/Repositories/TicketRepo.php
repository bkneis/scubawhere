<?php 

namespace Scubawhere\Repositories;

use Scubawhere\Helper;
use Scubawhere\Entities\Ticket;

/**
 * Class TicketRepo acts as a DAO for the accommodation models.
 *
 * It should always return eloquent models and can be thought of as a collection. If at any point in the project, you
 * find yourself accessing the model to retrieve the same data, please add a function to this class and use that.
 *
 * @package Scubawhere\Repositories
 *
 * @see \Scubawhere\Repositories\BaseRepo
 * @see \Scubawhere\Repositories\TicketRepoInterface
 */
class TicketRepo extends EloquentRepo implements TicketRepoInterface {

    public function __construct() 
    {
        parent::__construct(Ticket::class);
    }

    public function getAvailable() 
    {
        $now = Helper::localTime();
        $now = $now->format('Y-m-d');

        return Ticket::where(function($query) use ($now) {
                $query->whereNull('available_from')
                    ->orWhere('available_from', '<=', $now);
            })
            ->where(function($query) use ($now) {
                $query->whereNull('available_until')
                    ->orWhere('available_until', '>=', $now);
            })
            ->with('boats', 'boatrooms', 'trips', 'basePrices', 'prices')
            ->get();    
    }

}
