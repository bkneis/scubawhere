<?php
namespace ScubaWhere\Exceptions;

use Exception;
use Illuminate\Http\Response;

class InputNotValidException extends Exception
{
    protected $errors;

    public function __construct($errors)
    {
        $this->errors = $errors;
    }

    public function response()
    {
        return Response::json(406, array('errors' => $this->errors->all()));
    }
}
