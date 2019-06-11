<?php
	declare(strict_types=1);
	/**
	 * Created by PhpStorm.
	 * User: newsy
	 * Date: 08/05/2019
	 * Time: 12:50
	 */

	namespace Framework\Syscrack\Game;


	/**
	 * Class Tool
	 * @package Framework\Syscrack\Game
	 */
	class Tool
	{

		protected $inputs = [];
		protected $requirements = [];
		protected $action = "view";
		public $class = "default";
		public $description = "";
		public $icon = "flash";

		/**
		 * Tool constructor.
		 *
		 * @param string $description
		 * @param string $class
		 */
		public function __construct($description = "", $class = "default")
		{

			$this->description = $description;
			$this->class = $class;
		}

		/**
		 * @return bool
		 */
		public function hasInputs()
		{

			return (empty($this->inputs) == false);
		}

		/**
		 * @return bool
		 */
		public function hasRequirements()
		{

			return (empty($this->requirements) == false);
		}

		/**
		 * @param string $action
		 */
		public function setAction(string $action)
		{

			$this->action = $action;
		}

		/**
		 * @return string
		 */
		public function getAction()
		{

			return ($this->action);
		}

		/**
		 * @return array
		 */
		public function getInputs()
		{

			return ($this->inputs);
		}

		/**
		 * @return array
		 */
		public function getRequirements()
		{

			return ($this->requirements);
		}

		public function isConnected()
		{

			$this->requirements['connected'] = true;
		}

		public function isExternal()
		{

			$this->requirements['external'] = true;
		}

		public function softwareAction()
		{

			$this->requirements['software_action'] = true;
		}

		public function hide()
		{

			$this->requirements['hide'] = true;
		}

		public function hacked()
		{

			$this->requirements['hacked'] = true;
		}

		public function admin()
		{

			$this->requirements['admin'] = true;
		}

		public function localAllowed()
		{

			$this->requirements['local'] = true;
		}


		public function unhacked()
		{

			$this->requirements['hacked'] = false;
		}

		/**
		 * @param $panel
		 */
		public function panel( $panel )
		{

			$this->requirements['panel'] = $panel;
		}

		/**
		 * @param string $type
		 */
		public function isComputerType(string $type)
		{

			$this->requirements['type'] = $type;
		}

		/**
		 * @param string $type
		 */
		public function preferenceType(string $type)
		{

			$this->requirements['preferencetype'] = $type;
		}


		/**
		 * @param string $type
		 */
		public function hasSoftwareInstalled(string $type)
		{

			$this->requirements['software'] = $type;
		}

		/**
		 * @param string $type
		 * @param $value
		 */
		public function setRequirement(string $type, $value)
		{

			$this->requirements[$type] = $value;
		}

		/**
		 * @param $name
		 * @param $type
		 * @param string $value
		 * @param string $placeholder
		 * @param string $class
		 * @param bool $html
		 */
		public function addInput($name, $type, $value = "", $placeholder = "", $class = "form-control", $html = true): void
		{

			if ($html)
				$html = $this->html([
					'name' => $name,
					'type' => $type,
					'value' => $value,
					'placeholder' => $placeholder,
					'class' => $class
				]);
			else
				$html = null;

			$this->inputs[$name] = [
				'name' => $name,
				'type' => $type,
				'value' => $value,
				'html' => $html
			];
		}

		/**
		 * @param array $values
		 *
		 * @return string
		 */
		private function html(array $values): string
		{

			$html = "<input %s >";
			$str = "";

			foreach ($values as $key => $value)
				$str .= $key . "=" . sprintf('"%s" ', $value);

			return (sprintf($html, $str));
		}
	}