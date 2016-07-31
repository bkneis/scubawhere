<?php
namespace ScubaWhere\Exceptions;

use Exception;
use Illuminate\Http\Response;

class UsedInFutureException extends Exception
{
    protected $obj;

    public function __construct($obj)
    {
        $this->obj = $obj;
    }

    public function response()
    {
        return Response::json(406, array('errors' => $this->obj . ' can not be deleted as it is used in future bookings'));
    }
}
