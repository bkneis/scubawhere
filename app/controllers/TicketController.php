<?php

use Illuminate\Http\Request;
use Scubawhere\Services\TicketService;
use Scubawhere\Exceptions\Http\HttpUnprocessableEntity;

class TicketController extends ApiController {

    /** @var \Scubawhere\Services\TicketService */
    protected $ticket_service;

    public function __construct(TicketService $ticket_service, Request $request) {
        $this->ticket_service = $ticket_service;
        parent::__construct($request);
    }

    /**
     * Get a single ticket by ID
     *
     * @param $id
     * @api GET /api/ticket/{id}
     * @return \Scubawhere\Entities\Ticket
     * @throws HttpUnprocessableEntity
     */
    public function show($id) 
    {
        if(!$id) {
            throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, ['Please provide a ticket ID.']);
        }

        return $this->ticket_service->get($id);
    }

    /**
     * Get all tickets belonging to a company
     *
     * @api GET /api/ticket
     * @return array Collection Ticket models
     */
    public function index()
    {
        $with_deleted   = (bool) Input::get('with_deleted');
        $only_available = (bool) Input::get('only_available');
        
        if($with_deleted) {
            return $this->ticket_service->getAllWithTrashed();
        } elseif ($only_available) {
            return $this->ticket_service->getAvailable();
        }
        return $this->ticket_service->getAll();
    }

    /**
     * Create a new ticket
     *
     * @api POST /api/ticket
     * @throws \Scubawhere\Exceptions\Http\HttpUnprocessableEntity
     * @return \Illuminate\Http\JsonResponse
     */
    public function store()
    {
        $input = array(
            'name'                => 'required',
            'description'         => '',
            'parent_id'           => '',
            'available_from'      => 'date',
            'available_until'     => 'date',
            'available_for_from'  => 'date',
            'available_for_until' => 'date',
            'trips'               => 'required|array',
            'prices'              => 'required|array',
            'boats'               => 'array',
            'boatrooms'           => 'array'
        );
        
        $data = $this->validate($input);
        $data['only_packaged'] = Input::get('only_packaged', false);

        $ticket = $this->ticket_service->create($data);
        
        // @todo implment the commented out response and fix the front end to accept a ticket
        //return $this->responseCreated('OK. Ticket created.', $ticket);
        return Response::json( array('status' => 'Ticket created and connected OK', 'id' => $ticket->id, 'prices' => $ticket->prices()->get()), 201);
    }

    /**
     * Edit an existing ticket
     *
     * @param $id
     * @api GET /api/ticket/{id}
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     * @throws HttpUnprocessableEntity
     */
    public function update($id)
    {
        $input = array(
            'name'                => 'required',
            'description'         => '',
            'parent_id'           => '',
            'available_from'      => 'date',
            'available_until'     => 'date',
            'available_for_from'  => 'date',
            'available_for_until' => 'date',
            'trips'               => 'required|array',
            'prices'              => 'required|array',
            'boats'               => 'array',
            'boatrooms'           => 'array'
        );

        $data = $this->validate($input);
        $data['only_packaged'] = Input::get('only_packaged', false);
       
        $ticket = $this->ticket_service->update($id, $data);
        
        //return $this->responseOK('OK. Ticket updated.', array('data' => array('ticket' => $ticket)));
        return Response::json(array('status' => 'OK. Ticket updated', 'base_prices' => $ticket->basePrices()->get(), 'prices' => $ticket->prices()->get()));
    }

    /**
     * Delete an ticket and remove it from any quotes or packages
     *
     * @api DELETE /api/ticket/{id}
     * @throws \Scubawhere\Exceptions\NotFoundException
     * @throws Exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        if (!$id) {
            throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, ['Please provide a ticket ID.']);
        }
        
        $this->ticket_service->delete($id);
        return Response::json(array('status' => 'OK. Ticket deleted'), 200);
    }

}
