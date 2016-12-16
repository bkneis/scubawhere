<?php

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Validator;
use Scubawhere\Exceptions\Http\HttpNotFound;

abstract class ApiController extends Controller
{
    /* @var Response */
    protected $response;

    /* @var Request */
    protected $request;

    public function __construct(Response $response, Request $request, Validator $validator)
    {
        $this->response  = $response;
        $this->request   = $request;
        $this->validator = $validator;
    }

    public function validateInput(array $data, array $rules, array $messages = [])
    {
        $validator = $this->validator->make($data, $rules, $messages);

        if($validator->fails()) {
            throw new HttpNotFound(__CLASS__.__METHOD__, $validator->errors()->all());
        }
    }

    public function responseOK(array $data)
    {
        return $this->response->json($data, 200);
    }

}