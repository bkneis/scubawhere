<?php

use Illuminate\Support\Facades\Response;
use ScubaWhere\Services\TicketService;
use ScubaWhere\Exceptions\BadRequestException;
use ScubaWhere\Exceptions\NotFoundException;
use ScubaWhere\Exceptions\InvalidInputException;

class TicketController extends Controller {

    /**
     * Service to manage tickets
     * \ScubaWhere\Services\TicketService
     */
    protected $ticket_service;

    /**
     * Response Object to create http responses
     * @var \Illuminate\Support\Facades\Response
     */
    protected $response;

    /**
     * @param TicketService Injected using laravel's IOC container
     */
    public function __construct(TicketService $ticket_service, Response $response) {
        $this->ticket_service = $ticket_service;
        $this->response = $response;
    }

    /**
     * /api/ticket
     * Get a single ticket by ID
     * @throws \ScubaWhere\Exceptions\NotFoundException
     * @return json Ticket model
     */
    public function getIndex() 
    {
        $id = Input::get('id');
        //if(!$id) throw new InvalidInputException(['Please provide an ID.']);
        return $this->ticket_service->get($id);
    }

    /**
     * /api/ticket/all
     * Get all tickets belonging to a company
     * @return array Collection Ticket models
     */
    public function getAll()
    {
        return $this->ticket_service->getAll();
    }

    /**
     * /api/ticket/all-with-trashed
     * Get all tickets belonging to a company including soft deleted models
     * @return array Collection Ticket models
     */
    public function getAllWithTrashed()
    {
        return $this->ticket_service->getAllWithTrashed();
    }

    /**
     * /api/ticket/add
     * Create a new ticket
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Illuminate\Http\Response 201 Created with newly created ticket
     */
    public function postAdd()
    {
        $data = Input::only('name', 'description', 'parent_id', 'available_from', 'available_until', 'available_for_from', 'available_for_until'); // Please NEVER use parent_id in the front-end!
        $data['only_packaged'] = Input::get('only_packaged', false);
        $trips = Input::get('trips', []);
        $base_prices = Input::get('base_prices');
        $prices = Input::get('prices');
        $boats = Input::get('boats');
        $boatrooms = Input::get('boatrooms');

        if( !is_array($trips) || empty($trips) ) {
            throw new BadRequestException(['Please specify at least one eligable trip.']);
        }
       
        $ticket = $this->ticket_service->create($data, $trips, $boats, $boatrooms, $base_prices, $prices);
        return $this->response->json( array('status' => 'Ticket created and connected OK', 'id' => $ticket->id, 'prices' => $ticket->prices()->get()), 201); // 201 Created
    }

    /**
     * /api/ticket/edit
     * Edit an existing ticket
     * @throws \ScubaWhere\Exceptions\InvalidInputException
     * @return \Illuminate\Http\Response 200 Success with updated ticket
     */
    public function postEdit()
    {
        $data = Input::only('name', 'description', 'parent_id', 'available_from', 'available_until', 'available_for_from', 'available_for_until'); // Please NEVER use parent_id in the front-end!
        $data['only_packaged'] = Input::get('only_packaged', false);
        $trips = Input::get('trips', []);
        $base_prices = Input::get('base_prices');
        $prices = Input::get('prices');
        $boats = Input::get('boats');
        $boatrooms = Input::get('boatrooms');
        $id = Input::get('id');

        if( !is_array($trips) || empty($trips) ) {
            throw new BadRequestException(['Please specify at least one eligable trip.']);
        }
       
        $ticket = $this->ticket_service->update($id, $data, $trips, $boats, $boatrooms, $base_prices, $prices);
        return $this->response->json(array('status' => 'OK. Ticket updated', 'base_prices' => $ticket->basePrices()->get(), 'prices' => $ticket->prices()->get()));
    }

    /**
     * /api/ticket/delete
     * Delete an ticket and remove it from any quotes or packages
     * @throws \ScubaWhere\Exceptions\NotFoundException
     * @throws Exception
     * @return \Illuminate\Http\Response 200 Success
     */
    public function postDelete()
    {
        $id = Input::get('id');
        //if(!$id) throw new InvalidInputException(['Please provide an ID.']);
        
        $this->ticket_service->delete($id);
        return $this->response->json(array('status' => 'OK. Ticket deleted'), 200); // 200 Success
    }

}
