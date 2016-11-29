<?php 

namespace Scubawhere\Exceptions;

use Exception;

abstract class BaseException extends Exception
{
	public abstract function response();
}
