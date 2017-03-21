<?php
namespace Framework\Views\Structures;

/**
 * Lewis Lancaster 2016
 *
 * Interface Page
 *
 * @package Framework\Views\Structures
 */

interface Page
{

	/**
	 * Tells the controller where to point too with what specific URI strings
	 *
	 * @return mixed
	 */

	public function mapping();
}