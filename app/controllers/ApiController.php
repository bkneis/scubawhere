<?php

abstract class ApiController extends Controller
{
    protected $response;

    public function __construct(\Illuminate\Http\Response $response)
    {
        $this->response = $response;
    }

    public function trimData(array $data)
    {
        unset($data['company_id']);
        unset($data['updated_at']);
        unset($data['created_at']);
        if(isset($data['deleted_at'])) unset($data['deleted_at']);
        return $data;
    }

    abstract public function transform();

    public function transformCollection(\Illuminate\Database\Eloquent\Collection $collection)
    {
        return array_map([$this, 'transform'], $collection->toArray());
    }

    public function responseOK(array $data)
    {
        return $this->response->json($data, 200);
    }

}