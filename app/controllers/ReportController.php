<?php

use Scubawhere\Services\ReportService;
use Scubawhere\Exceptions\Http\HttpUnprocessableEntity;

class ReportController extends Controller {

	protected $report_service;

	public function __construct(ReportService $report_service)
	{
		$this->report_service = $report_service;
	}

	public function getUtilisation()
	{
		/**
		 * Allowed input parameter
		 * after  {date string}
		 * before {date string}
		 */

		$after  = Input::get('after', null);
		$before = Input::get('before', null);

		if(empty($after) || empty($before))
			return Response::json(['errors' => ['Both the "after" and the "before" parameters are required.']], 400); // 400 Bad Request

		return $this->report_service->generate('utilisation', $before, $after);
	}

	public function getTrainingutilisation()
	{
		/**
		 * Allowed input parameter
		 * after  {date string}
		 * before {date string}
		 */

		$after  = Input::get('after', null);
		$before = Input::get('before', null);

		if(empty($after) || empty($before))
			return Response::json(['errors' => ['Both the "after" and the "before" parameters are required.']], 400); // 400 Bad Request

		return  $this->report_service->generate('training_utilisation', $before, $after);
	}

	public function getSources()
	{
		/**
		 * Allowed input parameter
		 * after  {date string}
		 * before {date string}
		 */

		$after  = Input::get('after', null);
		$before = Input::get('before', null);

		if(empty($after) || empty($before))
			return Response::json(['errors' => ['Both the "after" and the "before" parameters are required.']], 400); // 400 Bad Request

		return $this->report_service->generate('sources', $before, $after);
	}

	public function getDemographics()
	{
		/**
		 * Allowed input parameter
		 * after  {date string}
		 * before {date string}
		 */

		$after  = Input::get('after', null);
		$before = Input::get('before', null);

		if(empty($after) || empty($before))
			return Response::json(['errors' => ['Both the "after" and the "before" parameters are required.']], 400); // 400 Bad Request

		return $this->report_service->generate('demographics', $before, $after);
	}

	/**
	 * @todo This should really accept a parameter of the type of report to create i.e. accommodations, summary (totals) or packages
	 * @return array
	 */
	public function getRevenueStreams()
	{
		$after  = Input::get('after', null);
		$before = Input::get('before', null);

		if(empty($after) || empty($before))
			return Response::json(['errors' => ['Both the "after" and the "before" parameters are required.']], 400); // 400 Bad Request

		return $this->report_service->generate('revenue', $before, $after);
	}

	public function getCancellations()
	{
		$after  = Input::get('after', null);
		$before = Input::get('before', null);

		if(empty($after) || empty($before)) {
			throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, array(
				'errors' => ['Both the "after" and the "before" parameters are required.']
			));
		}

		$report = $this->report_service->generate('cancellations', $before, $after);

		return Response::json(array(
			'status' => 'Success. Report created',
			'data'   => array('report' => $report)
		));
	}

	public function getDiscounts()
	{
		$after  = Input::get('after', null);
		$before = Input::get('before', null);

		if(empty($after) || empty($before)) {
			throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, array(
				'errors' => ['Both the "after" and the "before" parameters are required.']
			));
		}

		$report = $this->report_service->generate('discounts', $before, $after);

		return Response::json(array(
			'status' => 'Success. Report created',
			'data'   => array('report' => $report)
		));
	}
	
	public function getSurcharges()
	{
		$after  = Input::get('after', null);
		$before = Input::get('before', null);

		if(empty($after) || empty($before)) {
			throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, array(
				'errors' => ['Both the "after" and the "before" parameters are required.']
			));
		}

		$report = $this->report_service->generate('surcharges', $before, $after);

		return Response::json(array(
			'status' => 'Success. Report created',
			'data'   => array('report' => $report)
		));
	}
}
