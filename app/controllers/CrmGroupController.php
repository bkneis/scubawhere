<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use ScubaWhere\Helper;

class CrmGroupController extends Controller {

	public function getIndex() {

		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			return Auth::user()->customerGroups()->findOrFail( Input::get('id') );
		}
		catch(ModelNotFoundException $e)
		{
			return Response::json( array('errors' => array('The customer group could not be found.')), 404 ); // 404 Not Found
		}

	}

	public function getAll()
	{
		return Auth::user()->crmGroups()->with('rules')->get(); // with('rules')
	}

	public function getAllWithTrashed()
	{
		return Auth::user()->crmGroups()->with('rules')->withTrashed()->get();
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

		$group = Auth::user()->crmGroups()->save($group);

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
			$group = Auth::user()->crmGroups()->findOrFail( Input::get('id') );
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

		return Response::json( array('status' => '<b>OK</b> Customer Group updated', 'id' => $group->id), 201 ); // 201 Created
	}

	public function postDelete()
	{
		try
		{
			if( !Input::get('id') ) throw new ModelNotFoundException();
			$group = Auth::user()->crmGroups()->findOrFail( Input::get('id') );
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

			$group = Auth::user()->crmGroups()->find( Input::get('id') );
			$group->delete();
		}

		return array('status' => 'Ok. Customer Group deleted');
	}

}