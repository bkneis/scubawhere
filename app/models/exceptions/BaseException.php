<?php 

namespace ScubaWhere\Exceptions;

use Exception;

abstract class BaseException extends Exception
{
	public abstract function response();
}
