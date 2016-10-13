<?php 

namespace ScubaWhere\Exceptions;

use ScubaWhere\Exceptions\BaseException;
use Illuminate\Support\Facades\Request;

class InternalErrorException extends BaseException {

	protected $errors;

    public function __construct($errors) {
        $this->errors = ['Uh oh, we apologise. It seems like their is a failure somewhere. If this problem persists, please contact us at support@scubawhere.com'];
    }

    public function response() {
        return \Response::json(array('errors' => $this->errors), 500); // 500 Server Error
    }
}
