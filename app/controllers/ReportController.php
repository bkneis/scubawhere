<?php

use Scubawhere\Helper;
use Scubawhere\Context;
use Scubawhere\Services\ReportService;

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

	public function getRevenueStreams()
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

		return $this->report_service->generate('revenue', $before, $after);
	}
}
