<?php

namespace Scubawhere\Strategies;

interface ReportGeneratorInterface {
	
	public function createReport($before, $after);

}