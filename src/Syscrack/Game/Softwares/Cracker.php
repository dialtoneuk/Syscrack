<?php
    namespace Framework\Syscrack\Game\Softwares;

    /**
     * Lewis Lancaster 2017
     *
     * Class Cracker
     *
     * @package Framework\Syscrack\Game\Softwares
     */

    use Framework\Syscrack\Game\BaseClasses\BaseSoftware;

    class Cracker extends BaseSoftware
    {

        /**
         * The configuration of this Structure
         *
         * @return array
         */

        public function configuration()
        {

            return array(
                'uniquename'    => 'cracker',
                'extension'     => '.crc',
                'type'          => 'cracker',
                'icon'          => 'glyphicon-copyright-mark',
                'installable'   => true,
                'executable'    => true
            );
        }
    }