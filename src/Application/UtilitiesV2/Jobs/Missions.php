<?php
	declare( strict_types=1 );

	namespace Framework\Application\UtilitiesV2\Jobs;

	use Framework\Application\UtilitiesV2\Jobs\Base;

	/**
	 * Class Missions
	 * @package Framework\Application\UtilitiesV2\Jobs
	 */
	class Missions extends Base
	{

		/**
		 * @param array $data
		 *
		 * @return bool
		 */

		public function execute(array $data): bool
		{

			return( parent::execute($data) );
		}

		/**
		 * @return int
		 */

		public function frequency(): int
		{
			return( 0 );
		}
	}