<?php
	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 06/08/2018
	 * Time: 01:50
	 */

	namespace Framework\Application\UtilitiesV2\AutoExecs;


	use Framework\Application\Session;
	use Framework\Application\Container;
	use Framework\Application\UtilitiesV2\Interfaces\AutoExec;
	use Framework\Database\Manager;

	abstract class Base implements AutoExec
	{

		/**
		 * @var Session
		 */

		protected static $session;

		/**
		 * @var Manager
		 */

		protected static $database;

		/**
		 * Base constructor.
		 * @throws \Error
		 */

		public function __construct()
		{

			if ( isset( self::$session ) == false )
				self::$session = new Session();

			if ( isset( self::$database ) == false )
				self::$database = new Manager( true );

			if( isset( self::$session ) == false )
				self::$session  = Container::getObject("session");
		}

		/**
		 * @param array $data
		 *
		 * @return mixed|void
		 */

		public function execute(array $data)
		{

			return;
		}
	}