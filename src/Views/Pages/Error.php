<?php
namespace Framework\Views\Pages;

/**
 * Lewis Lancaster 2016
 *
 * Class Error
 *
 * @package Framework\Views\Pages
 */

use Framework\Views\Structures\Page;
use Flight;

class Error implements Page
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
                '/error/', 'page'
            ]
        );
    }

    /**
     * Default page
     */

    public function page()
    {

        Flight::render('error/page.error');
    }
}