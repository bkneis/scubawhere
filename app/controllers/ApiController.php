<?php

use Illuminate\Http\Request;
use Illuminate\Http\Response;

abstract class ApiController extends Controller
{
    /* @var Response */
    protected $response;

    /* @var Request */
    protected $request;

    public function __construct(Response $response, Request $request)
    {
        $this->response = $response;
        $this->request  = $request;
    }

    public function validateInput(array $data, array $rules, array $messages = [])
    {
        $validator = Validator::make($data, $rules, $messages);

        if($validator->fails()) {
            throw new HttpNotFound(__CLASS__.__METHOD__, $validator->errors()->all());
        }
    }

    public function responseOK(array $data)
    {
        return $this->response->json($data, 200);
    }

}