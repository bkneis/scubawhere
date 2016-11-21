<?php

namespace Scubawhere\Entities;

use Scubawhere\Context;
use LaravelBook\Ardent\Ardent;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Ardent implements UserInterface, RemindableInterface
{
	use RemindableTrait;

	protected $guarded = array('id', 'password', 'remember_token', 'created_at', 'updated_at');

	protected $hidden = array('password');

	public static $rules = array(
		'username' => 'sometimes|required|alpha_dash|between:4,64|unique:users,username',
		'password' => 'size:60',
		'email'    => 'required|email|unique:users,email',
		'phone'    => '', //'required',
	);

	/**
	 * The default company for the user.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function company()
	{
		return $this->belongsTo('\Scubawhere\Entities\Company');
	}

	/**
	 * The companies the user is authorized for.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function companies()
	{
		return $this->belongsToMany('\Scubawhere\Entities\Company', 'user_company');
	}

	public function scopeOnlyOwners($query)
	{
		return $query->where('company_id', Context::get()->id);
	}

	/**
	 * Get the name of the key used to store the company the context should be set to.
	 *
	 * @return string
	 */
	public function getActiveCompanyKey()
	{
		return $this->username . '-active-company';
	}

	/**
	 * Get the company the user's context is currently set to.
	 *
	 * If not set then it defaults to the company in the company_id field in users table.
	 *
	 * @return \Scubawhere\Entities\Company
	 */
	public function getActiveCompany()
	{
		$company_id = \Cache::get($this->getActiveCompanyKey());
		if(is_null($company_id)) {
			return $this->company;
		}
		return Company::findOrFail((int) $company_id);
	}

	/**
	 * Push the company's id as the value to the user's active company (context)
	 *
	 * @param $id
	 */
	public function setActiveCompany($id)
	{
		\Cache::forever(\Auth::user()->getActiveCompanyKey(), $id);
	}

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}

	/**
	 * Additions with Laravel v4.1.26
	 */
	public function getRememberToken()
	{
		return $this->remember_token;
	}

	public function setRememberToken($value)
	{
		$this->remember_token = $value;
	}

	public function getRememberTokenName()
	{
		return 'remember_token';
	}

}
