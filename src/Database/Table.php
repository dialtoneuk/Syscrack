<?php
	declare(strict_types=1);

	namespace Framework\Database;

	/**
	 * Lewis Lancaster 2016
	 *
	 * Class Table
	 *
	 * @package Framework\Database
	 */

	use Exception;
	use Illuminate\Database\Query\Builder;
	use ReflectionClass;

	/**
	 * Class Table
	 * @package Framework\Database
	 */
	class Table
	{

		/**
		 * @var \Illuminate\Database\Capsule\Manager
		 */

		protected static $database;

		protected static $cache = [];

		/**
		 * Checks if we have initialized the database, if we have not, do it
		 *
		 * Table constructor.
		 */

		public function __construct()
		{

			try
			{

				if (Manager::getCapsule() === null)
					$this->initializeDatabase();

				self::$database = Manager::getCapsule();
			} catch (\Error $error)
			{


			}
		}

		/**
		 * @param null $table
		 *
		 * @return Builder
		 *
		 */

		final protected function getTable($table = null)
		{

			try
			{

				if ($table === null)
					$table = strtolower(self::getShortName(new ReflectionClass($this)));

				if (self::$database::table($table)->exists() == false)
				{
					if (self::$database::table($table)->exists() == false)
					{
						try
						{

							self::$database::table($table)->get();
						} catch (Exception $error)
						{

							throw new \Error();
						}

						return self::$database::table($table);
					}
					else
						return self::$database::table($table);
				}
				else
					return self::$database::table($table);

			} catch (\ReflectionException $exception)
			{

				throw new \Error();
			}
		}

		/**
		 * If this table has a database connection or not
		 *
		 * @return bool
		 */

		final private function hasConnection()
		{

			try
			{

				Manager::getCapsule()::connection();
			} catch (Exception $error)
			{

				return false;
			}

			return true;
		}

		/**
		 * Intializes a connection with the database
		 */

		final private function initializeDatabase()
		{

			$manager = new Manager();

			if ($this->hasConnection() == false)
			{

				throw new \Error();
			}

			unset($manager);
		}

		/**
		 * Gets the short name of a class
		 *
		 * @param ReflectionClass $reflectionclass
		 *
		 * @return string
		 */

		final private function getShortName(ReflectionClass $reflectionclass)
		{

			return $reflectionclass->getShortName();
		}
	}