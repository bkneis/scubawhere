<?php

use Scubawhere\Services\AgentService;
use Scubawhere\Exceptions\NotFoundException;
use Scubawhere\Exceptions\InvalidInputException;

class AgentController extends Controller {

    /**
     * Service to manage agents
     * \Scubawhere\Services\AgentService
     */
    protected $agent_service;

    /**
     * @param AgentService Injected using laravel's IOC container
     */
    public function __construct(AgentService $agent_service) {
        $this->agent_service = $agent_service;
    }

    /**
     * Get a single agent by ID
     * 
     * @api /api/agent
     * @return json
     * @throws InvalidInputException
     */
    public function getIndex() 
    {
        $id = Input::get('id');
        if(!$id) throw new InvalidInputException(['Please provide an ID.']);
        return $this->agent_service->get($id);
    }

    /**
     * /api/agent/all
     * Get all agents belonging to a company
     * @return array Collection Agent models
     */
    public function getAll()
    {
        return $this->agent_service->getAll();
    }

    /**
     * /api/agent/all-with-trashed
     * Get all agents belonging to a company including soft deleted models
     * @return array Collection Agent models
     */
    public function getAllWithTrashed()
    {
        return $this->agent_service->getAllWithTrashed();
    }

    /**
     * /api/agent/add
     * Create a new agent
     * @throws \Scubawhere\Exceptions\InvalidInputException
     * @return \Illuminate\Http\Response 201 Created with newly created agent
     */
    public function postAdd()
    {
        $data = Input::only(
            'name',
            'website',
            'branch_name',
            'branch_address',
            'branch_phone',
            'branch_email',
            'billing_address',
            'billing_phone',
            'billing_email',
            'commission',
            'terms',
            'commission_rules'
        );
       
        $agent = $this->agent_service->create($data);
        return Response::json(array('status' => 'OK. Agent created', 'model' => $agent), 201); // 201 Created
    }

    /**
     * /api/agent/edit
     * Edit an existing agent
     * @throws \Scubawhere\Exceptions\InvalidInputException
     * @return \Illuminate\Http\Response 200 Success with updated agent
     */
    public function postEdit()
    {
        $id = Input::get('id');
        $data = Input::only(
            'name',
            'website',
            'branch_name',
            'branch_address',
            'branch_phone',
            'branch_email',
            'billing_address',
            'billing_phone',
            'billing_email',
            'commission',
            'terms'
        );

        $agent = $this->agent_service->update($id, $data);
        return Response::json(array('status' => 'OK. Agent updated', 'model' => $agent), 200); // 200 Success
    }

    /**
     * /api/agent/delete
     * Delete an agent and remove it from any quotes or packages
     * @throws \Scubawhere\Exceptions\NotFoundException
     * @throws Exception
     * @return \Illuminate\Http\Response 200 Success
     */
    public function postDelete()
    {
        $id = Input::get('id');
        if(!$id) throw new InvalidInputException(['Please provide an ID.']);
        $this->agent_service->delete($id);
        return Response::json(array('status' => 'OK. Agent deleted'), 200); // 200 Success
    }

}
