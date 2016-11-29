<?php

namespace Scubawhere\Services;

use Scubawhere\Strategies\DiscountsReportGenerator;
use Scubawhere\Strategies\SourcesReportGenerator;
use Scubawhere\Strategies\RevenueReportGenerator;
use Scubawhere\Strategies\UtilisationReportGenerator;
use Scubawhere\Strategies\DemographicsReportGenerator;
use Scubawhere\Strategies\CancellationsReportGenerator;

class ReportService {

	public function generate($type, $before, $after) 
	{
		$generator = null;
		switch($type) {
			case 'utilisation':
				$generator = new UtilisationReportGenerator('sessions');
				break;
			case 'training_utilisation':
				$generator = new UtilisationReportGenerator('trainings');
				break;
			case 'sources':
				$generator = new SourcesReportGenerator;
				break;
			case 'demographics':
				$generator = new DemographicsReportGenerator;
				break;
			case 'revenue':
				$generator = new RevenueReportGenerator;
				break;
			case 'cancellations':
				$generator = new CancellationsReportGenerator;
				break;
			case 'discounts':
				$generator = new DiscountsReportGenerator;
				break;
		}
		return $generator->createReport($before, $after);
	}

}