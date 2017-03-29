<?php

use Illuminate\Http\Request;
use Scubawhere\Services\AgentService;
use Scubawhere\Exceptions\InvalidInputException;
use Scubawhere\Transformers\AgentTransformer;
use Scubawhere\Exceptions\Http\HttpUnprocessableEntity;

/**
 * Class AgentController
 * 
 * Responsible for managing travel agents of a user. Only CRUD ops are really needed
 * for this end point as any logic to do with agents are within the booking itself
 * or reporting.
 * 
 * @todo Implement RESTful design to the api end points (i.e. Use PUT/DELETE etc with url params, GET /api/agent/1)
 * @api /api/agent
 * @version 1.1.0
 * @author Bryan Kneis
 */
class AgentController extends ApiController {

    /** @var \Scubawhere\Services\AgentService */
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
     * @return \Illuminate\Http\JsonResponse
     * @throws HttpUnprocessableEntity
     */
    public function getIndex() 
    {
        $id = $this->request->get('id');
        if(! $id) {
            throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, ['Please provide an ID.']);
        }
        
        return $this->responseOK(
            'Ok. Agent retrieved',
            array('data' => $this->transformer->transform(
                $this->agent_service->get($id)
            ))
        );
    }

    /**
     * Get all agents belonging to a company
     * 
     * @api /api/agent/all
     * @return \Illuminate\Http\JsonResponse 
     */
    public function getAll()
    {
        return $this->responseOK(
            'Ok. All agents retrieved.',
            array('data' => $this->transformer->transformMany(
                $this->agent_service->getAll()
            ))
        );
    }

    /**
     * Get all agents belonging to a company including soft deleted models
     * 
     * @api /api/agent/all-with-trashed
     * @return \Illuminate\Http\JsonResponse 
     */
    public function getAllWithTrashed()
    {
        return $this->responseOK(
            'Ok. All agents retrieved.',
            array('data' => $this->transformer->transformMany(
                $this->agent_service->getAllWithTrashed()
            ))
        );
    }

    /**
     * Create a new agent
     * 
     * @api /api/agent/add
     * @throws \Scubawhere\Exceptions\InvalidInputException
     * @return \Illuminate\Http\JsonResponse 
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
    }

    /**
     * Edit an existing agent
     * 
     * @api /api/agent/edit
     * @throws \Scubawhere\Exceptions\InvalidInputException
     * @return \Illuminate\Http\JsonResponse 
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
    }

    /**
     * Delete an agent and remove it from any quotes or packages
     * 
     * @api /api/agent/delete
     * @throws \Scubawhere\Exceptions\NotFoundException
     * @throws Exception
     * @return \Illuminate\Http\JsonResponse 
     */
    public function postDelete()
    {
        $id = Input::get('id');
        if(!$id) {
            throw new InvalidInputException(['Please provide an ID.']);
        }
        $this->agent_service->delete($id);
        
        return $this->responseOK('OK. Agent deleted');
    }

}
