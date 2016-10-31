<?php

use Scubawhere\Services\TicketService;
use Illuminate\Support\Facades\Response;
use Scubawhere\Exceptions\Http\HttpUnprocessableEntity;

class TicketController extends Controller {

    /** @var \Scubawhere\Services\TicketService */
    protected $ticket_service;

    /**
     * Response Object to create http responses
     *
     * @var \Illuminate\Support\Facades\Response
     */
    protected $response;

    public function __construct(TicketService $ticket_service, Response $response) {
        $this->ticket_service = $ticket_service;
        $this->response = $response;
    }

    /**
     * Get a single ticket by ID
     *
     * @api /api/ticket
     *
     * @throws \Scubawhere\Exceptions\Http\HttpUnprocessableEntity
     *
     * @return \Scubawhere\Entities\Ticket
     */
    public function getIndex() 
    {
        $id = Input::get('id');
        if(!$id) {
            throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, ['Please provide a ticket ID.']);
        }

        return $this->ticket_service->get($id);
    }

    /**
     * Get all tickets belonging to a company
     *
     * @api /api/ticket/all
     *
     * @return array Collection Ticket models
     */
    public function getAll()
    {
        return $this->ticket_service->getAll();
    }

    /**
     * Get all tickets belonging to a company including soft deleted models
     *
     * @api /api/ticket/all-with-trashed
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllWithTrashed()
    {
        return $this->ticket_service->getAllWithTrashed();
    }

    public function getOnlyAvailable()
    {
        return $this->ticket_service->getAvailable();
    }

    /**
     * Create a new ticket
     *
     * @api /api/ticket/add
     *
     * @throws \Scubawhere\Exceptions\Http\HttpUnprocessableEntity
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postAdd()
    {
        $data = Input::only('name', 'description', 'parent_id', 'available_from', 'available_until', 'available_for_from', 'available_for_until'); // Please NEVER use parent_id in the front-end!
        $data['only_packaged'] = Input::get('only_packaged', false);
        $trips = Input::get('trips', []);
        $base_prices = Input::get('base_prices', []);
        $prices = Input::get('prices', []);
        $boats = Input::get('boats', []);
        $boatrooms = Input::get('boatrooms', []);

        if( !is_array($trips) || empty($trips) ) {
            throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, ['Please specify at least one eligable trip.']);
        }
       
        $ticket = $this->ticket_service->create($data, $trips, $boats, $boatrooms, $base_prices, $prices);
        return $this->response->json( array('status' => 'Ticket created and connected OK', 'id' => $ticket->id, 'prices' => $ticket->prices()->get()), 201);
    }

    /**
     * Edit an existing ticket
     *
     * @api /api/ticket/edit
     *
     * @throws \Scubawhere\Exceptions\Http\HttpUnprocessableEntity
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postEdit()
    {
        $data = Input::only('name', 'description', 'parent_id', 'available_from', 'available_until', 'available_for_from', 'available_for_until'); // Please NEVER use parent_id in the front-end!
        $data['only_packaged'] = Input::get('only_packaged', false);
        $trips = Input::get('trips', []);
        $base_prices = Input::get('base_prices', []);
        $prices = Input::get('prices', []);
        $boats = Input::get('boats', []);
        $boatrooms = Input::get('boatrooms', []);
        $id = Input::get('id');

        if( !is_array($trips) || empty($trips) ) {
            throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, ['Please specify at least one eligable trip.']);
        }
       
        $ticket = $this->ticket_service->update($id, $data, $trips, $boats, $boatrooms, $base_prices, $prices);
        return $this->response->json(array('status' => 'OK. Ticket updated', 'base_prices' => $ticket->basePrices()->get(), 'prices' => $ticket->prices()->get()));
    }

    /**
     * Delete an ticket and remove it from any quotes or packages
     *
     * @api /api/ticket/delete
     *
     * @throws \Scubawhere\Exceptions\NotFoundException
     * @throws Exception
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postDelete()
    {
        $id = Input::get('id');
        if(!$id) {
            throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, ['Please provide a ticket ID.']);
        }
        
        $this->ticket_service->delete($id);
        return $this->response->json(array('status' => 'OK. Ticket deleted'), 200);
    }

}
