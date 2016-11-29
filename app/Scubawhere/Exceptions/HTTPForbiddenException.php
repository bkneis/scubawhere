<?php 

namespace Scubawhere\Exceptions;

use Scubawhere\Exceptions\BaseException;
use Illuminate\Support\Facades\Request;

class HTTPForbiddenException extends BaseException {

	protected $errors;

	public function __construct(array $errors) 
	{
        $this->errors = $errors; 
    }

	public function response() 
	{
        return \Response::json(array('errors' => $this->errors), 403); // 500 Server Error
    }
}
