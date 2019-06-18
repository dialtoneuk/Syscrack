<?php
	declare( strict_types=1 );

	namespace Framework\Application\UtilitiesV2\Jobs;

	use Framework\Application\UtilitiesV2\Debug;

	/**
	 * Class Test
	 * @package Framework\Application\UtilitiesV2\Jobs
	 */
	class Test extends Base
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
			return( 60 * 60 );
		}
	}