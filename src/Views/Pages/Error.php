<?php
    namespace Framework\Views\Pages;

    /**
     * Lewis Lancaster 2016
     *
     * Class Error
     *
     * @package Framework\Views\Pages
     */

    use Flight;
    use Framework\Views\Structures\Page;
    use Framework\Views\BaseClasses\Page as BaseClass;

    class Error extends BaseClass implements Page
    {

        /**
         * Error constructor.
         */

        public function __construct()
        {

            parent::__construct( false );
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
                    '/error/', 'page'
                ],
                [
                    '/error/404/', 'error404'
                ],
                [
                    '/error/database/', 'errorDatabase'
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

        public function error404()
        {

            Flight::render('error/page.404');
        }

        public function errorDatabase()
        {

            Flight::render('error/page.database');
        }
    }