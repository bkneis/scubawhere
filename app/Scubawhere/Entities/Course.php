<?php

namespace Scubawhere\Entities;

use Scubawhere\Helper;
use Scubawhere\Context;
use LaravelBook\Ardent\Ardent;
use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Scubawhere\Exceptions\Http\HttpUnprocessableEntity;

class Course extends Ardent {
	
	use Owneable;
	use Bookable;
	use Packageable;
	use SoftDeletingTrait;
	
	protected $dates = array('deleted_at');
	
	protected $fillable = array('name', 'description', 'capacity', 'certificate_id');

	public static $rules = array(
		'name'           => 'required',
		'description'    => '',
		'capacity'       => 'integer|min:0',
		'certificate_id' => 'integer|exists:certificates,id'
	);

	/**
	 * Sanitise the html and string inputs using Purifier
     */
	public function beforeSave()
	{
		if (isset($this->name)) {
			$this->name = Helper::sanitiseString($this->name);
		}
		if (isset($this->description)) {
			$this->description = Helper::sanitiseBasicTags($this->description);
		}
    }

	/**
	 * Overload Eloquent's create method to also return a HTTP response on failure
	 * 
	 * @param array $data
	 * @return \Illuminate\Database\Eloquent\Model
	 * @throws HttpUnprocessableEntity
	 * @throws \Exception
     */
	public static function create(array $data = [])
	{
		$course = new Course($data);
		if (!$course->validate()) {
			throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, $course->errors()->all());
		}
		return Context::get()->courses()->save($course);
	}

	/**
	 * Syncronise the addons related to this package to the given array
	 * 
	 * @param array $data
	 * @return $this
	 * @throws HttpUnprocessableEntity
     */
	public function update(array $data = [])
	{
		if (! parent::update($data)) {
			throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, $this->errors()->all());
		}
		return $this;
	}
	
	/**
	 * Syncronise the tickets and trainings id's to a course.
	 *
	 * This function utilises laravel's sync method that ensures the ids of 
	 * the given array match the ones in the course_* pivot tables.
	 *
	 * @param $data
	 * @return $this
	 */
	public function syncItems($data)
	{
		if (! is_null($data['tickets'])) {
			$this->tickets()->sync($data['tickets']);
		}
		if (! is_null($data['trainings'])) {
			$this->trainings()->sync($data['trainings']);
		}
		return $this;
	}
	
	/**
	 * |--------------------------------------
	 * | Eloquent relationships
	 * |--------------------------------------
	 */

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
	public function tickets()
	{
		return $this->belongsToMany('\Scubawhere\Entities\Ticket')->withPivot('quantity')->withTimestamps();
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
	public function trainings()
	{
        return $this->belongsToMany('\Scubawhere\Entities\Training')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

}
