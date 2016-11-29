<?php

namespace Scubawhere\Policies;

use Scubawhere\Exceptions\HTTPForbiddenException;

class AddPackagedAddonPolicy implements BasePolicy
{
	protected $package;

	protected $packagefacade;

	protected $addon;

	protected $booking;

	public function __construct($package, $packagefacade, $addon, $booking)
	{
		$this->package       = $package;
		$this->packagefacade = $packagefacade;
		$this->addon         = $addon;
		$this->booking       = $booking;
	}

	protected function isAddonValidForPackage()
	{
		$exists = $this->package
			->addons()
			->where('id', $this->addon->id)
			->exists();

		if (!$exists) {
			throw new HTTPForbiddenException(['This addon can not be booked as part of this package.']);
		}
	}

	protected function isBookingdetailTheSameAsThePackage()
	{
		// Validate that the bookingdetail is in the same package as the addon
		if ($this->bookingdetail->packagefacade_id !== $this->packagefacade->id) {
			throw new HTTPForbiddenException(['This addon can not be booked for this trip, as the trip is not in the same package.']);
		}
	}

	protected function hasPackageGotSpaceForAddon()
	{
		$bookedAddonsQuantity = $this->addon
			->bookingdetails()
			->wherePivot('packagefacade_id', $this->packagefacade->id)
			->filterByID($this->booking->id)
			->sum('addon_bookingdetail.quantity');

		$packageAddonsQuantity = $this->package
			->addons()
			->where('id', $this->addon->id)
			->first()
			->pivot->quantity;

		if (($bookedAddonsQuantity + $this->quantity) > $packageAddonsQuantity) {
			throw new HTTPForbiddenException(['The addon cannot be assigned because the package\'s limit for the addon would be exceeded.']);
		}
	}

	protected function hasAtleastOneAddon()
	{
        $validator = Validator::make(array('quantity' => $quantity), array('quantity' => 'integer|min:1'));
        if ($validator->fails()) {
			throw new BadRequestException($validator->messages()->all());
        }
	}

	public function allows()
	{
		$this->isAddonValidForPackage();
		$this->isBookingdetailTheSameAsThePackage();
		$this->hasPackageGotSpaceForAddon();
		$this->hasAtleastOneAddon();
	}
	
}

