<?php

namespace Scubawhere\Exceptions\Http;

use Illuminate\Support\Facades\Response;

class HttpUnauthorized extends HttpBaseException
{
    protected $code = 401;

    protected $errors;

    public function __construct($id, array $errors)
    {
        parent::__construct($this->code, $id);
        $this->errors = $errors;
    }

    public function response()
    {
        return Response::json($this->errors, $this->code);
    }
}