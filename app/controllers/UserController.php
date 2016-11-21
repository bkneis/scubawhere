<?php

use Scubawhere\Repositories\UserRepo;
use Scubawhere\Exceptions\Http\HttpUnauthorized;
use Scubawhere\Exceptions\Http\HttpUnprocessableEntity;

class UserController extends Controller
{
    protected $user_repo;

    public function __construct(UserRepo $user_repo)
    {
        $this->user_repo = $user_repo;
    }

    /**
     * Get a list of names and ids of the companies the user is authorized to use.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCompanies()
    {
        $user = Auth::user();
        $companies = $user->companies()->select('id', 'name')->get();
        $active_id = \Cache::get($user->getActiveCompanyKey());
        if (is_null($active_id)) {
            $company = $user->company;
            $company->active = true;
            return array($company);
        }
        foreach($companies as $company) {
            if($company->id === (int) $active_id) {
                $company->active = true;
            }
        }
        return $companies;
    }

    public function getActiveCompany()
    {
        return Auth::user()->getActiveCompany();
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

    /**
     * Create a user and attach them to the current context's company
     *
     * @api POST /user
     * @throws HttpUnprocessableEntity
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $data = Input::only(
            'username',
            'email'
        );
        $password = Input::get('password');

        $user = $this->user_repo->create($data, $password);

        return Response::json(array(
            'status' => 'OK. User created successfully',
            'data' => array('user' => $user)
        , 200));

    }
}