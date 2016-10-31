<?php 

use Scubawhere\Services\LogService;

class LogController extends Controller
{
    protected $log_service;

    public function __construct(LogService $logging_service)
    {
        $this->log_service = $logging_service;
    }

    public function getIndex()
    {
        return $this->log_service->get(Input::get('id'));
    }

    public function getAll()
    {
        return $this->log_service->getAll();
    }

    public function postDelete()
    {
        return $this->log_service->delete(Input::get('id'));
    }
}
    

