<?php
	declare(strict_types=1);
	/**
	 * Created by PhpStorm.
	 * User: newsy
	 * Date: 05/05/2019
	 * Time: 14:19
	 */

	namespace Framework\Tests;

	use Framework\Syscrack\Game\Metadata;

	/**
	 * Class MetadataTest
	 * @package Framework\Tests
	 */
	class MetadataTest extends BaseTestCase
	{

		/**
		 * @var Metadata
		 */

		protected static $metadata;
		protected static $computerid = 1000;

		public static function setUpBeforeClass(): void
		{

			self::$metadata = new Metadata();

			try
			{
				self::$computerid += random_int(0, self::$computerid);
			} catch (\Exception $e)
			{
				//No
			}

			parent::setUpBeforeClass();
		}

		public static function tearDownAfterClass(): void
		{

			if (self::$metadata->exists(self::$computerid))
				self::$metadata->delete(self::$computerid);

			parent::tearDownAfterClass();
		}

		public function testCreate()
		{

			self::$metadata->create(self::$computerid, Metadata::generateData("Test",
				"npc", [], [], []));

			static::assertTrue(self::$metadata->exists(self::$computerid));
		}


		public function testExists()
		{

			static::assertTrue(self::$metadata->exists(self::$computerid));
		}

		public function testGet()
		{

			static::assertNotEmpty(self::$metadata->get(self::$computerid));
		}
	}
