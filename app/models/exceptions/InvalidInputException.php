<?php namespace ScubaWhere\Exceptions;

use ScubaWhere\Exceptions\BaseException;
use Illuminate\Support\Facades\Request;

class InvalidInputException extends BaseException
{
    protected $message;

    public function __construct($msg)
    {
        $this->message = $msg;
    }

    public function response()
    {
        return \Response::json(array('errors' => $this->message), 406);
    }
}
