<?php

namespace ScubaWhere\Strategies;

interface ReportGeneratorInterface {
	
	public function createReport($before, $after);

}