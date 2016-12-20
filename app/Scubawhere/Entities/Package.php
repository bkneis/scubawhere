<?php

namespace Scubawhere\Entities;

use Scubawhere\Helper;
use Scubawhere\Context;
use LaravelBook\Ardent\Ardent;
use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Scubawhere\Exceptions\Http\HttpUnprocessableEntity;

class Package extends Ardent {
	
	use Owneable;
	use Bookable;
	use SoftDeletingTrait;
	use LimitedAvailability;
	
	protected $dates = array('deleted_at');

	protected $fillable = array('name', 'description', 'parent_id', 'available_from', 'available_until', 'available_for_from', 'available_for_until');

	protected $hidden = array('parent_id');

	public static $rules = array(
		'name'                => 'required',
		'description'         => '',
		'parent_id'           => 'integer|min:1',
		'available_from'      => 'date',
		'available_until'     => 'date',
		'available_for_from'  => 'date',
		'available_for_until' => 'date'
	);

	/**
	 * Sanitise all HTML inputs and strip any HTML tags for string inputs
	 */
	public function beforeSave()
	{
		if( isset($this->name) )
			$this->name = Helper::sanitiseString($this->name);

		if( isset($this->description) )
			$this->description = Helper::sanitiseBasicTags($this->description);
	}

    /**
	 * Overload Eloquent's update method to return HTTP response on failure
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
	 * Overload Eloquent's create method to also return a HTTP response on failure
	 *
	 * @param array $data
	 * @return \Illuminate\Database\Eloquent\Model
	 * @throws HttpUnprocessableEntity
	 * @throws \Exception
     */
	public static function create(array $data = [])
	{
		$package = new Package($data);
		if (!$package->validate()) {
			throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, $package->errors()->all());
		}
		return Context::get()->packages()->save($package);
	}


	/**
	 * Syncronise the addons related to this package to the given array
	 * 
	 * @param $tickets
	 * @throws HttpUnprocessableEntity
     */
	public function syncTickets($tickets)
	{
		if(is_array($tickets) && !empty($tickets)) {
			try {
				$this->tickets()->sync( $tickets );
			}
			catch(\Exception $e) {
				throw new HttpUnprocessableEntity(__CLASS__.__METHOD__,
					['Their was a problem associating the tickets array, please ensure it meets the API requirements']);
			}
		}
	}

	/**
	 * Syncronise the addons related to this package to the given array
	 * 
	 * @param $courses
	 * @throws HttpUnprocessableEntity
     */
	public function syncCourses($courses)
	{
		if(is_array($courses) && !empty($courses)) {
			try {
				$this->courses()->sync($courses);
			}
			catch(\Exception $e) {
				throw new HttpUnprocessableEntity(__CLASS__.__METHOD__,
					['Their was a problem associating the courses array, please ensure it meets the API requirements']);
			}
		}
	}

	/**
	 * Syncronise the addons related to this package to the given array
	 * 
	 * @param $accommodations
	 * @throws HttpUnprocessableEntity
     */
	public function syncAccommodations($accommodations)
	{
		if(is_array($accommodations) && !empty($accommodations)) {
			try {
				$this->accommodations()->sync($accommodations);
			}
			catch(\Exception $e) {
				throw new HttpUnprocessableEntity(__CLASS__.__METHOD__,
					['Their was a problem associating the accommodations array, please ensure it meets the API requirements']);
			}
		}
	}

	/**
	 * Syncronise the addons related to this package to the given array
	 * 
	 * @param $addons
	 * @throws HttpUnprocessableEntity
     */
	public function syncAddons($addons)
	{
		if(is_array($addons) && !empty($addons)) {
			try {
				$this->addons()->sync($addons);
			}
			catch(\Exception $e) {
				throw new HttpUnprocessableEntity(__CLASS__.__METHOD__,
					['Their was a problem associating the addons array, please ensure it meets the API requirements']);
			}
		}
	}

	/**
	 * Syncronise all the relations of the package with the given array.
	 * 
	 * This method is to simplify the interface of updating / creating a package
	 * with all of its relationships.
	 * 
	 * @param array $items
	 * @return $this
	 * @throws HttpUnprocessableEntity
     */
	public function syncItems(array $items)
	{
		$this->syncAccommodations($items['accommodations']);
		$this->syncAddons($items['addons']);
		$this->syncCourses($items['courses']);
		$this->syncTickets($items['tickets']);
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
	public function accommodations()
	{
		return $this->morphedByMany(Accommodation::class, 'packageable')
			->withPivot('quantity')
			->withTimestamps();
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
	public function addons()
	{
		return $this->morphedByMany(Addon::class, 'packageable')
			->withPivot('quantity')
			->withTimestamps();
	}

	/**
	 * Overload the relationship from the bookable trait as the package needs to be
	 * accessed through the packagefacade
	 * 
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function bookingdetails()
	{
		return $this->hasManyThrough(Bookingdetail::class, Packagefacade::class);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
	public function courses()
	{
		return $this->morphedByMany(Course::class, 'packageable')
			->withPivot('quantity')
			->withTimestamps();
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
	public function packagefacades()
	{
		return $this->hasMany(Package::class);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
	public function tickets()
	{
		return $this->morphedByMany(Ticket::class, 'packageable')
			->withPivot('quantity')
			->withTimestamps();
	}
}
