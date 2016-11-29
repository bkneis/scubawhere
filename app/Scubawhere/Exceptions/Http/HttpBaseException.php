<?php

namespace Scubawhere\Exceptions\Http;

use Exception;
use Scubawhere\Context;
use Illuminate\Support\Facades\Cache;

abstract class HttpBaseException extends Exception
{
    public function __construct($code, $id) {
        $this->log($code, $id);
    }
    
    public function log($code, $id)
    {
        $data = array(
            'code'     => $code,
            'id'       => $id,
            'company'  => Context::get()->name,
            'time'     => time()
        );

        $key = Context::get()->name . '-' . $code . '-' . time();

        Cache::forever($key, $data);
    }

    abstract public function response();
}