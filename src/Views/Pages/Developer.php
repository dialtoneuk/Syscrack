<?php
namespace Framework\Views\Pages;

/**
 * Lewis Lancaster 2016
 *
 * Class Developer
 *
 * @package Framework\Views\Pages
 */

use Framework\Application\Utilities\Log;
use Framework\Views\Structures\Page;
use Flight;

class Developer implements Page
{

    public function __construct()
    {

        Log::log('Developer Page initialized');

        Log::$disabled = true;
    }

    /**
     * The mapping of the class, on the left is the url of which will be mapped to the method, which is given on the right,
     * for example, if you a user goes to www.syscrack.com/developer/databasecreator/ the framework will first look for the
     * 'developer' class identifier, this is known as the page class ( which is this class! ), the mapping function is then called and the array
     * of mapped url are returned. We then simply compare the url, if it is '/databasecreator/' then the method databaseCreator will be
     * called!
     *
     * @return array
     */

	public function mapping()
	{

        Log::log('Mapping Pulled');

		return array(
            [
                '/developer/', 'index'
            ],
			[
				'/developer/connectioncreator/', 'connectionCreator'
			],
            [
                '/developer/connectiontester/', 'connectionTester'
            ],
            [
                '/developer/logger/', 'logger'
            ],
            [
                '/developer/logger/@id:[0-9]{0,9}/', 'loggerDetailed'
            ],
            [
                '/developer/disable/', 'disable'
            ],
            [
                '/developer/settingsmanager/', 'settingsManager'
            ],
            [
                '/developer/pageviewer/', 'pageViewer'
            ],
            [
                '/developer/databasemigrator/', 'databaseMigrator'
            ]
		);
	}

    /**
     * Renders the index page
     */

	public function index()
    {

        $this->renderPageFile('page.developer');
    }

    /**
     * Renders the database migrator page
     */

    public function databaseMigrator()
    {

        $this->renderPageFile('page.dbmigrator');
    }

    /**
     * Renders the page viewer page
     */

    public function pageViewer()
    {

        $this->renderPageFile('page.pageviewer');
    }

    /**
     * Renders the logger page
     */

    public function logger()
    {

        $this->renderPageFile('page.logger');
    }

    /**
     * Renders the detailed logger page
     *
     * @param $id
     */

    public function loggerDetailed( $id )
    {

        Flight::render( 'developer/page.logger.detailed', array( 'id' => $id ) );
    }

    /**
     * Renders the disable developer section page
     */

    public function disable()
    {

        $this->renderPageFile('page.disabledevelopersection');
    }

    /**
     * Renders the settings manager
     */

    public function settingsManager()
    {

        $this->renderPageFile('page.settingsmanager');
    }

    /**
     * Renders the connection creator
     */

	public function connectionCreator()
	{

		$this->renderPageFile('page.dbconnectioncreator');
	}

    /**
     * Renders the database creator
     */

    public function connectionTester()
    {

        $this->renderPageFile('page.dbconnectiontester');
    }

    /**
     * Tells flight to render the page but with a prefix to save typing
     *
     * @param $file
     */

	private function renderPageFile($file )
    {

        Flight::render( "developer/{$file}" );
    }
}