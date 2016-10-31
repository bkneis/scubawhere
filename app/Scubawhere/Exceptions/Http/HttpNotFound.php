<?php

namespace Scubawhere\Exceptions\Http;

use Illuminate\Support\Facades\Response;

class HttpNotFound extends HttpBaseException
{
    protected $code = 404;

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