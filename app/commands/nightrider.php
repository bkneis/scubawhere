<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class nightrider extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'command:nightrider';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Generate code using scubawheres software implementation policy';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$this->info('Generating code base');
		$name = $this->option('model');
		$model = strtolower($name);
		$models = $model . 's';
		$Model = ucfirst($model);
		$Models = $Model . 's';

		// Generate Repo
		$file_path = storage_path() . '/nightrider/Repo.php';
		$dest_path = app_path() . '/models/repositories/' . $Model . 'Repo.php';
		$this->generateFile($file_path, $dest_path, $model, $models, $Model, $Models);

		// Generate Controller
		$file_path = storage_path() . '/nightrider/Controller.php';
		$dest_path = app_path() . '/controllers/' . $Model . 'Controller_new.php';
		$this->generateFile($file_path, $dest_path, $model, $models, $Model, $Models);

		// Generate Repo Interface
		$file_path = storage_path() . '/nightrider/RepoInterface.php';
		$dest_path = app_path() . '/models/repositories/interfaces/' . $Model . 'RepoInterface.php';
		$this->generateFile($file_path, $dest_path, $model, $models, $Model, $Models);

		// Generate Service
		$file_path = storage_path() . '/nightrider/Service.php';
		$dest_path = app_path() . '/models/services/' . $Model . 'Service.php';
		$this->generateFile($file_path, $dest_path, $model, $models, $Model, $Models);

		// Generate Service Provider
		$file_path = storage_path() . '/nightrider/ServiceProvider.php';
		$dest_path = app_path() . '/models/services/providers/' . $Model . 'RepoServiceProvider.php';
		$this->generateFile($file_path, $dest_path, $model, $models, $Model, $Models);

		$this->info('Files generated. Dont forget to include the service providers in app/start !!!');
	}

	protected function generateFile($file_path, $dest_path, $model, $models, $Model, $Models)
	{
		if(file_exists($dest_path)) {
			$this->comment($dest_path . ' Already exists!');
			return;
		}
		$file_contents = file_get_contents($file_path);
		$file_contents = str_replace('{{model}}', $model, $file_contents);
		$file_contents = str_replace('{{Model}}', $Model, $file_contents);
		$file_contents = str_replace('{{models}}', $models, $file_contents);
		$file_contents = str_replace('{{Models}}', $Models, $file_contents);
		file_put_contents($dest_path, $file_contents);
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			//array('model', InputArgument::REQUIRED, 'The model name to create the classes for'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('model', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

}
