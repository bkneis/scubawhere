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
    
    /*public function validateInput(array $data, array $rules, array $messages = [])
    {
        $validator = Validator::make($data, $rules, $messages);

        if($validator->fails()) {
            throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, $validator->errors()->all());
        }
    }*/

    public function validateInput(array $data, array $messages = [])
    {
        $input = Input::only(array_keys($data));
        
        $validator = Validator::make($input, $data, $messages);
        
        if ($validator->fails()) {
            throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, $validator->errors()->all());
        }
        
        return $input;
    }

    /**
     * @param $status
     * @param array $data
     * @return JsonResponse
     */
    public function responseOK($status, array $data = [])
    {
        $res = array('status' => $status) + $data;
        return new JsonResponse($res, 200);
    }

    /**
     * @param $status
     * @param $model
     * @return JsonResponse
     */
    public function responseCreated($status, $model)
    {
        return new JsonResponse(array('status' => $status, 'model' => $model), 201);
    }

}