<?php 

namespace Scubawhere\Repositories;

use Scubawhere\Helper;
use Scubawhere\Context;
use Scubawhere\Entities\Ticket;
use Scubawhere\Exceptions\Http\HttpNotFound;
use Scubawhere\Exceptions\Http\HttpBadRequest;
use Scubawhere\Exceptions\Http\HttpNotAcceptable;

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
class TicketRepo extends BaseRepo implements TicketRepoInterface {

    /** 
     * Eloquent model that acts as the root model to associate assets to
     *
     * @var \Scubawhere\Entities\Company
    */ 
    protected $company_model;

    public function __construct() {
        $this->company_model = Context::get();
    }

    /**
     * Get all tickets for a company
     *
     * @param array $relations
     *
     * @return \Illuminate\Database\Eloquent\Collection Eloquent collection with all tickets for a company
     */
    public function all(array $relations = []) {
        return Ticket::onlyOwners()->with($relations)->get();
    }

    /**
     * Get all tickets for a company including soft deleted models
     *
     * @param array $relations
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allWithTrashed(array $relations = []) {
        return Ticket::onlyOwners()->with($relations)->withTrashed()->get();
    }

    /**
     * Get an ticket for a company from its id
     *
     * @param int   $id
     * @param array $relations
     * @param bool  $fail
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotFound
     *
     * @return \Scubawhere\Entities\Ticket
     */
    public function get($id, array $relations = [], $fail = true) {
        $ticket = Ticket::onlyOwners()->with($relations)->find($id);

        if(is_null($ticket) && $fail) {
            throw new HttpNotFound(__CLASS__ . __METHOD__, ['The ticket could not be found']);
        }

        return $ticket;
    }

    /**
     * Get an ticket for a company by a specified column and value
     *
     * @param array $query
     * @param array $relations
     * @param bool  $fail
     *
     * @throws \Scubawhere\Exceptions\Http\HttpNotFound
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWhere(array $query, array $relations = [], $fail = true) {
        $ticket = Ticket::onlyOwners()->where($query)->with($relations)->find();

        if(is_null($ticket) && $fail) {
            throw new HttpNotFound(__CLASS__ . __METHOD__, ['The ticket could not be found']);
        }

        return $ticket;
    }

    public function getAvailable() 
    {
        $now = Helper::localTime();
        $now = $now->format('Y-m-d');

        return Ticket::onlyOwners()
            ->where(function($query) use ($now) {
                $query->whereNull('available_from')->orWhere('available_from', '<=', $now);
            })
            ->where(function($query) use ($now) {
                $query->whereNull('available_until')->orWhere('available_until', '>=', $now);
            })
            ->with('boats', 'boatrooms', 'trips', 'basePrices', 'prices')->get();    
    }

    /**
     * Associate any boats to the ticket if the ticket is limited to certain boats
     *
     * @param \Scubawhere\Entities\Ticket $ticket
     * @param array                       $boats
     *
     * @throws \Scubawhere\Exceptions\BadRequestException
     *
     * @return void 
     */
    private function associateBoats(Ticket $ticket, $boats)
    {
        if($boats && !empty($boats))
        {
            try {
                $ticket->boats()->sync($boats);
            } catch (\Exception $e) {
                throw new HttpBadRequest(__CLASS__.__METHOD__, ['Could not assign boats to the ticket, \'boats\' array is propably erroneous.']);
            }
        }
    }

    /**
     * Associate any tags to a trip
     *
     * @param Ticket $ticket
     * @param array  $boatrooms
     *
     * @throws \Scubawhere\Exceptions\BadRequestException
     *
     * @return void 
     */
    private function associateBoatrooms($ticket, $boatrooms)
    {
        if($boatrooms && !empty($boatrooms))
        {
            try {
                $ticket->boatrooms()->sync($boatrooms);
            } catch (\Exception $e) {
                throw new HttpBadRequest(__CLASS__.__METHOD__, ['Could not assign locations to trip, \'tags\' array is propably erroneous.']);
            }
        }
    }

    /**
     * Associate any trips to a ticket
     *
     * @param Ticket $ticket
     * @param array  $trips
     *
     * @throws \Scubawhere\Exceptions\BadRequestException
     *
     * @return void 
     */
    private function associateTrips(Ticket $ticket, $trips)
    {
        try {
            $ticket->trips()->sync($trips);
        } catch (\Exception $e) {
            throw new HttpBadRequest(__CLASS__.__METHOD__, ['Could not assign locations to trip, \'tags\' array is propably erroneous.']);
        }
    }

    /**
     * Create an ticket and associate it with its company
     *
     * @param array $data
     * @param array $trips
     * @param array $boats
     * @param array $boatrooms
     *
     * @throws \Scubawhere\Exceptions\InvalidInputException
     * @throws \Exception
     *
     * @return \Illuminate\Database\Eloquent\Model Eloquent model of an ticket for a company
     */
    public function create(array $data, array $trips, array $boats, array $boatrooms) {
        \DB::beginTransaction();
        try {
            $ticket = new Ticket($data);
            if (!$ticket->validate()) {
                throw new HttpNotAcceptable(__CLASS__.__METHOD__, $ticket->errors()->all());
            } 
            $this->company_model->tickets()->save($ticket);
            $this->associateTrips($ticket, $trips);
            $this->associateBoats($ticket, $boats);
            $this->associateBoatrooms($ticket, $boatrooms);
            \Db::commit();
        }
        catch(\Exception $e) {
            \DB::rollback();
            throw $e;
        }
        return $ticket;
    }

    /**
     * Update an ticket by id with specified data
     *
     * @param int   $id
     * @param array $data
     *
     * @throws \Scubawhere\Exceptions\InvalidInputException
     *
     * @return \Scubawhere\Entities\Ticket
     */
    public function update($id, array $data, array $trips, array $boats, array $boatrooms) {
        $ticket = $this->get($id);
        if(!$ticket->update($data)) {
            throw new HttpNotAcceptable(__CLASS__, __METHOD__, $ticket->errors()->all());
        }
        $this->company_model->tickets()->save($ticket);
        $this->associateTrips($ticket, $trips);
        $this->associateBoats($ticket, $boats);
        $this->associateBoatrooms($ticket, $boatrooms);
        return $ticket;
    }

}
