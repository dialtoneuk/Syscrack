<?php
	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 15/07/2018
	 * Time: 13:06
	 */

	namespace Framework\Application\UtilitiesV2;


	class Mailer
	{

		protected $phpmailer;
		protected $templates = [];
		protected $identifier = "%";

		/**
		 * Mailer constructor.
		 *
		 * @param bool $auto_load
		 * @param bool $exceptions
		 *
		 * @throws \RuntimeException
		 */

		public function __construct($auto_load = true, $exceptions = true)
		{

			$this->phpmailer = new \PHPMailer($exceptions);

			if ($auto_load)
				$this->preload();
		}

		/**
		 * @param $recipricant
		 * @param $subject
		 * @param $content
		 * @param null $from
		 *
		 * @return bool
		 * @throws \RuntimeException
		 */

		public function send($recipricant, $subject, $content, $from = null)
		{

			try
			{

				if (MAILER_IS_SMTP)
					$this->phpmailer->isSMTP();

				$this->setConfiguration($this->getConfiguration());
				$this->phpmailer->addAddress($recipricant);
				$this->phpmailer->Subject = $subject;

				if (!MAILER_IS_HTML)
					$this->phpmailer->isHTML(false);
				else
					$this->phpmailer->isHTML(true);

				if ($from == null)
					$this->phpmailer->setFrom(MAILER_FROM_ADDRESS, MAILER_FROM_USER);
				else
					$this->phpmailer->setFrom(MAILER_FROM_ADDRESS, $from);

				$this->phpmailer->Body = $content;
				$this->phpmailer->AltBody = "Please view this email from an updated web browser.";

				$this->phpmailer->send();
			} catch (\Exception $error)
			{

				throw new \RuntimeException($error->getMessage());
			}

			return true;
		}

		/**
		 * @param $configuration
		 *
		 * @throws \RuntimeException
		 */

		public function setConfiguration($configuration)
		{

			foreach ($configuration as $key => $value)
			{

				if (isset($this->phpmailer->$key) == false)
					throw new \RuntimeException("Invalid key:" . $key);

				$this->phpmailer->$key = $value;
			}
		}

		/**
		 * @return bool|string
		 * @throws \RuntimeException
		 */

		public function getConfiguration()
		{

			if (file_exists(SYSCRACK_ROOT . MAILER_CONFIGURATION_FILE) == false)
				throw new \RuntimeException("Configuration file invalid");

			return (file_get_contents(SYSCRACK_ROOT . MAILER_CONFIGURATION_FILE));
		}

		/**
		 * @throws \RuntimeException
		 */

		public function preload()
		{

			$dir = new DirectoryOperator($this->path());

			if ($dir->isEmpty())
				throw new \RuntimeException("No templates");

			$files = $dir->search([".html"]);

			foreach ($files as $file)
			{

				$contents = file_get_contents($file);

				if (empty($contents))
					continue;

				$info = pathinfo($file);
				$this->templates[$info["filename"]] = $contents;
			}
		}

		/**
		 * @param array $values
		 *
		 * @return array
		 * @throws \RuntimeException
		 */

		public function paraseAll(array $values)
		{
			if ($this->hasPreload() == false)
				$this->preload();

			foreach ($this->templates as $template)
			{

				$this->parse($values, $template);
			}

			return ($this->templates);
		}

		/**
		 * @param array $values
		 * @param $template
		 *
		 * @return mixed
		 * @throws \RuntimeException
		 */

		public function parse(array $values, $template)
		{

			if ($this->exist($template) == false)
				throw new \RuntimeException("Template does not exist");

			if (isset($values["url"]) == false)
				$values["url"] = SYSCRACK_URL_ADDRESS;

			if (isset($values["contact"]) == false)
				$values["contact"] = MAILER_CONTACT_ADDRESS;

			$contents = $this->get($template);

			foreach ($values as $key => $value)
			{

				if (str_contains($contents, $this->parseKey($key)))
					str_replace($this->parseKey($key), $contents, $value);
			}

			if ($contents === $this->templates[$template])
				return ($contents);

			$this->templates[$template] = $contents;

			return ($contents);
		}

		/**
		 * @param $template
		 *
		 * @return mixed
		 * @throws \RuntimeException
		 */

		public function get($template)
		{

			if ($this->hasPreload() == false)
				$this->preload();

			return ($this->templates[$template]);
		}

		/**
		 * @param $template
		 *
		 * @return bool
		 * @throws \RuntimeException
		 */

		public function exist($template)
		{

			if ($this->hasPreload() == false)
				$this->preload();

			return (isset($this->templates[$template]));
		}

		/**
		 * @param $template
		 *
		 * @return bool|string
		 * @throws \RuntimeException
		 */

		public function getRawTemplate($template)
		{

			if (file_exists(SYSCRACK_ROOT . $this->path($template)) == false)
				throw new \RuntimeException("Unknown template");

			return (file_get_contents(SYSCRACK_ROOT . $this->path($template)));

		}

		/**
		 * @param $key
		 *
		 * @return string
		 */

		private function parseKey($key)
		{

			return ($this->identifier . $key);
		}

		/**
		 * @return bool
		 */

		private function hasPreload()
		{

			return (empty($this->templates));
		}

		/**
		 * @param $file
		 *
		 * @return mixed
		 */

		private function omitRoot($file)
		{

			return (str_replace(SYSCRACK_ROOT . $this->path(), "", $file));
		}

		/**
		 * @param null $template
		 *
		 * @return string
		 */

		private function path($template = null)
		{

			if ($template == null)
				return (MAILER_TEMPLATES_ROOT);

			return (MAILER_TEMPLATES_ROOT . $template . ".html");
		}
	}