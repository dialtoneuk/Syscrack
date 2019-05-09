<?php
    namespace Framework\Syscrack\Game\Computers;

    /**
     * Lewis Lancaster 2017
     *
     * Class FBI
     *
     * @package Framework\Syscrack\Game\Computers
     */


    class FBI extends Npc
    {

        /**
         * The configuration of this computer
         *
         * @return array
         */

        public function configuration()
        {

            return array(
                'installable' => true,
                'type'        => 'fbi'
            );
        }
    }