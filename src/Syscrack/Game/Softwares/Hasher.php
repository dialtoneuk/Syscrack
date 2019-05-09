<?php
    namespace Framework\Syscrack\Game\Softwares;

    /**
     * Lewis Lancaster 2017
     *
     * Class Hasher
     *
     * @package Framework\Syscrack\Game\Softwares
     */

    use Framework\Syscrack\Game\BaseClasses\BaseSoftware;


    class Hasher extends BaseSoftware
    {

        /**
         * The configuration of this Structure
         *
         * @return array
         */

        public function configuration()
        {

            return array(
                'uniquename'    => 'hasher',
                'extension'     => '.hash',
                'type'          => 'hasher',
                'icon'          => 'glyphicon-lock',
                'installable'   => true,
                'executable'    => false,
            );
        }
    }