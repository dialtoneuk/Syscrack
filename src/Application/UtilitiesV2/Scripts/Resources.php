<?php
	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 22/07/2018
	 * Time: 01:01
	 */

	namespace Framework\Application\UtilitiesV2\Scripts;

	use Framework\Application\UtilitiesV2\Debug;
	use Framework\Application\UtilitiesV2\ResourceCombiner;
	use Framework\Application\UtilitiesV2\ResourceUnpacker;

	class Resources extends Base
	{

		/**
		 * @var ResourceUnpacker
		 */

		protected $unpacker;

		/**
		 * @var ResourceCombiner
		 */

		protected $packer;

		/**
		 * Resources constructor.
		 * @throws \Error
		 */

		public function __construct()
		{

			$this->unpacker = new ResourceUnpacker();
			$this->packer = new ResourceCombiner();
		}

		/**
		 * @param $arguments
		 *
		 * @return bool
		 * @throws \Error
		 */

		public function execute($arguments)
		{

			if ($arguments["action"] == "pack")
			{

				if (Debug::isCMD())
					Debug::echo("Packing resources", 3);

				$build = $this->packer->build();

				if (empty($build))
					throw new \Error("Build returned null ");

				$this->packer->save($build);

				if (Debug::isCMD())
					Debug::echo("Finished Packing resources", 3);
			}
			else if ($arguments["action"] == "unpack")
			{

				if (Debug::isCMD())
					Debug::echo("Unpacking resources", 3);

				$this->unpacker->process();
			}
			else
				throw new \Error("Unknown action");

			return (true);
		}

		/**
		 * @return array
		 */

		public function requiredArguments()
		{

			return ([
				"action"
			]);
		}


		/**
		 * @return array
		 */

		public function help()
		{
			return ([
				"arguments" => $this->requiredArguments(),
				"help" => "Unpacks and packs resources. action can either be pack or unpack."
			]);
		}
	}