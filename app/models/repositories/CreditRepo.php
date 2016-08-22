<?php namespace ScubaWhere\Repositories;

use ScubaWhere\Context;

class CreditRepo implements CreditRepoInterface {

	public function __construct()
	{
		$this->company_model = Context::get();
	}

	/*public function get($columns)
	{
		$attrs = implode(',', $columns);
		dd($attrs);
		return Context::get()->credits()->get($attrs);
	}*/

	public function get($column)
	{
		// @todo this doesnt feel right, why does it return an array, utsl the first func
		return Context::get()->credits()->select($column)->first()[$column];
	}

	public function getAll()
	{
		return Context::get()->credits()->first();
	}

}
