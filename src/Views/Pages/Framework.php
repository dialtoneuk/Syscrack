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
    use Framework\Application\Container;
    use Framework\Application\Settings;
    use Framework\Views\BaseClasses\Page as BaseClass;
    use Framework\Views\Structures\Page as Structure;

    class Framework extends BaseClass implements Structure
    {

        /**
         * Framework constructor.
         */

        public function __construct()
        {

            parent::__construct( false, true );
        }

        /**
         * Returns the pages mapping
         *
         * @return array
         */

        public function mapping()
        {

            return array(
                [
                    '/framework/', 'redirect'
                ],
                [
                    '/framework/error/database/', 'databaseError'
                ],
                [
                    '/framework/error/session/', 'sessionError'
                ],
                [
                    '/framework/404/', 'notFound'
                ]
            );
        }

        public function redirect()
        {

            Flight::redirect( Settings::getSetting('controller_index_root') . Settings::getSetting('controller_index_page') );
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

        /**
         * Renders the session error page
         */

        public function sessionError()
        {

            if( Container::hasObject('middlewares') == false )
            {

                Flight::notFound(); exit;
            }
            else
            {

                if( Container::getObject('middlewares')->getResult('sessioncheck') == true )
                {

                    Flight::notFound(); exit;
                }
            }

            Flight::render('error/page.session');
        }
    }