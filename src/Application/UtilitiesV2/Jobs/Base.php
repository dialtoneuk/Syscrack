<?php
	declare( strict_types=1);

	namespace Framework\Application\UtilitiesV2\Jobs;

	use Framework\Application\UtilitiesV2\Debug;
	use Framework\Application\UtilitiesV2\Interfaces\Job;

	/**
	 * Class Base
	 * @package Framework\Application\UtilitiesV2\Jobs
	 */
	class Base implements Job
	{

		/**
		 * @param array $data
		 *
		 * @return bool
		 */

		public function execute(array $data): bool
		{

			if( Debug::isCMD() )
				Debug::msg("Executed: " . @$data["classname"] );

			return( true );
		}

		/**
		 * @return int
		 */

		public function frequency(): int
		{

			return( 0 );
		}
	}