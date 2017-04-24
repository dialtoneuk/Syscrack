<?php
    namespace Framework\Views\Pages;

    /**
     * Lewis Lancaster 2017
     *
     * Class Framework
     *
     * @package Framework\Views\Pages
     */

    use Flight;
    use Framework\Views\BaseClasses\Page as BaseClass;
    use Framework\Views\Structures\Page as Structure;

    class Framework extends BaseClass implements Structure
    {

        /**
         * Framework constructor.
         */

        public function __construct()
        {

            parent::__construct( false );
        }

        /**
         * Returns the url routes
         *
         * @return array
         */

        public function mapping()
        {

            return array(
                [
                    '/framework/error/database/', 'databaseError'
                ],
                [
                    '/framework/error/404/', 'notFound'
                ]
            );
        }

        /**
         * Renders the 404 page
         */

        public function notFound()
        {

            Flight::render('error/page.404', array('page' => $this->getCurrentPage() ) );
        }

        /**
         * Renders the database error page
         */

        public function databaseError()
        {

            Flight::render('error/page.database');
        }
    }