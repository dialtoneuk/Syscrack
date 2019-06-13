<?php
	/**
	 * Created by PhpStorm.
	 * User: newsy
	 * Date: 13/06/2019
	 * Time: 16:04
	 */

	namespace Framework\Tests;

	use Framework\Application\Utilities\FileSystem;

	class FileSystemTest extends BaseTestCase
	{

		/**
		 *
		 */

		public function testRemoveFileExtension()
		{

			$result = FileSystem::removeFileExtension("/var/www/vhosts/hertz.world/httpdocs/src/Application/UtilitiesV2/Constructor.php");
			self::assertNotEmpty( $result );
		}
	}
