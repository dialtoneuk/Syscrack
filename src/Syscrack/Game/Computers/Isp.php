<?php
    namespace Framework\Syscrack\Game\Computers;

    /**
     * Lewis Lancaster 2017
     *
     * Class Isp
     *
     * @package Framework\Syscrack\Game\Computer
     */

    use Framework\Syscrack\Game\Structures\Computer as Structure;

    class Isp extends Npc implements Structure
    {

        /**
         * Npc constructor.
         */

        public function __construct()
        {

            parent::__construct();
        }

        /**
         * The configuration of this computer
         *
         * @return array
         */

        public function configuration()
        {

            return array(
                'installable' => true,
                'type'        => 'isp'
            );
        }
    }