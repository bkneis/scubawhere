<?php 

namespace ScubaWhere\Exceptions;

use ScubaWhere\Exceptions\BaseException;
use Illuminate\Support\Facades\Request;

class NotFoundException extends BaseException {

	protected $errors;

    public function __construct($errors) {
        $this->errors = $errors;
    }

    public function response() {
        return \Response::json(array('errors' => $this->errors), 404);
    }
}
