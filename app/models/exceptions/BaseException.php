<?php namespace ScubaWhere\Exceptions;

use Exception;

abstract class BaseException extends Exception
{
	abstract function response();
}
