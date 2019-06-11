<?php
	declare(strict_types=1);
	/**
	 * Created by PhpStorm.
	 * User: newsy
	 * Date: 05/06/2019
	 * Time: 01:33
	 */

	namespace Framework\Tests;

	use Framework\Application\Render;

	/**
	 * Class RenderTest
	 * @package Framework\Tests
	 */
	class RenderTest extends BaseTestCase
	{

		public function testProcessAssets()
		{

			$assets = Render::getAssets();
			$result = Render::processAssets( $assets );

			static::assertNotEmpty( $assets );
			static::assertNotEmpty( $result );
			static::assertArrayHasKey( "css", $assets );
			static::assertArrayHasKey( "css", $result );;
		}

		public function testGetAssets()
		{

			$assets = Render::getAssets();
			static::assertNotEmpty( $assets );
			static::assertArrayHasKey( "css", $assets );
		}
	}
