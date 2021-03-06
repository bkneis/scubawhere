<?php

use Scubawhere\Entities\Country;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CountryController extends Controller
{
    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => 'post'));
    }

    public function getIndex()
    {
        try {
            if (!Input::get('id')) {
                throw new ModelNotFoundException();
            }

            return Country::findOrFail(Input::get('id'));
        } catch (ModelNotFoundException $e) {
            return Response::json(array('errors' => array('The country could not be found.')), 404); // 404 Not Found
        }
    }

    public function getAll()
    {
        return Country::orderBy('name')->get();
    }
}
