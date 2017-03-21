<?php
namespace Framework\Views\Pages;

/**
 * Lewis Lancaster 2016
 *
 * Class Index
 *
 * @package Framework\Views\Pages
 */

use Framework\Exceptions\ViewException;
use Framework\Views\Structures\Page;

class Index implements Page
{

	/**
	 * The index page has a special algorithm which allows it to access the root. Only the index can do this.
	 *
	 * @return array
	 */

	public function mapping()
	{

		return array(
			[
				'/', 'page'
			],
			[
				'/index/', 'page'
			]
		);
	}

	/**
	 * Default page
	 */

	public function page()
	{

	    throw new ViewException('fuck');
	}
}