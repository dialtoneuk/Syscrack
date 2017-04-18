<?php
    namespace Framework\Syscrack\Game;

    /**
     * Lewis Lancaster 2017
     *
     * Class Statistics
     *
     * @package Framework\Syscrack\Game
     */

    use Framework\Application\Settings;
    use Framework\Application\Utilities\FileSystem;
    use Framework\Exceptions\SyscrackException;

    class Statistics
    {

        /**
         * @var array
         */

        protected $statistics = [];

        /**
         * Statistics constructor.
         *
         * @param bool $autoread
         */

        public function __construct( $autoread=true )
        {

            if( $autoread == true )
            {

                $this->readStatistics();
            }
        }

        /**
         * Gets a statistic
         *
         * @param $statistic
         *
         * @return mixed
         */

        public function getStatistic( $statistic )
        {

            return $this->statistics[ $statistic ];
        }

        /**
         * Adds a statistic
         *
         * @param $statistic
         */

        public function addStatistic( $statistic )
        {

            if( isset( $this->statistics[ $statistic ] ) )
            {

                throw new SyscrackException();
            }

            $this->statistics[ $statistic ] = 0;
        }

        /**
         * Saves our statistics to file
         */

        public function saveStatistics()
        {

            FileSystem::writeJson( Settings::getSetting('syscrack_statistics_file'), $this->statistics );
        }

        /**
         * Returns true if we have statistics
         *
         * @return bool
         */

        public function hasStatistics()
        {

            if( FileSystem::fileExists( Settings::getSetting('syscrack_statistics_file') ) == false )
            {

                return false;
            }

            if( empty( $this->readStatistics() ) )
            {

                return false;
            }

            return true;
        }

        /**
         * Reads the statistics from the file
         *
         * @return array|mixed
         */

        private function readStatistics()
        {

            if( empty( $this->statistics ) == false )
            {

                return $this->statistics;
            }

            $this->statistics = FileSystem::readJson( Settings::getSetting('syscrack_statistics_file') );

            return $this->statistics;
        }
    }