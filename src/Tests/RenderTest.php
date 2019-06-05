<?php
	/**
	 * Created by PhpStorm.
	 * User: newsy
	 * Date: 05/06/2019
	 * Time: 01:33
	 */

	namespace Framework\Tests;

	use Framework\Application\Render;

	class RenderTest extends BaseTestCase
	{

		public function testProcessAssets()
		{

			$assets = Render::getAssets();
			$result = Render::processAssets( $assets );

			$this->assertNotEmpty( $assets );
			$this->assertNotEmpty( $result );
			$this->assertArrayHasKey( "css", $assets );
			$this->assertArrayHasKey( "css", $result );;
		}

		public function testGetAssets()
		{

			$assets = Render::getAssets();
			$this->assertNotEmpty( $assets );
			$this->assertArrayHasKey( "css", $assets );
		}
	}
