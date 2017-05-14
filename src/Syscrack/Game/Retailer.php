<?php
    namespace Framework\Syscrack\Game;

    /**
     * Lewis Lancaster 2017
     *
     * Class Retailer
     *
     * @package Framework\Syscrack
     */

    use Framework\Application\Settings;
    use Framework\Application\Utilities\FileSystem;
    use Framework\Exceptions\SyscrackException;

    class Retailer
    {

        /**
         * @var Computer
         */

        protected $computer;

        /**
         * Retailer constructor.
         */

        public function __construct()
        {

            if( isset( $this->computer ) == false )
            {

                $this->computer = new Computer();
            }
        }

        /**
         * Returns true if this computer is a retailer
         *
         * @param $computerid
         *
         * @return bool
         */

        public function isRetailer( $computerid )
        {

            if( $this->computer->getComputerType( $computerid ) !== Settings::getSetting('syscrack_computer_retailer_type') )
            {

                return false;
            }

            return true;
        }

        /**
         * Returns true if this computer has stock
         *
         * @param $computerid
         *
         * @return bool
         */

        public function hasStock( $computerid )
        {

            if( FileSystem::fileExists( $this->getFilePath( $computerid ) . 'stock.json' ) == false )
            {

                return false;
            }

            if( empty( $this->getFilePath( $computerid ) ) )
            {

                return false;
            }

            return true;
        }

        /**
         * Adds a stock item to the market
         *
         * @param $computerid
         *
         * @param $itemid
         *
         * @param array $data
         */

        public function addStockItem( $computerid, $itemid, array $data = ['name' => 'Default CPU', 'type' => 'hardware', 'hardware' => 'cpu', 'value' => '1000', 'price' => 100, 'quantity' => 10 ] )
        {

            $stock = $this->getStock( $computerid );

            if( isset( $stock[ $itemid ] ) )
            {

                throw new SyscrackException();
            }

            $stock[ $itemid ] = array_merge( $data, [
                'timeadded' => time()
            ]);

            $this->save( $computerid, $stock );
        }

        /**
         * Removes a stock item
         *
         * @param $computerid
         *
         * @param $itemid
         */

        public function removeStockItem( $computerid, $itemid )
        {

            $stock = $this->getStock( $computerid );

            if( isset( $stock[ $itemid ] ) == false )
            {

                throw new SyscrackException();
            }

            unset( $stock[ $itemid ] );

            $this->save( $computerid, $stock );
        }

        /**
         * Gets the retailers stock
         *
         * @param $computerid
         *
         * @return mixed
         */

        public function getStock( $computerid )
        {

            return FileSystem::readJson( $this->getFilePath( $computerid ) . 'stock.json' );
        }

        /**
         * Returns true if we have this stock items
         *
         * @param $computerid
         *
         * @param $itemid
         *
         * @return bool
         */

        public function hasStockItem( $computerid, $itemid )
        {

            $stock = $this->getStock( $computerid );

            if( isset( $stock[ $itemid ] ) == false )
            {

                return false;
            }

            return true;
        }

        /**
         * Saves the retailers stock
         *
         * @param $computerid
         *
         * @param array $data
         */

        private function save( $computerid, array $data=[] )
        {

            FileSystem::writeJson( $this->getFilePath( $computerid ) . 'stocks.json', $data );
        }

        /**
         * Gets the filepath
         *
         * @param $computerid
         *
         * @return string
         */

        private function getFilePath( $computerid )
        {

            return Settings::getSetting('syscrack_retailer_location') . $computerid . '/';
        }
    }