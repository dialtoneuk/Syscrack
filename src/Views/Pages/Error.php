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
    use Framework\Views\BaseClasses\Page as BaseClass;
    use Framework\Views\Structures\Page;

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