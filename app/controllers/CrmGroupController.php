<?php

use Scubawhere\Context;
use Scubawhere\Entities\Agency;
use Scubawhere\Entities\Booking;
use Scubawhere\Entities\CrmGroup;
use Scubawhere\Entities\CrmGroupRule;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CrmGroupController extends Controller 
{
    public function getIndex() 
    {
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			return Context::get()->customerGroups()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The customer group could not be found.')), 404 ); // 404 Not Found
		}

	}

	public function getAll()
	{
		return Context::get()->crmGroups()->with('rules')->get(); // with('rules')
	}

	public function getAllWithTrashed()
	{
		return Context::get()->crmGroups()->with('rules')->withTrashed()->get();
	}

	/**
	 * @todo This function is horrifically expensive. During first round of refactoring we need to :
	 * 1. Use join instead of eager loading customer and token relationships
	 * 2. Use mysql to use aggregate functions such as count instead of returning all of the tokens
	 * 3. Use mysql to aggregate the customers so we do not require php to filter through unique customers
	 * 4. Seperate the logic of retrieving tokens and customers to reuse this function when generating customer list for campaigns
	*/
    public function getCustomeranalytics()
    {
        $group_id = Input::only('id');

        $customers = [];

		$tmpRules = [];
		$rules = [];
		$rules['certs'] = [];
		$rules['classes'] = [];
		$rules['tickets'] = [];

		// FORMAT RULES INTO IDS TO FILTER THROUGH
        $group = Context::get()->crmGroups()->where('id', '=', $group_id)->with('rules')->first();
        $tmpRules = $group->rules;
        foreach ($tmpRules as $rule) {
            // Translate agency to certification ids
            if($rule->agency_id !== null) {
                $agency = Agency::with('certificates')->findOrFail( $rule->agency_id );
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

		$certificates_customers = Context::get()->customers()
			->with('tokens')
			->whereHas('certificates', function($query) use ($rules){
				$query->whereIn('certificates.id', $rules['certs']);
			})
			->get()//;->lists('email', 'firstname', 'lastname', 'id');
			->map(function($obj) {
				$data = array();
				$num_sent = 0;
				$num_read = 0;
				foreach($obj->tokens as $token) 
				{
					$num_sent++;
					if($token->opened > 0) $num_read++;
				}
				$data['num_sent']    = $num_sent;
				$data['num_read']    = $num_read;
				$data['firstname']   = $obj->firstname;
				$data['id']   		 = $obj->id;
				$data['lastname']    = $obj->lastname;
				$data['email']       = $obj->email;
				$data['unsubscribe'] = $obj->unsubscribed;
				$data['opened_rate'] = $num_sent != 0 ? ($num_read / $num_sent) * 100 : 0; 
				return $data;
			})
			->toArray();

		$customers = $certificates_customers;

		$booked_customers = Context::get()->bookingdetails()
			->whereHas('booking', function($query) {
				$query->whereIn('status', Booking::$counted); 
			})
			->whereIn('ticket_id', $rules['tickets'])
			->orWhereIn('training_id', $rules['classes'])
			->with('customer.tokens')
			/*->with(['customer' => function($q) {
				$q->select('firstname', 'lastname', 'email');
			},
			'customer.tokens' => function($q) {
				$q->select(DB::raw('count(opened)'));	
			}])*/
			->get()
			->map(function($obj) {
				$data = array();
				$num_sent = 0;
				$num_read = 0;
				foreach($obj->customer->tokens as $token) 
				{
					$num_sent++;
					if($token->opened > 0) $num_read++;
				}
				$data['num_sent']    = $num_sent;
				$data['num_read']    = $num_read;
				$data['firstname']   = $obj->customer->firstname;
				$data['lastname']    = $obj->customer->lastname;
				$data['id']			 = $obj->customer->id;
				$data['email']       = $obj->customer->email;
				$data['unsubscribe'] = $obj->customer->unsubscribed;
				$data['opened_rate'] = $num_sent != 0 ? ($num_read / $num_sent) * 100 : 0; 
				return $data;
			})
			->toArray();

		$customers = array_merge($customers, $booked_customers);
		$customers = array_unique($customers, SORT_REGULAR);
		$customers = array_filter($customers, function($obj) { return !$obj['unsubscribe']; });

        return Response::json($customers, 200);
    }

	// @deprecated
    public function getCustomers()
    {
        $group_id = Input::only('id');
        $customers = [];

		$tmpRules = [];
		$rules = [];
		$rules['certs'] = [];
		$rules['classes'] = [];
		$rules['tickets'] = [];

		// FORMAT RULES INTO IDS TO FILTER THROUGH
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

		$certificates_customers = Context::get()->customers()
			->whereHas('certificates', function($query) use ($rules){
				$query->whereIn('certificates.id', $rules['certs']);
			})
			->with(['crmSubscription' => function($q) {
				$val = 1;
				$q->where('subscribed', '=', $val);	
			}])->get();//->lists('email', 'firstname', 'lastname', 'id');

		$customers = array_merge($customers, array($certificates_customers));

		$booked_customers = Context::get()->bookingdetails()
			->whereHas('booking', function($query) use($rules) {
				$query->where('status', Booking::$counted); // changed confirmed to completed when soren pushes it
			})->whereIn('ticket_id', $rules['tickets']) //->orWhereIn('training_id', $rules['classes']) ADD WHEN SOREN ADDS TRAINING IS TO DETAILS
			->leftJoin('customers', 'customers.id', '=', 'booking_details.customer_id')
			->get();//->lists('email', 'firstname', 'lastname', 'id');

		$customers = array_merge($customers, array($booked_customers));
		$customers = array_unique($customers);
        $customers = $customers[0]; // second index is empty array?

        return Response::json(array('customers' => $customers), 200);
    }

	public function postAdd()
	{
		$data = Input::only(
			'name',
			'description'
		);

		$group = new CrmGroup($data);

		if( !$group->validate() )
		{
			return Response::json( array('errors' => $group->errors()->all()), 406 ); // 406 Not Acceptable
		}

		$group = Context::get()->crmGroups()->save($group);

		// ADD RULES FOR CERTIFICATES
		$certificates = Input::get('certificates', []);

		foreach ($certificates as $cert) {
			$rule_data = array('certificate_id' => (int) $cert);
			$rule = new CrmGroupRule($rule_data);
			if( !$rule->validate() )
			{
				return Response::json( array('errors' => $rule->errors()->all()), 406 ); // 406 Not Acceptable
			}

			$group->rules()->save($rule);
		}

		// ADD RULES FOR TICKETS
		$tickets = Input::get('tickets', []);

		foreach ($tickets as $ticket) {
			$rule_data = array('ticket_id' => (int) $ticket);
			$rule = new CrmGroupRule($rule_data);
			if( !$rule->validate() )
			{
				return Response::json( array('errors' => $rule->errors()->all()), 406 ); // 406 Not Acceptable
			}

			$group->rules()->save($rule);
		}

		// ADD RULES FOR TRAININGS
		$trainings = Input::get('classes', []);

		foreach ($trainings as $training) {
			$rule_data = array('training_id' => (int) $training);
			$rule = new CrmGroupRule($rule_data);
			if( !$rule->validate() )
			{
				return Response::json( array('errors' => $rule->errors()->all()), 406 ); // 406 Not Acceptable
			}

			$group->rules()->save($rule);
		}

		// ADD RULES FOR TRAININGS
		$agencies = Input::get('agencies', []);

		foreach ($agencies as $agency) {
			$rule_data = array('agency_id' => (int) $agency);
			$rule = new CrmGroupRule($rule_data);
			if( !$rule->validate() )
			{
				return Response::json( array('errors' => $rule->errors()->all()), 406 ); // 406 Not Acceptable
			}

			$group->rules()->save($rule);
		}

		return Response::json( array('status' => '<b>OK</b> Customer Group created', 'id' => $group->id), 201 ); // 201 Created
	}

	public function postEdit()
	{

		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$group = Context::get()->crmGroups()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The agent could not be found.')), 404 ); // 404 Not Found
		}

		$data = Input::only(
			'name',
			'description'
		);

		if( !$group->update($data) )
		{
			return Response::json( array('errors' => $group->errors()->all()), 406 ); // 406 Not Acceptable
		}

		$group->rules()->delete();

		$certificates = Input::get('certificates', []);
		foreach ($certificates as $cert) {
			// check if cert is in group rules, if not add it
			$rule_data = array('certificate_id' => (int) $cert);
			$rule = new CrmGroupRule($rule_data);
			if( !$rule->validate() )
			{
				return Response::json( array('errors' => $rule->errors()->all()), 406 ); // 406 Not Acceptable
			}

			$group->rules()->save($rule);
		}

		// ADD RULES FOR TICKETS
		$tickets = Input::get('tickets', []);

		foreach ($tickets as $ticket) {
			$rule_data = array('ticket_id' => (int) $ticket);
			$rule = new CrmGroupRule($rule_data);
			if( !$rule->validate() )
			{
				return Response::json( array('errors' => $rule->errors()->all()), 406 ); // 406 Not Acceptable
			}

			$group->rules()->save($rule);
		}

		// ADD RULES FOR TRAININGS
		$trainings = Input::get('classes', []);

		foreach ($trainings as $training) {
			$rule_data = array('training_id' => (int) $training);
			$rule = new CrmGroupRule($rule_data);
			if( !$rule->validate() )
			{
				return Response::json( array('errors' => $rule->errors()->all()), 406 ); // 406 Not Acceptable
			}

			$group->rules()->save($rule);
		}

		// ADD RULES FOR TRAININGS
		$agencies = Input::get('agencies', []);

		foreach ($agencies as $agency) {
			$rule_data = array('agency_id' => (int) $agency);
			$rule = new CrmGroupRule($rule_data);
			if( !$rule->validate() )
			{
				return Response::json( array('errors' => $rule->errors()->all()), 406 ); // 406 Not Acceptable
			}

			$group->rules()->save($rule);
		}

		return Response::json( array('status' => '<b>OK</b> Customer Group updated', 'id' => $group->id), 201 ); // 201 Created
	}

	public function postDelete()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$group = Context::get()->crmGroups()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The customer group could not be found.')), 404 ); // 404 Not Found
		}

		try
		{
			$group->forceDelete();
		}
		catch(QueryException $e)
		{

			$group = Context::get()->crmGroups()->find( Input::get('id') );
			$group->delete();
		}

		return array('status' => 'Ok. Customer Group deleted');
	}

}
