<?php 

namespace ScubaWhere\Repositories;

use ScubaWhere\Context;
use ScubaWhere\Exceptions;
use ScubaWhere\Exceptions\InvalidInputException;
use ScubaWhere\Repositories\TicketRepoInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TicketRepo extends BaseRepo implements TicketRepoInterface {

    /** 
     * Eloquent model that acts as the root model to associate assets to
     * @var \Company 
    */ 
    protected $company_model;

    public function __construct() {
        $this->company_model = Context::get();
    }

    /**
     * Get all tickets for a company
     * @return \Illuminate\Database\Eloquent\Collection Eloquent collection with all tickets for a company
     */
    public function all() {
        return \Ticket::onlyOwners()->with('boats', 'boatrooms', 'trips', 'basePrices', 'prices')->get();
    }

    /**
     * Get all tickets for a company including soft deleted models
     * @return \Illuminate\Database\Eloquent\Collection Eloquent collection with all tickets for a company including soft deleted models
     */
    public function allWithTrashed() {
        return \Ticket::onlyOwners()->withTrashed()->with('boats', 'boatrooms', 'trips', 'basePrices', 'prices')->get();
    }

    /**
     * Get an ticket for a company from its id
     * @param  int   ID of the ticket
     * @throws \Illuminate\Database\Eloquent\ModelNotFound
     * @return \Illuminate\Database\Eloquent\Model Eloquent model of an ticket for a company
     */
    public function get($id) {
        return \Ticket::onlyOwners()->with('boats', 'boatrooms', 'trips', 'basePrices', 'prices')->findOrFail($id);
    }

    /**
     * Get an ticket for a company by a specified column and value
     * @param  string Column name to search by
     * @param  mixed  Value to match the ticket
     * @return \Illuminate\Database\Eloquent\Model Eloquent model of an ticket for a company
     */
    public function getWhere($column, $value) {
        return \Ticket::onlyOwners()->where($column, $value)->with('boats', 'boatrooms', 'trips', 'basePrices', 'prices')->get();
    }

    /**
     * Get an ticket for a company with specified relationships
     * @param  int    ID of the ticket
     * @param  array  Relationships to retrieve with the model
     * @return \Ticket 
     */
    public function getWith($id, $relations) {
        return \Ticket::onlyOwners()->with($relations)->findOrFail($id);
    }

    public function getAvailable() 
    {
        $now = Helper::localTime();
        $now = $now->format('Y-m-d');

        return \Ticket::onlyOwners()
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
     * @param  \Ticket Model to associate boats to
     * @throws \ScubaWhere\Exceptions\BadRequestException 
     * @return void 
     */
    private function associateBoats($ticket, $boats)
    {
        if($boats && !empty($boats))
        {
            try {
                $ticket->boats()->sync($boats);
            } catch (Exception $e) {
                throw new BadRequestException(['Could not assign boats to the ticket, \'boats\' array is propably erroneous.']);
            }
        }
    }

    /**
     * Associate any tags to a trip
     * @param  \Trip Model to associate tags to
     * @throws \ScubaWhere\Exceptions\BadRequestException 
     * @return void 
     */
    private function associateBoatrooms($ticket, $boatrooms)
    {
        if($boatrooms && !empty($boatrooms))
        {
            try {
                $trip->boatrooms()->sync($tags);
            } catch (Exeption $e) {
                throw new BadRequestException(['Could not assign locations to trip, \'tags\' array is propably erroneous.']);
            }
        }
    }

    /**
     * Associate any trips to a ticket
     * @param  \Ticket Model to associate tags to
     * @param  array   Trip ids to associate to the ticket
     * @throws \ScubaWhere\Exceptions\BadRequestException 
     * @return void 
     */
    private function associateTrips($ticket, $trips)
    {
        try {
            $ticket->trips()->sync($trips);
        } catch (Exeption $e) {
            throw new BadRequestException(['Could not assign locations to trip, \'tags\' array is propably erroneous.']);
        }
    }

    /**
     * Create an ticket and associate it with its company
     * @param array Information about the ticket to save
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Illuminate\Database\Eloquent\Model Eloquent model of an ticket for a company
     */
    public function create($data, $trips, $boats, $boatrooms) {
        \DB::beginTransaction();
        try {
            $ticket = new \Ticket($data);
            if (!$ticket->validate()) {
                throw new InvalidInputException($ticket->errors()->all());
            } 
            $this->company_model->tickets()->save($ticket);
            $this->associateTrips($ticket, $trips);
            $this->associateBoats($ticket, $boats);
            $this->associateBoatrooms($ticket, $boatrooms);
            \Db::commit();
        }
        catch(Exception $e) {
            \DB::rollback();
            throw $e;
        }
        return $ticket;
    }

    /**
     * Update an ticket by id with specified data
     * @param  int   ID of the ticket
     * @param  array Data to update the ticket with
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Illuminate\Database\Eloquent\Model Eloquent model of an ticket for a company
     */
    public function update($id, $data, $trips, $boats, $boatrooms) {
        /*\DB::beginTransaction();
        try {
            $ticket = $this->get($id);
            if(!$ticket->update($data)) {
                throw new InvalidInputException($ticket->errors()->all());
            }
            $this->company_model->tickets()->save($ticket);
            $this->associateTrips($ticket, $trips);
            $this->associateBoats($ticket, $boats);
            $this->associateBoatrooms($ticket, $boatrooms);
            \Db::commit();
        }
        catch(Exception $e) {
            \DB::rollback();
            throw $e;
        }*/
        $ticket = $this->get($id);
        if(!$ticket->update($data)) {
            throw new InvalidInputException($ticket->errors()->all());
        }
        $this->company_model->tickets()->save($ticket);
        $this->associateTrips($ticket, $trips);
        $this->associateBoats($ticket, $boats);
        $this->associateBoatrooms($ticket, $boatrooms);
        return $ticket;
    }

    /**
     * Delete an ticket by its id
     * @param  int ID of the ticket
     * @throws Exception
     */
    public function delete($id) {
        $ticket = $this->get($id);
        $ticket->delete();
    }

    /**
     * Delete an ticket by a specified column and value
     * @param  string Column name to search by
     * @param  mixed  Value to match the ticket
     * @throws Exception
     */
    public function deleteWhere($column, $value) {
        $ticket = $this->getWhere($column, $value);
        $ticket->delete();
    }
}