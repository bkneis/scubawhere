<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use ScubaWhere\Context;

class AgentController extends Controller
{
    public function getIndex()
    {
        try {
            if (!Input::get('id')) {
                throw new ModelNotFoundException();
            }

            return Context::get()->agents()->findOrFail(Input::get('id'));
        } catch (ModelNotFoundException $e) {
            return Response::json(array('errors' => array('The agent could not be found.')), 404); // 404 Not Found
        }
    }

    public function getAll()
    {
        return Context::get()->agents()->get();
    }

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
            'terms'
        );

        $agent = new Agent($data);

        if (!$agent->validate()) {
            return Response::json(array('errors' => $agent->errors()->all()), 406); // 406 Not Acceptable
        }

        $agent = Context::get()->agents()->save($agent);

        return Response::json(array('status' => 'OK. Agent created', 'id' => $agent->id), 201); // 201 Created
    }

    public function postEdit()
    {
        try {
            if (!Input::get('id')) {
                throw new ModelNotFoundException();
            }
            $agent = Context::get()->agents()->findOrFail(Input::get('id'));
        } catch (ModelNotFoundException $e) {
            return Response::json(array('errors' => array('The agent could not be found.')), 404); // 404 Not Found
        }

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

        if (!$agent->update($data)) {
            return Response::json(array('errors' => $agent->errors()->all()), 406); // 406 Not Acceptable
        }

        return Response::json(array('status' => 'OK. Agent updated.'), 200); // 200 OK
    }

    public function postDelete()
    {
        try {
            if(!Input::get('id'))
                throw new ModelNotFoundException();
            $agent = Context::get()->agents()->findOrFail(Input::get('id'));
            $agent->delete();
        } catch (ModelNotFoundException $e) {
            return Response::json(array('errors' => array('The agent could not be found.')), 404); // 404 Not Found
        }
    }
}
