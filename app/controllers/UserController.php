<?php

use Scubawhere\Exceptions\Http\HttpUnauthorized;
use Scubawhere\Exceptions\Http\HttpUnprocessableEntity;

class UserController extends Controller
{
    /**
     * Get a list of names and ids of the companies the user is authorized to use.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCompanies()
    {
        return Auth::user()->companies()->select('id', 'name')->get();
    }

    /**
     * Check that the user is authorized to use the requested company.
     *
     * @param $id
     * @return bool
     */
    protected function isAuthorizedToCompany($id)
    {
        $companies = Auth::user()->companies()->get(['id']);
        $auth = false;
        foreach($companies as $company) {
            if($company->id === (int) $id) {
                $auth = true;
            }
        }
        return $auth;
    }

    /**
     * Switch the context of the user to a specified company.
     *
     * First, check that the user is authorized to switch to the specified company.
     * If so, then set the active company (a key in the cache with the value being the company id)
     * that will be used to set the context on every request via the App::matched in filters.php
     *
     * @api POST /user/switch-company
     * @return \Illuminate\Http\Response
     * @throws HttpUnauthorized
     * @throws HttpUnprocessableEntity
     */
    public function postSwitchCompany()
    {
        $id = Input::get('company_id');

        if(is_null($id)) {
            throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, ['The company_id field is required.']);
        }

        if(!$this->isAuthorizedToCompany($id)) {
            throw new HttpUnauthorized(__CLASS__.__METHOD__, ['You are not authorized to use this company.']);
        }

        Auth::user()->setActiveCompany($id);

        return Response::json(array('status' => 'Ok. The company has been switched'), 200);
    }
}