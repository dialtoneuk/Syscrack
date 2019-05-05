<?php
    namespace Framework\Views\Pages;

    /**
     * Lewis Lancaster 2016
     *
     * Class Error
     *
     * @package Framework\Views\Pages
     */

    use Framework\Application\Render;
    use Framework\Views\BaseClasses\Page as BaseClass;
    use Framework\Views\Structures\Page as Structure;

    class Error extends BaseClass implements Structure
    {

        /**
         * Error constructor.
         */

        public function __construct()
        {

            parent::__construct( false, false );
        }

        /**
         * Returns the pages flight mapping
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

            Render::view('error/page.error', [], []);
        }
    }