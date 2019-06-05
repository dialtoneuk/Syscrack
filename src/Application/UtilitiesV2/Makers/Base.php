<?php

	namespace Framework\Application\UtilitiesV2\Makers;

	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 31/08/2018
	 * Time: 21:48
	 */

	use Framework\Application;
	use Framework\Application\UtilitiesV2\Conventions\FileData;
	use Framework\Application\UtilitiesV2\Conventions\TokenData;
	use Framework\Application\UtilitiesV2\Interfaces\Maker;
	use Framework\Application\UtilitiesV2\TokenReader;

	abstract class Base implements Maker
	{

		/**
		 * @var FileData
		 */

		protected $template;

		/**
		 * @var TokenReader
		 */

		protected $tokenreader;

		/**
		 * Base constructor.
		 */

		public function __construct()
		{

			$this->tokenreader = new TokenReader();
		}

		/**
		 * @param FileData $template
		 */

		public function before(FileData $template = null): void
		{

			if ($template == null)
				throw new \Error("template is null and has not been set by inheritor");

			if ($this->exist($template->path) == false)
				throw new \Error("path does not exist: " . $template->path);

			$this->template = $template;
		}

		/**
		 * @return array
		 */

		public function requiredTokens(): array
		{

			return (["classname", "namespace"]);
		}

		/**
		 * @param TokenData $values
		 * @param $path
		 *
		 * @return FileData|bool
		 */

		public function make(TokenData $values, $path): FileData
		{

			if (count(explode(".", $path)) == 1)
				$path = $path . $values->arrayValue("values", "classname") . ".php";

			$result = $this->tokenreader->parse($this->template, $values, $path);

			if ($result == false)
				if (TokenReader::hasLastError())
					throw TokenReader::getLastError();

			return ($result);
		}

		/**
		 * @return string
		 */

		public function filepath(): string
		{

			return("src/");
		}

		/**
		 * @return string
		 */

		public function namespace(): string
		{

			return( Application::globals()->SYSCRACK_NAMESPACE_ROOT . "Application\\Syscrack\\" );
		}

		/**
		 * @param $path
		 *
		 * @return bool
		 */

		private function exist($path)
		{

			return (file_exists(SYSCRACK_ROOT . $path));
		}
	}