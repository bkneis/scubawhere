<?php

/*
 * Class AdminController
 * 
 * Controller used to return the admin page of the RMS. This api route
 * uses the 'admin' middleware that only allows the admin user controlled
 * by scubawhere access. No customer should be able to access this!
 */
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

		// Automatically remove data that is older than 30 days
		$time30DaysAgo = date('Y-m-d H:i:s', strtotime('-30 days'));
		$itemsDeleted  = false;
		foreach($log as $index => $line)
		{
			if(substr($line, 0, 19) < $time30DaysAgo)
			{
				unset($log[$index]);
				$itemsDeleted = true;
			}
			else
				break;
		}

		if($itemsDeleted)
			file_put_contents(storage_path() . '/logs/heartbeats.log', implode("\n", $log)."\n", LOCK_EX);


		$data = array();

		// Extract required values
		foreach($log as $line)
		{
			// Split line by space
			$line = explode(" ", $line);

			// Assign values to $data array
			// Example log line: '2015-10-13 17:58:20 1 n #dashboard 81.156.246.247'
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
