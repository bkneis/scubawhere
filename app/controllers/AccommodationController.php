<?php

use Illuminate\Http\Request;
use Scubawhere\Exceptions\Http\HttpNotFound;
use Scubawhere\Services\AccommodationService;
use Scubawhere\Transformers\AccommodationTransformer;
use Scubawhere\Exceptions\Http\HttpUnprocessableEntity;

/**
 * Class AccommodationController
 */
class AccommodationController extends ApiController {

    /** @var AccommodationService */
    protected $accommodation_service;

    /** @var Request  */
    protected $request;
    
    /** @var AccommodationTransformer */
    protected $transformer;

    public function __construct(AccommodationService $accommodation_service,
                                AccommodationTransformer $accommodationTransformer,
                                Request $request
    )
    {
        $this->transformer = $accommodationTransformer;
        $this->accommodation_service = $accommodation_service;
        $this->request = $request;
        
        parent::__construct($request);
    }

    /**
     * Get a single accommodation by ID
     *
     * @api /api/accommodation
     * @param $id
     * @return \Scubawhere\Entities\Accommodation
     * @throws HttpUnprocessableEntity
     */
    public function show($id)
    {
        if (! $id) {
            throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, ['The ID field is required']);
        }
        
        return $this->responseOk(
            'Ok. Accommodations retrieved',
            array('data' => $this->transformer->transform(
                $this->accommodation_service->get($id)
            ))
        );
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
            return $this->transformer->transformMany($this->accommodation_service->getAllWithTrashed());
        }
        return $this->transformer->transformMany($this->accommodation_service->getAll());
    }

    /**
     * @todo move this to manifest controller and replace the getmanifest function in service object
     *
     * @return mixed
     * @throws HttpUnprocessableEntity
     */
    public function getAvailability()
    {
        $input = array(
            'after' => 'required|date',
            'before' => 'required|date'
        );
        $dates = $this->validate($input);
        
        return $this->responseOK(
            'Success. Availability retrieved',
            array('data' => $this->accommodation_service->getAvailability($dates))
        );
    }

    /**
     * Create a new accommodation
     *
     * @api /api/accommodation/add
     * @throws HttpUnprocessableEntity
     * @return \Illuminate\Http\JsonResponse
     */
    public function store()
    {
        $input = array(
            'name' => 'required',
            'description' => '',
            'capacity' => 'required',
            'parent_id' => '',
            'prices' => 'required'
        );
        $data = $this->validate($input);
        $accommodation = $this->accommodation_service->create($data);
        
        return $this->responseCreated(
            'OK. Accommodation created',
            $this->transformer->transform($accommodation->load('basePrices', 'prices'))
        );
    }

    /**
     * Edit an existing accommodation
     *
     * @api /api/accommodation/edit
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws HttpUnprocessableEntity
     */
    public function update($id)
    {
        $input = array(
            'name' => 'required',
            'description' => '',
            'capacity' => 'required',
            'parent_id' => '',
            'prices' => ''
        );
        $data = $this->validate($input);
        $accommodation = $this->accommodation_service->update($id, $data);

        return $this->responseOK(
            'OK. Accommodation updated',
            array('model' => $this->transformer->transform($accommodation->load('basePrices', 'prices')))
        );
    }

    /**
     * Delete an accommodation and remove it from any quotes or packages
     *
     * @api /api/accommodation
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws HttpUnprocessableEntity
     * @throws \Scubawhere\Exceptions\ConflictException
     */
    public function destroy($id)
    {
        if(!$id) {
            throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, ['The ID is a required field']);
        }
        $this->accommodation_service->delete($id);
        
        return $this->responseOK('OK. Accommodation deleted');
    }

}
