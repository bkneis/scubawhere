<?php

namespace Scubawhere\Exceptions\Http;

use Illuminate\Support\Facades\Response;

class HttpNotAcceptable extends HttpBaseException
{
    protected $code = 406;

    protected $errors;

    public function __construct($id, array $errors)
    {
        parent::__construct($this->code, $id);
        $this->errors = $errors;
    }

    public function response()
    {
        return Response::json(array('errors' => $this->errors), $this->code);
    }
}