<?php

namespace Scubawhere\Exceptions\Http;

use Illuminate\Support\Facades\Response;

class HttpInternalServerError extends HttpBaseException
{
    protected $code = 500;

    public function __construct($id)
    {
        parent::__construct($this->code, $id);
    }

    public function response()
    {
        return Response::json(
            ['Oh uh, their seems to be an un balance in the force, causing our server to'.
                'error. If this problem persists please contact us at support@scubawhere.com']
            , $this->code);
    }

}