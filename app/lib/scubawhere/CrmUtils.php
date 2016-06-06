<?php namespace ScubaWhere;

use ScubaWhere\Context;

class CrmUtils 
{
	public static function getCustomersByGroup($group_ids)
	{
		$tmpRules = [];
        $rules = [];
        $rules['certs'] = [];
        $rules['classes'] = [];
        $rules['tickets'] = [];

        // FORMAT RULES INTO IDS TO FILTER THROUGH
        foreach ($group_ids as $group_id) {
            $group = Context::get()->crmGroups()->where('id', '=', $group_id)->with('rules')->first();
            $tmpRules = $group->rules;
            foreach ($tmpRules as $rule) {
                // Translate agency to certification ids
                if($rule->agency_id !== null) {
                    $agency = Agency::with('certificates')->findOrFail( $rule->agency_id ); // Context::get()->agencies() etc gives eeror
                    $certs = $agency->certificates;
                    foreach ($certs as $cert) {
                        array_push($rules['certs'], $cert->id); // ->id
                    }
                }
                else if($rule->certificate_id !== null) {
                    array_push($rules['certs'], $rule->certificate_id);
                }
                else if($rule->training_id !== null) {
                    array_push($rules['classes'], $rule->training_id);
                }
                else if($rule->ticket_id !== null) {
                    array_push($rules['tickets'], $rule->ticket_id);
                }
            }
        }

        $certificates_customers = Context::get()->customers()->whereHas('certificates', function($query) use ($rules){
            $query->whereIn('certificates.id', $rules['certs']);
        })->get();//->lists('email', 'firstname', 'lastname', 'id');

        $customers = array_merge($customers, array($certificates_customers));

        $booked_customers = Context::get()->bookingdetails()->whereHas('booking', function($query) use($rules) {
            $query->where('status', 'confirmed'); // changed confirmed to completed when soren pushes it
        })->whereIn('ticket_id', $rules['tickets']) //->orWhereIn('training_id', $rules['classes']) ADD WHEN SOREN ADDS TRAINING IS TO DETAILS
        ->leftJoin('customers', 'customers.id', '=', 'booking_details.customer_id')->get();//->lists('email', 'firstname', 'lastname', 'id');

        $customers = array_merge($customers, array($booked_customers));
        $customers = array_unique($customers);
        $customers = $customers[0]; // second index is empty array?

        return $customers;
	}

}