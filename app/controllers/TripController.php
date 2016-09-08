<?php

use ScubaWhere\Helper;
use ScubaWhere\Context;
use ScubaWhere\Services\LogService;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TripController extends Controller {

    protected $log_service;

    public function __construct(LogService $log_service)
    {
        $this->log_service = $log_service;
    }
    
    public function getIndex()
    {
        try {
            if (!Input::get('id')) {
                throw new ModelNotFoundException();
            }

            return Context::get()->trips()->withTrashed()->with(
                array(
                    'locations',
                    'tags',
                    'tickets',
                )
            )->findOrFail(Input::get('id'));
        } catch (ModelNotFoundException $e) {
            return Response::json(array('errors' => array('The trip could not be found.')), 404); // 404 Not Found
        }
    }

    public function getAll()
    {
        return Context::get()->trips()->with(
            array(
                'locations',
                'tags',
            )
        )->get();
    }

    public function getAllWithTrashed()
    {
        return Context::get()->trips()->withTrashed()->with(
            array(
                'locations',
                'tags',
            )
        )->get();
    }

    public function getTags()
    {
        return Tag::where('for_type', 'Trip')->orderBy('name')->get();
    }

    public function postAdd()
    {
        $data = Input::only('name', 'description', 'duration');

        // Check optional fields
        if (Input::has('boat_required')) {
            $data['boat_required'] = Input::get('boat_required'); // If not present in input array, defaults to TRUE
        }
        if (Input::has('photo')) {
            $data['photo'] = Input::get('photo');
        }
        if (Input::has('video')) {
            $data['video'] = Input::get('video');
        }

        // Validate that locations and tags are provided
        $locations = Input::get('locations');
        $tags = Input::get('tags');
        if (!$locations || empty($locations) || !is_numeric($locations[0])) {
            return Response::json(array('errors' => array('At least one location is required.')), 406); // 406 Not Acceptable
        }
        if (!$tags || empty($tags) || !is_numeric($tags[0])) {
            return Response::json(array('errors' => array('At least one tag is required.')), 406); // 406 Not Acceptable
        }

        $trip = new Trip($data);

        if (!$trip->validate()) {
            // The validator failed
            return Response::json(array('errors' => $trip->errors()->all()), 406); // 406 Not Acceptable
        }

        // Input has been validated, save the model
        $trip = Context::get()->trips()->save($trip);

        // Trip has been created, let's connect it
        // Connect locations
        try {
            $trip->locations()->sync($locations);
        } catch (Exception $e) {
            return Response::json(array('errors' => array('Could not assign locations to trip, \'locations\' array is propably erroneous.')), 400); // 400 Bad Request
        }

        // Connect tags
        try {
            $trip->tags()->sync($tags);
        } catch (Exeption $e) {
            return Response::json(array('errors' => array('Could not assign tags to trip, \'tags\' array is propably erroneous.')), 400); // 400 Bad Request
        }

        // When no problems occur, we return a success response
        return Response::json(array('status' => 'OK. Trip created', 'id' => $trip->id), 201); // 201 Created
    }

    public function postEdit()
    {
        $data = Input::only('name', 'description', 'duration');

        // Check optional fields
        if (Input::has('photo')) {
            $data['photo'] = Input::get('photo');
        }
        if (Input::has('video')) {
            $data['video'] = Input::get('video');
        }

        try {
            if (!Input::get('id')) {
                throw new ModelNotFoundException();
            }
            $trip = Context::get()->trips()->findOrFail(Input::get('id'));
        } catch (ModelNotFoundException $e) {
            return Response::json(array('errors' => array('Can\'t find the trip with the submitted ID!')), 404); // 404 Not Found
        }

        // Validate that locations and tags are provided
        $locations = Input::get('locations');
        $tags = Input::get('tags');
        if (!$locations || empty($locations) || !is_numeric($locations[0])) {
            return Response::json(array('errors' => array('At least one location is required.')), 406); // 406 Not Acceptable
        }
        if (!$tags || empty($tags) || !is_numeric($tags[0])) {
            return Response::json(array('errors' => array('At least one tag is required.')), 406); // 406 Not Acceptable
        }

        if (!$trip->update($data)) {
            // When validation fails
            return Response::json(array('errors' => $trip->errors()->all()), 406); // 406 Not Acceptable
        }

        // Trip has been updated, let's reconnect it

        // Connect locations
        if (!$trip->locations()->sync($locations)) {
            return Response::json(array('errors' => array('Could not assign locations to trip, \'locations\' array is propably erroneous.')), 400); // 400 Bad Request
        }

        // Connect tags
        if (!$trip->tags()->sync($tags)) {
            return Response::json(array('errors' => array('Could not assign tags to trip, \'tags\' array is propably erroneous.')), 400); // 400 Bad Request
        }

        // When no problems occur, we return a success response
        return Response::json(array('status' => 'OK. Trip updated'), 200); // 200 OK
    }

    /*public function postDeactivate()
    {

        try
        {
            if( !Input::get('id') ) throw new ModelNotFoundException();
            $trip = Context::get()->trips()->findOrFail( Input::get('id') );
        }
        catch(ModelNotFoundException $e)
        {
            return Response::json( array('errors' => array('The trip could not be found.')), 404 ); // 404 Not Found
        }

        $trip->delete();

        return array('status' => 'OK. Trip deactivated');
    }

    public function postRestore()
    {
        try
        {
            if( !Input::get('id') ) throw new ModelNotFoundException();
            $trip = Context::get()->trips()->onlyTrashed()->findOrFail( Input::get('id') );
        }
        catch(ModelNotFoundException $e)
        {
            return Response::json( array('errors' => array('The trip could not be found.')), 404 ); // 404 Not Found
        }

        $trip->restore();

        return array('status' => 'OK. Trip restored');
    }*/

    public function postDelete()
    {
        try 
        {
            if (!Input::get('id')) throw new ModelNotFoundException();
            $trip = Context::get()->trips()
								  ->with(['tickets',
								  'departures' => function($query) {
								      return $query->where('start', '>=', Helper::localTime()->format('Y-m-d H:i:s'));
								  }])
                                  ->findOrFail(Input::get('id'));
        } 
        catch (ModelNotFoundException $e) 
        {
            return Response::json(
                        array('errors' => 
                            array('The trip could not be found.')
                        ), 404); // 404 Not Found
        }

        $problem_tickets = array();

        // Check for any tickets that require the trip (the tickets only trip)
        foreach($trip->tickets as $obj) 
        {
            $ticket = Context::get()->tickets()
                                         ->with('trips')
                                         ->where('id', '=', $obj->id)
                                         ->first();
            if(sizeof($ticket->trips) == 1)
            {
                array_push($problem_tickets, $obj);
            }
        }
        // Check for any problems, if so log them and return 409 error
        if(sizeof($problem_tickets) > 0)
        {
            $logger = $this->log_service->create('Attempting to delete the trip ' . $trip->name);
            foreach($problem_tickets as $prob) 
            {
                $logger->append('The ticket ' . $prob->name . ' uses soley this trip, please assign the ticket a diffrent trip to delete');
            }    
            return Response::json(
                        array('errors' =>
                            array('The trip could not be deleted as it has tickets that require it, for more information on how to delete it, visit the error logs tab')
                        ), 409);
        }
        // Check if the trip is scheduled for future departures
        elseif(sizeof($trip->departures) > 0)
        {
            $logger = $this->log_service->create('Attempting to delete the trip ' . $trip->name);
            foreach($trip->departures as $obj) 
            {
                $logger->append('The trip is scheduled to depart on ' . $obj->start . ', please delete the departure in scheduleing or edit it to use a diffrent trip');
            }
            return Response::json(
                        array('errors' =>
                            array('The trip could not be deleted as it is scheduled for departure in the future, for more information on how to delete it, visit the error logs tab')
                        ), 409);
        }
        // If no problems, unassign the trips to the tickets
        else 
        {
            foreach($trip->tickets as $obj) 
            {
                DB::table('ticket_trip')
                    ->where('trip_id', $trip->id)
                    ->where('ticket_id', $obj->id)
                    ->update(array('deleted_at' => DB::raw('NOW()'))); 
            }
        }

        $trip->delete();

        return array('status' => 'Ok. Trip deleted');
    }

    /*
     *
    public function postDelete()
    {
        try {
            if (!Input::get('id')) {
                throw new ModelNotFoundException();
            }
            $trip = Context::get()->trips()->findOrFail(Input::get('id'));
        } catch (ModelNotFoundException $e) {
            return Response::json(array('errors' => array('The trip could not be found.')), 404); // 404 Not Found
        }

        try {
            $trip->forceDelete();
        } catch (QueryException $e) {
            return Response::json(array('errors' => array('The trip can not be removed currently because it has tickets or active trips assigned to it.')), 409); // 409 Conflict
        }

        return array('status' => 'Ok. Trip deleted');
    }
     */
}
