<?php

	namespace Framework\Application\UtilitiesV2;

	use Framework\Application;

	/**
	 * Class Colours
	 * @package Framework\Application\UtilitiesV2\Util
	 */
	class Colours
	{

		/**
		 * @param int $output
		 *
		 * @return string
		 * @throws \Error
		 */

		public static function generate($output)
		{

			switch ($output)
			{

				case Application::globals()->COLOURS_OUTPUT_HEX:
					return (dechex(rand(0x000000, 0xFFFFFF)));
					break;
				case Application::globals()->COLOURS_OUTPUT_RGB:
					return (rand(0, 255) . "," . rand(0, 255) . "," . rand(0, 255));
					break;
				default:
					throw new \Error("Unknown output");
					break;
			}
		}
	}