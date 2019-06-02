<?php

	namespace Framework\Syscrack\Game\Operations;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Hack
	 *
	 * @package Framework\Syscrack\Game\Operations
	 */

	use Framework\Application\Settings;
	use Framework\Exceptions\SyscrackException;
	use Framework\Syscrack\Game\AddressDatabase;
	use Framework\Syscrack\Game\Bases\BaseOperation;
	use Framework\Syscrack\Game\Preferences;

	class Hack extends BaseOperation
	{

		/**
		 * @var AddressDatabase;
		 */

		protected static $addressdatabase;

		/**
		 * @var Preferences
		 */

		protected static $preferences;

		/**
		 * Hack constructor.
		 */

		public function __construct()
		{

			if (isset(self::$addressdatabase) == false)
				self::$addressdatabase = new AddressDatabase();

			if( isset( self::$preferences ) == false )
				self::$preferences = new Preferences();

			parent::__construct();
		}

		/**
		 * The configuration of this operation
		 */

		public function configuration()
		{

			return array(
				'allowsoftware' => false,
				'allowlocal' => false,
				'requiresoftware' => false,
				'requireloggedin' => false,
				'elevated' => true,
			);
		}

		/**
		 * @param null $ipaddress
		 *
		 * @return string
		 */

		public function url($ipaddress = null)
		{

			return ('game/internet/' . $ipaddress);
		}

		/**
		 * @param $timecompleted
		 * @param $computerid
		 * @param $userid
		 * @param $process
		 * @param array $data
		 *
		 * @return bool
		 */

		public function onCreation( $timecompleted, $computerid, $userid, $process, array $data )
		{

			if( $this->checkData( $data, ["ipaddress"] ) == false )
				return false;
			else
			{

				$target = self::$internet->getComputer( $data["ipaddress"] );

				if( self::$computer->hasType( $computerid, 'cracker') == false )
					$this->formError("You need to install a cracker in order to hack into computers");
				else
				{

					if( self::$preferences->hasSoftwarePreference( $userid, $computerid,'cracker') == false )
						$cracker = $this->getHighestLevelSoftware( $computerid, 'cracker');
					else
						$cracker = self::$software->getSoftware( self::$preferences->getSoftwarePreference( $userid, $computerid,'cracker') );

					if( self::$computer->hasType( $target->computerid, 'hasher') == false )
						return true;
					else
					{

						$hasher = $this->getHighestLevelSoftware( $target->computerid, 'hasher');

						if( $hasher->level > $cracker->level )
							return false;

						return true;
					}
				}
			}

			return false;
		}

		/**
		 * Called when this process request is created
		 *
		 * @param $timecompleted
		 *
		 * @param $computerid
		 *
		 * @param $userid
		 *
		 * @param $process
		 *
		 * @param array $data
		 *
		 * @return mixed
		 */

		/**
		public function onCreation($timecompleted, $computerid, $userid, $process, array $data)
		{

			if ($this->checkData($data, ['ipaddress']) == false)
				return false;

			if (self::$computer->getComputer($computerid)->ipaddress == $data['ipaddress'])
				return false;


			if (self::$addressdatabase->hasAddress($data['ipaddress'], $userid))
				return false;

			if (self::$computer->hasType($computerid, Settings::setting('syscrack_software_cracker_type'), true) == false)
				return false;

			$victimid = $this->getComputerId( $data['ipaddress'] );

			if( self::$computer->hasType( $victimid, Settings::setting('syscrack_software_hasher_type'), true ) )
			{

				$hasher = $this->getHighestLevelSoftware( $victimid, Settings::setting('syscrack_software_hasher_type') );
				$cracker = $this->getHighestLevelSoftware( $computerid, Settings::setting('syscrack_software_cracker_type') );

				if( empty( $hasher ) )
					return true;

				if( empty( $cracker ) )
					return false;

				if( @$cracker["level"] > $hasher["level"] )
					return true;

				return false;
			}
			else
				return true;
		}
		 **/

		/**
		 * @param $timecompleted
		 * @param $timestarted
		 * @param $computerid
		 * @param $userid
		 * @param $process
		 * @param array $data
		 *
		 * @return bool|mixed
		 */

		public function onCompletion($timecompleted, $timestarted, $computerid, $userid, $process, array $data)
		{

			if ($this->checkData($data, ['ipaddress']) == false)
				throw new SyscrackException();

			if (self::$internet->ipExists($data['ipaddress']) == false )
				$this->formError("Computer has changed IP Address");
			else
			{

				self::$addressdatabase->addAddress($data['ipaddress'], $userid);

				//TODO: Rewrite statistics

				/**
				if (Settings::setting('syscrack_statistics_enabled') == true)
					self::$statistics->addStatistic('hacks');
				**/

				if (isset($data['redirect']) == false)
					return true;
				else
					return ($data['redirect']);
			}

		}

		/**
		 * Gets the completion speed of this action
		 *
		 * @param $computerid
		 *
		 * @param $ipaddress
		 *
		 * @param null $softwareid
		 *
		 * @return int
		 */

		public function getCompletionSpeed($computerid, $ipaddress, $softwareid = null)
		{

			return $this->calculateProcessingTime($computerid, Settings::setting('syscrack_hardware_cpu_type'), Settings::setting('syscrack_operations_hack_speed'), $softwareid);
		}
	}