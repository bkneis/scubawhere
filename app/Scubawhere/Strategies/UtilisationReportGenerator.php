<?php

namespace Scubawhere\Strategies;

use Scubawhere\Context;
use Scubawhere\Exceptions\Http\HttpInternalServerError;

class UtilisationReportGenerator extends BaseReportGenerator implements ReportGeneratorInterface {

	private $relations;
	private $relation;
	private $bookable;

	protected $type;

	public function __construct($type) 
	{
		$this->type = $type;
	}
	
	private function getData($before, $after)
	{
		$model = null;

		if($this->type === 'sessions') 
		{
			$model                 = Context::get()->departures();
			$this->relation        = 'trip';
			$this->bookable        = 'ticket';
			$this->relations       = 'trips';
		}
		elseif($this->type === 'trainings') 
		{
			$model                 = Context::get()->training_sessions();
			$this->relation        = 'training';
			$this->bookable        = 'course';
			$this->relations       = 'courses';
		}
		else
		{
			throw new HttpInternalServerError(__CLASS__.__METHOD__);
		}

		return $model->whereBetween('start', [$after, $before])->with(
			$this->relation,
			'bookingdetails',
				'bookingdetails.booking',
				'bookingdetails.' . $this->bookable
		)->orderBy('start')->get();
	}

	private function calculateUtilisation($departures)
	{
		$relations = $this->relations;

		$utilisation = [];
		$i = 1;
		foreach($departures as $departure)
		{
			$max = isset($departure->capacity[1]) ? $departure->capacity[1] : 0;

			$utilisation[$i] = [
				'date'        => $departure->start,
				'name'        => $departure->{$this->relation}->name,
				$relations    => [],
				'assigned'    => 0,
				'unassigned'  => $max,
				'capacity'    => $max,
			];

			foreach($departure->bookingdetails as $detail)
			{
				if($detail->booking->status !== 'confirmed') continue;

				$utilisation[$i]['assigned']++;

				if(empty($utilisation[$i][$relations][$detail->{$this->bookable}->name])) {
					$utilisation[$i][$relations][$detail->{$this->bookable}->name] = 1;
				}
				else {
					$utilisation[$i][$relations][$detail->{$this->bookable}->name]++;
				}

				$utilisation[$i]['unassigned']--;

				/*if($utilisation[$i]['unassigned'] > 0) {
					$utilisation[$i]['unassigned']--;
				}*/
			}

			if($utilisation[$i]['capacity'] === 0)
				$utilisation[$i]['capacity'] = $utilisation[$i]['assigned'];

			$i++;
		}
		return $utilisation;
	}

	private function calculateTotals($utilisation) 
	{
		$relations = $this->relations;
		$total = [$relations => [], 'assigned' => 0, 'unassigned' => 0, 'capacity' => 0];
		foreach ($utilisation as $trip)
		{
			$total['assigned']   += $trip['assigned'];
			$total['unassigned'] += $trip['unassigned'];
			$total['capacity']   += $trip['capacity'];

			foreach($trip[$relations] as $name => $number)
			{
				if(empty($total[$relations][$name])) $total[$relations][$name] = 0;

				$total[$relations][$name] += $number;
			}
		}
		return $total;
	}

	public function createReport($before, $after) 
	{
		$RESULT = array();

		$departures = $this->getData($before, $after);
		$utilisation = $this->calculateUtilisation($departures);
		$total = $this->calculateTotals($utilisation);

		$RESULT['daterange'] = $this->getDates($before, $after);
		$RESULT['utilisation'] = $utilisation;
		$RESULT['utilisation_total'] = $total;

		return $RESULT;			
	}

}