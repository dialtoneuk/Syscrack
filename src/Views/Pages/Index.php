<?php
    namespace Framework\Views\Pages;

    /**
     * Lewis Lancaster 2016
     *
     * Class Index
     *
     * @package Framework\Views\Pages
     */

    use Flight;
    use Framework\Application\Container;
    use Framework\Application\Session;
    use Framework\Views\Structures\Page;
    use Framework\Views\BaseClasses\Page as BaseClass;

    class Index extends BaseClass implements Page
    {

        /**
         * Index constructor.
         */

        public function __construct()
        {

            parent::__construct( false );

            if (session_status() !== PHP_SESSION_ACTIVE)
            {

                session_start();
            }

            Container::setObject('session', new Session());
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

            Flight::render('syscrack/page.index');
        }
    }