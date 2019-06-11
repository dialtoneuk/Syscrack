<?php
	declare(strict_types=1);
	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 15/07/2018
	 * Time: 13:06
	 */

	namespace Framework\Application\UtilitiesV2;


	use Framework\Application;

	/**
	 * Class Mailer
	 * @package Framework\Application\UtilitiesV2
	 */
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
		 * @throws \Error
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
		 * @throws \Error
		 */

		public function send($recipricant, $subject, $content, $from = null)
		{

			try
			{

				if ( Application::globals()->MAILER_IS_SMTP )
					$this->phpmailer->isSMTP();

				$this->setConfiguration($this->getConfiguration());
				$this->phpmailer->addAddress($recipricant);
				$this->phpmailer->Subject = $subject;

				if (!Application::globals()->MAILER_IS_HTML)
					$this->phpmailer->isHTML(false);
				else
					$this->phpmailer->isHTML(true);

				if ($from == null)
					$this->phpmailer->setFrom(Application::globals()->MAILER_FROM_ADDRESS, Application::globals()->MAILER_FROM_USER);
				else
					$this->phpmailer->setFrom(Application::globals()->MAILER_FROM_ADDRESS, $from);

				$this->phpmailer->Body = $content;
				$this->phpmailer->AltBody = "Please view this email from an updated web browser.";

				$this->phpmailer->send();
			} catch (\Exception $error)
			{

				throw new \Error($error->getMessage());
			}

			return true;
		}

		/**
		 * @param $configuration
		 *
		 * @throws \Error
		 */

		public function setConfiguration($configuration)
		{

			foreach ($configuration as $key => $value)
			{

				if (isset($this->phpmailer->$key) == false)
					throw new \Error("Invalid key:" . $key);

				$this->phpmailer->$key = $value;
			}
		}

		/**
		 * @return bool|string
		 * @throws \Error
		 */

		public function getConfiguration()
		{

			if (file_exists(SYSCRACK_ROOT . Application::globals()->MAILER_CONFIGURATION_FILE) == false)
				throw new \Error("Configuration file invalid");

			return (file_get_contents(SYSCRACK_ROOT . Application::globals()->MAILER_CONFIGURATION_FILE));
		}

		/**
		 * @throws \Error
		 */

		public function preload()
		{

			$dir = new DirectoryOperator($this->path());

			if ($dir->isEmpty())
				throw new \Error("No templates");

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
		 * @throws \Error
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
		 * @throws \Error
		 */

		public function parse(array $values, $template)
		{

			if ($this->exist($template) == false)
				throw new \Error("Template does not exist");

			if (isset($values["url"]) == false)
				$values["url"] = Application::globals()->SYSCRACK_URL_ADDRESS;

			if (isset($values["contact"]) == false)
				$values["contact"] = Application::globals()->MAILER_CONTACT_ADDRESS;

			$contents = $this->get($template);

			foreach ($values as $key => $value)
			{

				if (strpos($contents, $this->parseKey($key)) !== false )
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
		 * @throws \Error
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
		 * @throws \Error
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
		 * @throws \Error
		 */

		public function getRawTemplate($template)
		{

			if (file_exists(SYSCRACK_ROOT . $this->path($template)) == false)
				throw new \Error("Unknown template");

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
		 * @param null $template
		 *
		 * @return string
		 */

		private function path($template = null)
		{

			if ($template == null)
				return (Application::globals()->MAILER_TEMPLATES_ROOT);

			return (Application::globals()->MAILER_TEMPLATES_ROOT . $template . ".html");
		}
	}