<?php 

namespace Scubawhere\Exceptions;

use Scubawhere\Exceptions\BaseException;
use Illuminate\Support\Facades\Request;

class InvalidInputException extends BaseException
{
	protected $errors;

    public function __construct($errors)
    {
        $this->errors = $errors;
    }

    public function response()
    {
        return \Response::json(array('errors' => $this->errors), 406);
    }
}
