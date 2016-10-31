<?php 

namespace Scubawhere;

use Scubawhere\Entities\Company;

/**
 * Taken from the Singleton paragraph of http://www.phptherightway.com/pages/Design-Patterns.html
 */
class Context
{
	/**
	* @var Company The reference to the instance of this singleton class
	*/
	private static $instance;

	/**
	 * Sets the instance of this singleton class.
	 *
	 * @param Company $company The company model to set the context to
	 */
	public static function set($company)
	{
		if(!($company instanceof Company))
			throw new \Exception('Context must be set with an instance of the Company class!');

		static::$instance = $company;
	}

	/**
	 * Checks if the instance is set.
	 *
	 * @return boolean If the company instance is set
	 */
	public static function check()
	{
		return null !== static::$instance;
	}

	/**
	* Returns the instance of this singleton class.
	*
	* @return Company The company instance
	*/
	public static function get()
	{
		if (null === static::$instance) {
			throw new \Exception('Context has not been set!');
		}

		return static::$instance;
	}

	/**
	* Protected constructor to prevent creating a new instance of the
	* *Singleton* via the `new` operator from outside of this class.
	*/
	protected function __construct() {}

	/**
	* Private clone method to prevent cloning of the instance of the
	* *Singleton* instance.
	*
	* @return void
	*/
	private function __clone() {}

	/**
	* Private unserialize method to prevent unserializing of the *Singleton*
	* instance.
	*
	* @return void
	*/
	private function __wakeup() {}
}
