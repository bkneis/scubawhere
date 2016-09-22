<?php

# Small cron job command for Laravel 4.2
# Inspired by Laravel 5's new upcoming scheduler (https://laravel-news.com/2014/11/laravel-5-scheduler)
#
# Author: Soren Schwert (GitHub: sisou)
#
# Requirements:
# =============
# PHP 5.4
# Laravel 4.2 ? (not tested with 4.1 or below)
# A desire to put all application logic into version control
#
# Installation:
# =============
# 1. Put this file into your app/commands/ directory and name it 'CronRunCommand.php'.
# 2. In your artisan.php file (found in app/start/), put this line: 'Artisan::add(new CronRunCommand);'.
# 3. On the server's command line, run 'php artisan cron:run'. If you see a message telling you the execution time, it works!
# 4. On your server, configure a cron job to call 'php-cli artisan cron:run >/dev/null 2>&1' and to run every five minutes (*/5 * * * *)
# 5. Observe your laravel.log file (found in app/storage/logs/) for messages starting with 'Cron'.
#
# Usage:
# ======
# 1. Have a look at the example provided in the fire() function.
# 2. Have a look at the available schedules below (starting at line 127).
# 4. Code your schedule inside the fire() function.
# 3. Done. Now go push your cron logic into version control!

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CronRunCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'cron:run';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Run the scheduler';

	/**
	 * Current timestamp when command is called.
	 *
	 * @var integer
	 */
	protected $timestamp;

	/**
	 * Hold messages that get logged
	 *
	 * @var array
	 */
	protected $messages = array();

	/**
	 * Specify the time of day that daily tasks get run (UTC)
	 *
	 * @var string [HH:MM]
	 */
	protected $runAt = '00:00';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->timestamp = time();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$this->everyFiveMinutes(function()
		{
			/**
			 * Set overdue reserved bookings to status='expired'
			 */
			$bookings = Booking::whereIn('status', ['initialised', 'reserved'])->with('company')->get();

			$ids_abandoned = array();
			$ids_expired   = array();

			foreach($bookings as $booking)
			{
				$now  = new DateTime('now', new DateTimeZone($booking->company->timezone));
				$test = new DateTime($booking->reserved_until, new DateTimeZone($booking->company->timezone));
				if($test < $now)
				{
					$booking->status === 'initialised' ? $ids_abandoned[] = $booking->id : $ids_expired[] = $booking->id;
				}
			}

			if(count($ids_abandoned) > 0)
			{
				// Create a string containing as many ?,?... as there are IDs
				$clause = implode(',', array_fill(0, count($ids_abandoned), '?'));
				DB::update("UPDATE bookings SET `status` = NULL, `updated_at` = NOW() WHERE `id` IN (" . $clause . ");", $ids_abandoned);
			}

			if(count($ids_expired) > 0)
			{
				// Create a string containing as many ?,?... as there are IDs
				$clause = implode(',', array_fill(0, count($ids_expired), '?'));
				// This query deliberately does not set the `status` to null or 'saved'.
				// This way, we still know which bookings where reserved but have expired.
				DB::update("UPDATE bookings SET `status` = 'expired', `updated_at` = NOW() WHERE `id` IN (" . $clause . ");", $ids_expired);
			}

			$this->messages[] = count($ids_abandoned) . ' bookings abandoned';
			$this->messages[] = count($ids_expired) . ' bookings expired';

			/**
			 * Delete all abandoned bookings older than 1h
			 */
			$before = date('Y-m-d H:i:s', time() - 1 * 60 * 60);
			$affectedRows = Booking::where('status', null)
			                        ->where('updated_at', '<', $before)
			                        ->delete();
			//$this->messages[] = $affectedRows . ' abandoned bookings deleted';

			/**
			 * Delete all expired bookings older than 24h
			 */
			
			$before = date('Y-m-d H:i:s', time() - 24 * 60 * 60);
			$affectedRows = Booking::where('status', 'expired')
			                        ->where('updated_at', '<', $before)
			                        ->delete();
			$this->messages[] = $affectedRows . ' expired bookings deleted';
	
		});

		/**
		 * 1. Calculate time that is 30 minutes ago using the mysql server's time
		 * 2. Get all bookings that have been initialised and are older than 30 minutes
		 * 3. Filter through those booking and get any that are being edited (thier refrence appends a _)
		 * 4. Remove any bookings that are being edited from the array to be deleted
		 * 5. Delete the rest of the old initialised bookings
		 *
		 * @todo Find out why the created_at timestamps are always behing by 1 hour
		 */
		$this->everyThirtyMinutes(function()
		{
			$q = DB::select(DB::raw('select now() as time'));
			$test_time = date('Y-m-d H:i:s', 
							strtotime('-1 hour -30 minutes', strtotime($q[0]->time)));
			$bookings_refs = Booking::select('reference')
									->where('status', '=', 'initialised')
									->where('created_at', '<', $test_time)
									->get()
									->map(function($obj) {
										return $obj->reference;
									});

			$edit_bookings_refs = $bookings_refs->map(function($obj) {
													return $obj . '_';
												})
												->toArray();

			$edit_bookings_refs = Booking::select('reference')
										 ->whereIn('reference', $edit_bookings_refs)
										 ->get()
										 ->map(function($obj) {
											 return substr($obj->reference, 0, -1);
										 })
										 ->toArray();

			$delete_bookings_refs = array_diff($bookings_refs->toArray(), $edit_bookings_refs);

			Booking::whereIn('reference', $delete_bookings_refs)->delete();

			$this->messages[] = count($delete_bookings_refs) . ' abandoned bookings deleted';
		});

		$this->hourly(function()
		{
			Artisan::call('auth:clear-reminders');
		});

		$this->daily(function()
		{
			$dateString = date('Y-m-d_H-i');
			Artisan::call('db:backup --database=mysql --destination=local --destinationPath=/' . $dateString . '.sql --compression=gzip');
		});

		// DO NOT REMOVE!
		$this->finish();
	}

	protected function finish()
	{
		// Write execution time and messages to the log
		$executionTime = round(((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) * 1000), 3);
		$msg = 'Cron: execution time: ' . $executionTime . ' | ' . implode(', ', $this->messages);
		Log::info($msg);
		$this->info($msg);
	}

	/**
	 * AVAILABLE SCHEDULES
	 */

	protected function everyFiveMinutes(callable $callback)
	{
		/* if((int) date('i', $this->timestamp) % 5 === 0) */ call_user_func($callback);
	}

	protected function everyTenMinutes(callable $callback)
	{
		if((int) date('i', $this->timestamp) % 10 === 0) call_user_func($callback);
	}

	protected function everyFifteenMinutes(callable $callback)
	{
		if((int) date('i', $this->timestamp) % 15 === 0) call_user_func($callback);
	}

	protected function everyThirtyMinutes(callable $callback)
	{
		if((int) date('i', $this->timestamp) % 30 === 0) call_user_func($callback);
	}

	/**
	 * Called every full hour
	 */
	protected function hourly(callable $callback)
	{
		if(date('i', $this->timestamp) === '00') call_user_func($callback);
	}

	/**
	 * Called every hour at the minute specified
	 *
	 * @param  integer $minute
	 */
	protected function hourlyAt(int $minute, callable $callback)
	{
		if((int) date('i', $this->timestamp) === $minute) call_user_func($callback);
	}

	/**
	 * Called every day
	 */
	protected function daily(callable $callback)
	{
		if(date('H:i', $this->timestamp) === $this->runAt) call_user_func($callback);
	}

	/**
	 * Called every day at the 24h-format time specified
	 *
	 * @param  string $time [HH:MM]
	 */
	protected function dailyAt(string $time, callable $callback)
	{
		if(date('H:i', $this->timestamp) === $time) call_user_func($callback);
	}

	/**
	 * Called every day at 12:00am and 12:00pm
	 */
	protected function twiceDaily(callable $callback)
	{
		if(date('h:i', $this->timestamp) === '12:00') call_user_func($callback);
	}

	/**
	 * Called every weekday
	 */
	protected function weekdays(callable $callback)
	{
		$days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
		if(in_array(date('D', $this->timestamp), $days) && date('H:i', $this->timestamp) === $this->runAt) call_user_func($callback);
	}

	protected function mondays(callable $callback)
	{
		if(date('D', $this->timestamp) === 'Mon' && date('H:i', $this->timestamp) === $this->runAt) call_user_func($callback);
	}

	protected function tuesdays(callable $callback)
	{
		if(date('D', $this->timestamp) === 'Tue' && date('H:i', $this->timestamp) === $this->runAt) call_user_func($callback);
	}

	protected function wednesdays(callable $callback)
	{
		if(date('D', $this->timestamp) === 'Wed' && date('H:i', $this->timestamp) === $this->runAt) call_user_func($callback);
	}

	protected function thursdays(callable $callback)
	{
		if(date('D', $this->timestamp) === 'Thu' && date('H:i', $this->timestamp) === $this->runAt) call_user_func($callback);
	}

	protected function fridays(callable $callback)
	{
		if(date('D', $this->timestamp) === 'Fri' && date('H:i', $this->timestamp) === $this->runAt) call_user_func($callback);
	}

	protected function saturdays(callable $callback)
	{
		if(date('D', $this->timestamp) === 'Sat' && date('H:i', $this->timestamp) === $this->runAt) call_user_func($callback);
	}

	protected function sundays(callable $callback)
	{
		if(date('D', $this->timestamp) === 'Sun' && date('H:i', $this->timestamp) === $this->runAt) call_user_func($callback);
	}

	/**
	 * Called once every week (basically the same as using sundays() above...)
	 */
	protected function weekly(callable $callback)
	{
		if(date('D', $this->timestamp) === 'Sun' && date('H:i', $this->timestamp) === $this->runAt) call_user_func($callback);
	}

	/**
	 * Called once every week at the specified day and time
	 *
	 * @param  string $day  [Three letter format (Mon, Tue, ...)]
	 * @param  string $time [HH:MM]
	 */
	protected function weeklyOn(string $day, string $time, callable $callback)
	{
		if(date('D', $this->timestamp) === $day && date('H:i', $this->timestamp) === $time) call_user_func($callback);
	}

	/**
	 * Called each month on the 1st
	 */
	protected function monthly(callable $callback)
	{
		if(date('d', $this->timestamp) === '01' && date('H:i', $this->timestamp) === $this->runAt) call_user_func($callback);
	}

	/**
	 * Called each year on the 1st of January
	 */
	protected function yearly(callable $callback)
	{
		if(date('m', $this->timestamp) === '01' && date('d', $this->timestamp) === '01' && date('H:i', $this->timestamp) === $this->runAt) call_user_func($callback);
	}

}
