<?php

use Illuminate\Http\Request;
use Scubawhere\Exceptions\Http\HttpUnprocessableEntity;
use Scubawhere\Services\AgentService;
use Scubawhere\Exceptions\InvalidInputException;
use Scubawhere\Transformers\AgentTransformer;

class AgentController extends ApiController {

    /**
     * Service to manage agents
     * @var \Scubawhere\Services\AgentService
     */
    protected $agent_service;
    
    /* @var AgentTransformer*/
    protected $transformer;

    /**
     * @todo SCUBA-688 Fix the agent transformer service provider
     * @param AgentService $agent_service
     * @param Request $request
     */
    public function __construct(AgentService $agent_service, Request $request) {
        $this->agent_service = $agent_service;
        $this->transformer = new AgentTransformer();
        parent::__construct($request);
    }

    /**
     * Get a single agent by ID
     *
     * @api /api/agent
     * @return json
     * @throws HttpUnprocessableEntity
     */
    public function getIndex() 
    {
        $id = $this->request->get('id');
        if(! $id) {
            throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, ['Please provide an ID.']);
        }
        
        return $this->responseOK(
            $this->transformer->transform(
                $this->agent_service->get($id)
            )
        );
    }

    /**
     * Get all agents belonging to a company
     * 
     * @api /api/agent/all
     * @return array Collection Agent models
     */
    public function getAll()
    {
        return $this->transformer->transformMany($this->agent_service->getAll());
    }

    /**
     * Get all agents belonging to a company including soft deleted models
     * 
     * @api /api/agent/all-with-trashed
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllWithTrashed()
    {
        return $this->agent_service->getAllWithTrashed();
    }

    /**
     * Create a new agent
     * 
     * @api /api/agent/add
     * @throws \Scubawhere\Exceptions\InvalidInputException
     * @return \Illuminate\Http\Response
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
        return $this->responseCreated('OK. Agent created', $agent);
        //return Response::json(array('status' => 'OK. Agent created', 'model' => $agent), 201); // 201 Created
    }

    /**
     * Edit an existing agent
     * 
     * @api /api/agent/edit
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
            'terms',
            'commission_rules'
        );

        $agent = $this->agent_service->update($id, $data);
        return $this->responseOK('OK. Agent updated.', array('model' => $agent));
        //return Response::json(array('status' => 'OK. Agent updated', 'model' => $agent), 200); // 200 Success
    }

    /**
     * Delete an agent and remove it from any quotes or packages
     * 
     * @api /api/agent/delete
     * @throws \Scubawhere\Exceptions\NotFoundException
     * @throws Exception
     * @return \Illuminate\Http\Response 200 Success
     */
    public function postDelete()
    {
        $id = Input::get('id');
        if(!$id) {
            throw new InvalidInputException(['Please provide an ID.']);
        }
        $this->agent_service->delete($id);
        
        return $this->responseOK('OK. Agent deleted');
        //return Response::json(array('status' => 'OK. Agent deleted'), 200); // 200 Success
    }

}
