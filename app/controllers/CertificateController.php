<?php

class CertificateController extends Controller
{
    public function getIndex()
    {
        try {
            if (!Input::get('id')) {
                throw new ModelNotFoundException();
            }

            return Certifcate::findOrFail(Input::get('id'));
        } catch (ModelNotFoundException $e) {
            return Response::json(array('errors' => array('The certifcate could not be found.')), 404); // 404 Not Found
        }
    }

    public function getAll()
    {
        return Certificate::with('agency')->get();
    }
}
