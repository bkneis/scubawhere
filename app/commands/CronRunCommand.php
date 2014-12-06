<?php

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
	protected $time;

	/**
	 * Hold messages that get logged
	 *
	 * @var array
	 */
	protected $messages = array();

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->time = time();
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
			 * Unreserve all reserved bookings where reserved datetime is 15 minutes overdue
			 */
			$bookings = Booking::whereNotNull('reserved')->with('company')->get();
			$counter = 0;
			foreach($bookings as $booking)
			{
				$before = new DateTime('15 minutes ago', new DateTimeZone($booking->company->timezone));
				if(new DateTime($booking->reserved) < $before)
				{
					DB::update("UPDATE bookings SET `reserved` = NULL WHERE `id` = ?;", array($booking->id));
					$counter++;
				}
			}
			$this->messages[] = $counter . ' bookings unreserved';

			/**
			 * Delete all unsaved bookings older than 1h
			 */
			$before = date('Y-m-d H:i:s', time() - 60 * 60);
			$affectedRows = Booking::where('confirmed', false)
			                       ->whereNull('reserved')
			                       ->where('saved', false)
			                       ->where('updated_at', '<', $before)
			                       ->delete();
			$this->messages[] = $affectedRows . ' bookings deleted';
		});

		$this->finish();
	}

	protected function finish()
	{
		$executionTime = round(((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) * 1000), 3);
		Log::info('Cron: execution time: ' . $executionTime . ' | ' . implode(', ', $this->messages));
	}

	protected function everyFiveMinutes(callable $callback)
	{
		if((int) date('i', $this->time) % 5 === 0) call_user_func($callback);
	}

	protected function everyTenMinutes(callable $callback)
	{
		if((int) date('i', $this->time) % 10 === 0) call_user_func($callback);
	}

	protected function everyFifteenMinutes(callable $callback)
	{
		if((int) date('i', $this->time) % 15 === 0) call_user_func($callback);
	}

	protected function everyThirtyMinutes(callable $callback)
	{
		if((int) date('i', $this->time) % 30 === 0) call_user_func($callback);
	}

	/**
	 * Called every full hour
	 */
	protected function hourly(callable $callback)
	{
		if(date('i', $this->time) === '00') call_user_func($callback);
	}

	/**
	 * Called every day at midnight
	 */
	protected function daily(callable $callback)
	{
		if(date('H:i', $this->time) === '00:00') call_user_func($callback);
	}

	/**
	 * Called every hour at the minute specified
	 *
	 * @param  integer $minute
	 */
	protected function hourlyAt(int $minute, callable $callback)
	{
		if((int) date('i', $this->time) === $minute) call_user_func($callback);
	}

	/**
	 * Called every day at the 24h-format time specified
	 *
	 * @param  string $time [HH:MM]
	 */
	protected function dailyAt(string $time, callable $callback)
	{
		if(date('H:i', $this->time) === $time) call_user_func($callback);
	}

	/**
	 * Called every day at 12:00am and 12:00pm
	 */
	protected function twiceDaily(callable $callback)
	{
		if(date('h:i', $this->time) === '12:00') call_user_func($callback);
	}

	/**
	 * Called every weekday at midnight
	 */
	protected function weekdays(callable $callback)
	{
		$days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
		if(in_array(date('D', $this->time), $days) && date('H:i', $this->time) === '00:00') call_user_func($callback);
	}

	protected function mondays(callable $callback)
	{
		if(date('D', $this->time) === 'Mon' && date('H:i', $this->time) === '00:00') call_user_func($callback);
	}

	protected function tuesdays(callable $callback)
	{
		if(date('D', $this->time) === 'Tue' && date('H:i', $this->time) === '00:00') call_user_func($callback);
	}

	protected function wednesdays(callable $callback)
	{
		if(date('D', $this->time) === 'Wed' && date('H:i', $this->time) === '00:00') call_user_func($callback);
	}

	protected function thursdays(callable $callback)
	{
		if(date('D', $this->time) === 'Thu' && date('H:i', $this->time) === '00:00') call_user_func($callback);
	}

	protected function fridays(callable $callback)
	{
		if(date('D', $this->time) === 'Fri' && date('H:i', $this->time) === '00:00') call_user_func($callback);
	}

	protected function saturdays(callable $callback)
	{
		if(date('D', $this->time) === 'Sat' && date('H:i', $this->time) === '00:00') call_user_func($callback);
	}

	protected function sundays(callable $callback)
	{
		if(date('D', $this->time) === 'Sun' && date('H:i', $this->time) === '00:00') call_user_func($callback);
	}

	protected function weekly(callable $callback)
	{
		if(date('D', $this->time) === 'Mon' && date('H:i', $this->time) === '00:00') call_user_func($callback);
	}

	/**
	 * Called once every week at the specified day and time
	 *
	 * @param  string $day  [Three letter format (Mon, Tue, ...)]
	 * @param  string $time [HH:MM]
	 */
	protected function weeklyOn(string $day, string $time, callable $callback)
	{
		if(date('D', $this->time) === $day && date('H:i', $this->time) === $time) call_user_func($callback);
	}

	protected function monthly(callable $callback)
	{

	}

	protected function yearly(callable $callback)
	{

	}

}
