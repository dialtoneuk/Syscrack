<?php
    namespace Framework\Syscrack\Game\BaseClasses;

/**
 * Lewis Lancaster 2017
 *
 * Class Software
 *
 * @package Framework\Syscrack\Game
 */

    use Framework\Syscrack\Game\Computer;
    use Framework\Syscrack\Game\Hardware;
    use Framework\Syscrack\Game\Internet;
    use Framework\Syscrack\Game\Softwares;

    class Software
{

        /**
         * @var Softwares
         */

        public $softwares;

        /**
         * @var Hardware
         */

        public $hardware;

        /**
         * @var Computer
         */

        public $computer;

        /**
         * @var Internet
         */

        public $internet;

        /**
         * Software constructor.
         *
         * @param bool $createclasses
         */

        public function __construct($createclasses=true )
        {

            if( $createclasses )
            {

                $this->softwares = new Softwares();

                $this->hardware = new Hardware();

                $this->computer = new Computer();

                $this->internet = new Internet();
            }
        }
}