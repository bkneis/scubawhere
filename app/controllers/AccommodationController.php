<?php

use Illuminate\Http\Request;
use Scubawhere\Exceptions\Http\HttpNotFound;
use Scubawhere\Services\AccommodationService;
use Scubawhere\Exceptions\Http\HttpUnprocessableEntity;

class AccommodationController extends Controller {

    /** @var AccommodationService */
    protected $accommodation_service;

    /** @var Request  */
    protected $request;

    public function __construct(AccommodationService $accommodation_service, Request $request)
    {
        $this->accommodation_service = $accommodation_service;
        $this->request               = $request;
    }

    /**
     * Get a single accommodation by ID
     *
     * @api /api/accommodation
     *
     * @param $id
     * @return \Scubawhere\Entities\Accommodation
     * @throws HttpNotFound
     */
    public function show($id)
    {
        $data = array(
            'id' => $id
        );

        $rules = array('id' => 'required');
        $messages = array('id.required' => 'The accommodation could not be found.');
        $validator = Validator::make($data, $rules, $messages);

        if($validator->fails()) {
            throw new HttpNotFound(__CLASS__.__METHOD__, $validator->errors()->all());
        }

        return $this->accommodation_service->get($data);
    }

    /**
     * Get all accommodations belonging to a company
     *
     * @todo Allow the with_deleted flag to be applied to filter aswell
     *
     * @api GET /api/accommodation
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws HttpNotFound
     */
    public function index()
    {
        $data = $this->request->only('after', 'before', 'accommodation_id');

        if(!(is_null($data['after']) && is_null($data['before']) && is_null($data['accommodation_id']))) {
            $rules = array(
                'after' => 'date|required_with:before',
                'before' => 'date',
                'accommodation_id' => 'integer|min:1'
            );
            $validator = Validator::make($data, $rules);

            if($validator->fails()) {
                throw new HttpNotFound(__CLASS__.__METHOD__, $validator->errors()->all());
            }

            return $this->accommodation_service->getFilter($data);
        }

        $with_trashed = (bool) $this->request->get('with_deleted');
        if($with_trashed) {
            return $this->accommodation_service->getAllWithTrashed();
        }
        return $this->accommodation_service->getAll();
    }

    /**
     * Get all accommodations belonging to a company
     * @api /api/accommodation/filter
     * @throws HttpNotFound
     * @return array Collection Accommodation models
     */
    /*public function getFilter()
    {
        $data = Input::only('after', 'before', 'accommodation_id');

        $rules = array(
            'after' => 'date|required_with:before',
            'before' => 'date',
            'accommodation_id' => 'integer|min:1'
        );
        $validator = Validator::make($data, $rules);

        if($validator->fails()) {
            throw new HttpNotFound(__CLASS__.__METHOD__, $validator->errors()->all());
        }

        return $this->accommodation_service->getFilter($data);
    }*/

    /**
     * @todo move this to manifest controller and replace the getmanifest function in service object
     *
     * @return mixed
     * @throws HttpUnprocessableEntity
     */
    public function getAvailability()
    {
        $dates = Input::only('after', 'before');

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

    /**
     * Create a new accommodation
     *
     * @api /api/accommodation/add
     *
     * @throws HttpUnprocessableEntity
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store()
    {
        $data = Input::only('name', 'description', 'capacity', 'parent_id'); // Please NEVER use parent_id in the front-end!

        $rules = array(
            'name'        => 'required',
            'capacity'    => 'required',
            'base_prices' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);

        if($validator->fails()) {
            throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, $validator->errors()->all());
        }

        $accommodation = $this->accommodation_service->create($data, Input::get('base_prices'), Input::get('prices'));

        return Response::json(array('status' => 'OK. Accommodation created', 'model' => $accommodation->load('basePrices', 'prices')), 201); // 201 Created
    }

    /**
     * Edit an existing accommodation
     *
     * @api /api/accommodation/edit
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws HttpUnprocessableEntity
     */
    public function update($id)
    {
        $data = Input::only('name', 'description', 'capacity', 'parent_id'); // Please NEVER use parent_id in the front-end!

        $rules = array(
            'name' => 'required',
            'capacity' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);

        if($validator->fails()) {
            throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, $validator->errors()->all());
        }

        $accommodation = $this->accommodation_service->update($id, $data, Input::get('base_prices'), Input::get('prices'));

        return Response::json(array('status' => 'OK. Accommodation updated', 'model' => $accommodation->load('basePrices', 'prices')), 200);
    }

    /**
     * Delete an accommodation and remove it from any quotes or packages
     *
     * @api /api/accommodation/delete
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws HttpNotFound
     * @throws \Scubawhere\Exceptions\ConflictException
     */
    public function destroy($id)
    {
        if(!$id) {
            throw new HttpNotFound(__CLASS__.__METHOD__, ['The Accommodation was not found']);
        }

        $this->accommodation_service->delete($id);

        return Response::json(array('status' => 'OK. Accommodation deleted'), 200); // 200 Success
    }

}
