<?php

use Scubawhere\Entities\Agency;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class AgencyController
 * 
 * Responsible for returning training agencies such as PADI, SSI etc.
 * that courses cna be associated to. An argument can be made to create 
 * and agencyRepo but due to time constraints and the fact that the rest
 * of the application has to rarely reference these it is not worth it.
 * 
 * @api /api/agency
 * @author Bryan Kneis
 * @version 1.0
 */
class AgencyController extends Controller
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

            return Agency::with('certificates')->findOrFail(Input::get('id'));
        } catch (ModelNotFoundException $e) {
            return Response::json(array('errors' => array('The agency could not be found.')), 404); // 404 Not Found
        }
    }

    public function getAll()
    {
        return Agency::with('certificates')->get();
    }
}
