<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Scubawhere\Exceptions\Http\HttpNotFound;
use Scubawhere\Exceptions\Http\HttpUnprocessableEntity;

abstract class ApiController extends Controller
{
    /**
     * @var Request
     */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    
    public function validateInput(array $data, array $rules, array $messages = [])
    {
        $validator = Validator::make($data, $rules, $messages);

        if($validator->fails()) {
            throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, $validator->errors()->all());
        }
    }

    public function responseOK(array $data)
    {
        return new JsonResponse($data, 200);
    }

    public function responseCreated(array $data)
    {
        return new JsonResponse($data, 201);
    }

}