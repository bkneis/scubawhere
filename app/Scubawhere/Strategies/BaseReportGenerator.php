<?php

namespace Scubawhere\Strategies;

use Scubawhere\Helper;
use Scubawhere\Context;

class BaseReportGenerator {
	
	protected function getDates($before, $after)
	{
		$before = new \DateTime($before);
		$before->add(new \DateInterval('P1D'));
		$before = $before->format('Y-m-d H:i:s');

		return array(
			'after'    => Helper::sanitiseString($after),
			'before'   => Helper::sanitiseString($before),
			'timezone' => Context::get()->timezone,
		);
	}

}