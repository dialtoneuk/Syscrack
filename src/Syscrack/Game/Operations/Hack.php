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

	class Hack extends BaseOperation
	{

		/**
		 * @var AddressDatabase;
		 */

		protected static $addressdatabase;

		/**
		 * Hack constructor.
		 */

		public function __construct()
		{

			if (isset(self::$addressdatabase) == false)
				self::$addressdatabase = new AddressDatabase();

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

				$target = self::$internet->computer( $data["ipaddress"] );

				if( empty( $target ) || $target == null )
					return false;

				$cracker = $this->software( $computerid, 'cracker');

				if( empty( $cracker ) || $cracker === null )
					$this->formError('You need a cracker in order to hack into computers');
				else
				{

					$hasher = $this->software( $target->computerid, 'hasher');

					if( empty( $hasher ) || $hasher === null )
						return true;
					elseif( $cracker->level > $hasher->level )
						return true;
					else
						return false;
				}
			}

			return false;
		}

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

				if (isset($data['redirect']) == false)
					return true;
				else
					return ($data['redirect']);
			}

			return false;
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