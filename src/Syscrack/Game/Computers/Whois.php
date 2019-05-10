<?php

	namespace Framework\Syscrack\Game\Computers;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Whois
	 *
	 * @package Framework\Syscrack\Game\Computers
	 */

	use Framework\Application\Settings;

	class Whois extends Npc
	{

		/**
		 * The configuration of this computer
		 *
		 * @return array
		 */

		public function configuration()
		{

			return array(
				'installable' => false,
				'type' => 'whois',
				'data' => true
			);
		}

		/**
		 * @param $computerid
		 * @param $userid
		 *
		 * @return array
		 */

		public function data($computerid, $userid)
		{

			$computers = [];
			$metaset = [];

			if (self::$metadata->exists($computerid))
				$metadata = self::$metadata->get($computerid);
			else
				$metadata = [];

			if (isset($metadata->whois))
				$array = $metadata->whois;
			else
				$array = Settings::setting("syscrack_whois_default_computers");

			foreach ($array as $computerid)
			{

				$computers[$computerid] = self::$computer->getComputer($computerid);

				if (self::$metadata->exists($computerid))
					$metaset[$computerid] = self::$metadata->get($computerid);
			}

			return ([
				"whois_computers" => $computers,
				"metaset" => $metaset
			]);
		}
	}