<?php

use Illuminate\Http\Request;
use Scubawhere\Services\AccommodationService;
use Scubawhere\Exceptions\Http\HttpUnprocessableEntity;

class ManifestController extends Controller
{
    protected $request;
    protected $accommodation_service;

    public function __construct(Request $request, AccommodationService $accommodation_service)
    {
        $this->request               = $request;
        $this->accommodation_service = $accommodation_service;
    }

    protected function getAccommodationManifest($id, $date)
    {
        $rules = array(
            'id'    => 'required|integer',
            'date'  => 'required|date'
        );

        $validator = Validator::make(['id' => $id, 'date' => $date], $rules);

        if($validator->fails()) {
            throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, $validator->errors()->all());
        }

        return Response::json(array(
            'status' => 'Sucess. Avaialability retrieved',
            'data'   => array(
                'date'          => $date,
                'accommodation' => $this->accommodation_service->getManifest(['after' => $date])
            )
        ));
    }

    protected function getTripManifest($dates)
    {

    }

    public function index()
    {
        $type  = $this->request->get('type');
        $id    = $this->request->get('id');
        $date = $this->request->get('date');

        switch ($type) {
            case 'accommodation':
                return $this->getAccommodationManifest($id, $date);
                break;
            case 'trip':
                return $this->getTripManifest($date);
                break;
            default:
                throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, ['The type field is required']);
        }
    }
}