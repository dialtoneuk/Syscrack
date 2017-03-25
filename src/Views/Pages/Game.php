<?php
namespace Framework\Views\Pages;

/**
 * Lewis Lancaster 2016
 *
 * Class Index
 *
 * @package Framework\Views\Pages
 */

use Framework\Application\Container;
use Framework\Application\Session;
use Framework\Application\Settings;
use Framework\Application\Utilities\Log;
use Framework\Exceptions\ViewException;
use Framework\Views\Structures\Page;
use Flight;

class Game implements Page
{

    /**
     * Index constructor.
     */

    public function __construct()
    {

        session_start();

        Container::setObject('session',  new Session() );

        if( Container::getObject('session')->isLoggedIn() == false )
        {

            Flight::redirect( '/'. Settings::getSetting('controller_index_page') );
        }
    }

    /**
	 * The index page has a special algorithm which allows it to access the root. Only the index can do this.
	 *
	 * @return array
	 */

	public function mapping()
	{

		return array(
			[
				'/game/', 'page'
			]
		);
	}

	/**
	 * Default page
	 */

	public function page()
	{

	    Flight::render('syscrack/page.game');
	}
}