<?php

namespace ScubaWhere\Services;

use ScubaWhere\Strategies\SourcesReportGenerator;
use ScubaWhere\Strategies\RevenueReportGenerator;
use ScubaWhere\Strategies\UtilisationReportGenerator;
use ScubaWhere\Strategies\DemographicsReportGenerator;

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
		}
		return $generator->createReport($before, $after);
	}

}