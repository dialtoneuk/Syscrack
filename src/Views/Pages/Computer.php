<?php
    namespace Framework\Views\Pages;

    /**
     * Lewis Lancaster 2016
     *
     * Class Computer
     *
     * @package Framework\Views\Pages
     */

    use Flight;
    use Framework\Application\Container;
    use Framework\Application\Session;
    use Framework\Views\BaseClasses\Page as BaseClass;
    use Framework\Views\Structures\Page as Structure;

    class Computer extends BaseClass implements Structure
    {

        /**
         * Computer constructor.
         */

        public function __construct()
        {

            parent::__construct();

            if (session_status() !== PHP_SESSION_ACTIVE)
            {

                session_start();
            }

            if (Container::hasObject('session') == false)
            {

                Container::setObject('session', new Session());
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
                    '/computer/', 'page'
                ],
                [
                    '/computer/log/', 'computerLog'
                ],
                [
                    '/computer/software/', 'computerSoftware'
                ],
                [
                    '/computer/processes/', 'computerProcesses'
                ],
                [
                    '/computer/processes/@processid', 'computerViewProcess'
                ],
                [
                    '/computer/actions/@process', 'computerAction'
                ],
                [
                    '/computer/actions/@process/@softwareid', 'computerSoftwareAction'
                ]
            );
        }

        /**
         * Default page
         */

        public function page()
        {

            Flight::render('syscrack/page.computer');
        }

        public function computerLog()
        {


        }

        public function computerSoftware()
        {


        }

        public function computerProcesses()
        {


        }

        public function computerViewProcess()
        {

        }

        public function computerAction( $process )
        {


        }

        public function computerSoftwareAction( $process, $softwareid )
        {


        }
    }