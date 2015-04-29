<?php

class AdminController extends Controller {

	public function getIndex()
	{
		return "Nothing to see here, sorry.";
	}

	public function getHeartbeats()
	{
		try
		{
			$log = File::get(storage_path() . '/logs/heartbeats.log');
		}
		catch(\Illuminate\Filesystem\FileNotFoundException $e)
		{
			return array();
		}

		// Split log file by line
		$log = explode("\n", $log);

		// Test if the last line of the log file is empty and if so, remove it
		// PHP would throw an error later when it would try to get the nonexistent 2nd value of $line
		if( $log[ count($log) - 1 ] === '' )
			array_pop($log);

		$data = array();

		// Extract required values
		foreach($log as $line)
		{
			// Split line by space
			$line = explode(" ", $line);

			// Assign values to $data array
			// Example log line: '2014-11-18 00:24:52 GET / 12.010 66.042 24.234 74.653 176.939'
			array_push($data, array(
				'date'       => $line[0],
				'time'       => $line[1],
				'company_id' => $line[2],
				'type'       => $line[3],
				'route'      => $line[4],
				'ip'         => $line[5],
			));
		}

		return View::make('heartbeats.panel', array(
			'client_ip' => Request::getClientIp(),
			'data'      => json_encode($data),
			'companies' => json_encode(Company::lists('name', 'id'))
		));
	}
}
