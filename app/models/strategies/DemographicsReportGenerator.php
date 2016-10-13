<?php

namespace ScubaWhere\Strategies;

class DemographicsReportGenerator implements ReportGeneratorInterface {

	public function getBookingsByAge()
	{
		return DB::table('bookings')
		    ->join('customers', 'bookings.lead_customer_id', '=', 'customers.id')
			->where('bookings.company_id', Context::get()->id)
		    ->where('bookings.status', 'confirmed')
		    ->whereBetween('bookings.created_at', [$afterUTC, $beforeUTC])
		    ->select('bookings.price', 'bookings.created_at', 'customers.birthday')
			->get();
	}

	private function getBookingsByCountry()
	{
		return DB::table('bookings')
		    ->join('customers', 'bookings.lead_customer_id', '=', 'customers.id')
		    ->join('countries', 'customers.country_id', '=', 'countries.id')
		    ->where('bookings.company_id', Context::get()->id)
		    ->where('bookings.status', 'confirmed')
		    ->whereBetween('bookings.created_at', [$afterUTC, $beforeUTC])
		    ->select('customers.country_id', 'countries.name', DB::raw('SUM(price)'))
		    ->groupBy('customers.country_id')
		    ->get();

	}

	private function getBookingsByCertification()
	{
		return DB::table('bookings')
		    ->join('customers', 'bookings.lead_customer_id', '=', 'customers.id')
		    ->join('certificate_customer', 'customer_id', '=', 'bookings.lead_customer_id')
		    ->where('bookings.company_id', Context::get()->id)
		    ->where('bookings.status', 'confirmed')
		    ->whereBetween('bookings.created_at', [$afterUTC, $beforeUTC])
		    ->select('certificate_customer.certificate_id', DB::raw('SUM(price)'))
		    ->groupBy('certificate_customer.certificate_id')
		    ->get();
	}

	private function calculateAgeDemographic()
	{
		$sql = $this->getBookingsByAge();

		$ages = [];
		$ages['0-15']    = 0;
		$ages['16-25']   = 0;
		$ages['26-35']   = 0;
		$ages['36-50']   = 0;
		$ages['50+']     = 0;
		$ages['unknown'] = 0;

		foreach($sql as $object)
		{
			if($object->birthday != null) {

				$dateOfBooking = new DateTime($object->created_at);
				$dateOfBooking->setTime(0, 0, 0);

				$birthday = new DateTime($object->birthday);

				$age = $dateOfBooking->diff($birthday)->y;

				     if(             $age <= 15) $ages['0-15']  += $object->price;
				else if($age > 16 && $age <= 25) $ages['16-25'] += $object->price;
				else if($age > 25 && $age <= 35) $ages['26-35'] += $object->price;
				else if($age > 35 && $age <= 50) $ages['36-50'] += $object->price;
				else if($age > 50)               $ages['50+']   += $object->price;
			}
			else
				$ages['unknown'] += $object->price;
		}
		return $ages;
	}

	private function calculateCountryDemographic($currency)
	{
		$sql = $this->getBookingsByCountry();

		$countries = [];

		foreach($sql as $object)
		{
			$name = $object->name;

			if(empty($countries[$name])) $countries[$name] = 0;

			$countries[$name] += $object->{'SUM(price)'} / $currency->getSubunitToUnit();
		}
		return $countries;
	}

	private function calculateCertificationDemographic($currency)
	{
		$sql = $this->getBookingsByCertification();

		$certificates = [];
		$cert_list = Certificate::lists('name', 'id'); // Produces [id => name] array
		foreach($sql as $object)
		{
			$cert_name = $cert_list[$object->certificate_id];
			if(empty($certificates[$cert_name])) {
				$certificates[$cert_name] = 0;
			}
			$certificates[$cert_name] += $object->{'SUM(price)'} / $currency->getSubunitToUnit();
		}
		return $certificates;
	}

	public function createReport($before, $after) 
	{
		$RESULT = [];
		$currency = new PhilipBrown\Money\Currency( Context::get()->currency->code );

		$RESULT['age_revenue']         = $this->calculateAgeDemographic();
		$RESULT['country_revenue']     = $this->calculateCountryDemographic($currency);
		$RESULT['certificate_revenue'] = $this->calculateCertificationDemographic($currency);

		return $RESULT;	
	}

}