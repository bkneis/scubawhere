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
	protected $description = 'Run the scheduler.';

	/**
	 * Current timestamp when command is called.
	 *
	 * @var integer
	 */
	protected $time;

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
			$this->info('I am running every 5 minutes!');
		});

		$this->finish();
	}

	protected function finish()
	{
		$this->info('Cron: ' . date('Y-m-d H:i:s') . ' | Execution time: ' . round(((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) * 1000), 3));
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
