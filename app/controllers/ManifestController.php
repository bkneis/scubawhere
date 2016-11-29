<?php

use Illuminate\Http\Request;

use Scubawhere\Exceptions\Http\HttpUnprocessableEntity;

class ManifestController extends Controller
{

    protected $request;

    public function __constrcutor(Request $request)
    {
        $this->request = $request;
    }

    protected function getAccommodationManifest($dates)
    {
        $rules = array(
            'after'  => 'required|date',
            'before' => 'required|date'
        );

        $validator = Validator::make($dates, $rules);

        if($validator->fails()) {
            throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, $validator->errors()->all());
        }

        return Response::json(array(
            'status' => 'Sucess. Avaialability retrieved',
            'data'   => $this->accommodation_service->getAvailability($dates)
        ));
    }

    protected function getTripManifest($dates)
    {

    }

    public function index()
    {
        $type  = $this->request->only('type');
        $dates = $this->request->only('after', 'before');

        switch ($type) {
            case 'accommodation':
                return $this->getAccommodationManifest($dates);
                break;
            case 'trip':
                return $this->getTripManifest($dates);
                break;
            default:
                throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, ['The type field is required']);
        }
    }
}