<?php
	namespace Mods\Base\Views;

	use Framework\Syscrack\Game\ModLoader;
	use Framework\Views\BaseClasses\Page;
	use Framework\Views\ControllerV2;

	class ModTest extends Page
	{

		public function mapping()
		{

			return [
				[
					'/modtest/', 'page'
				],
			];
		}

		public function page()
		{

			self::$user->getUser( 1, false );
		}

		public static function setup(bool $autoload=true, bool $session=true)
		{

			parent::setup($autoload, $session); // TODO: Change the autogenerated stub
		}
	}