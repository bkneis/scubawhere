<?php

namespace Scubawhere\Strategies;

class SourcesReportGenerator extends BaseReportGenerator implements ReportGeneratorInterface {

	private function getFrequencyOfBooking()
	{
		// Generate frequency of booking sources
		$sql = "SELECT source, COUNT(*) FROM bookings WHERE company_id=? AND status='confirmed' AND created_at BETWEEN ? AND ? GROUP BY source";
		$sql = DB::select($sql, [Context::get()->id, $afterUTC, $beforeUTC]);

		$sources = [];

		foreach($sql as $object) {
			$name = $object->source === null ? 'agent' : $object->source;
			$sources[$name] = $object->{'COUNT(*)'};
		}

		return $source;
	}

	private function getRevenueForSources($currency)
	{
		// Generate revenue per booking source
		$sql = "SELECT source, SUM(price) FROM bookings WHERE company_id=? AND status='confirmed' AND created_at BETWEEN ? AND ? GROUP BY source";
		$sql = DB::select($sql, [Context::get()->id, $afterUTC, $beforeUTC]);

		$sources = [];

		foreach($sql as $object)
		{
			$name = $object->source === null ? 'agent' : $object->source;

			if(empty($sources[$name])) $sources[$name] = 0;

			$sources[$name] += $object->{'SUM(price)'} / $currency->getSubunitToUnit();
		}
		return $sources;
	}

	public function createReport($before, $after) 
	{
		$RESULT = array();
		$currency = new PhilipBrown\Money\Currency( Context::get()->currency->code );

		$RESULT['daterange'] = $this->getDates();
		$RESULT['source_frequency'] = $this->getFrequencyOfBooking();
		$RESULT['source_revenue'] = $this->getRevenueForSources($curency);

		return $RESULT;	
	}

}