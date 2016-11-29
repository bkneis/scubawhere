<?php 

namespace Scubawhere\Exceptions;

use Scubawhere\Exceptions\BaseException;
use Illuminate\Support\Facades\Request;

class HTTPPreconditionFailed extends BaseException {

	protected $errors;

	public function __construct(array $errors)
	{
        $this->errors = $errors;
    }

	public function response()
	{
        return \Response::json(array('errors' => $this->errors), 412); // 412 Precondition Failure
    }
}
