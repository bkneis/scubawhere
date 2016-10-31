<?php 

namespace Scubawhere\Repositories;

use Scubawhere\Context;

class CreditRepo implements CreditRepoInterface {

	public function __construct()
	{
		$this->company_model = Context::get();
	}
	
	public function get($column)
	{
		// @todo this doesnt feel right, why does it return an array, utsl the first func
		return Context::get()->credits()->select($column)->first()[$column];
	}

	// @note Should I use magic methods here like _call to overload a function get() and accept one or many arguments with the same call?
	public function gets($columns)
	{
		return Context::get()->credits()->select($columns)->first();
	}

	public function getAll()
	{
		return Context::get()->credits()->first();
	}

}
