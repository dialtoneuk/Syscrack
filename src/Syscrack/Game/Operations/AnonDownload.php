<?php
    namespace Framework\Syscrack\Game\Operations;

    /**
     * Lewis Lancaster 2017
     *
     * Class AnonDownload
     *
     * @package Framework\Syscrack\Game\Operations
     */

    use Framework\Application\Settings;
    use Framework\Exceptions\SyscrackException;
    use Framework\Syscrack\Game\BaseClasses\Operation as BaseClass;
    use Framework\Syscrack\Game\Structures\Operation as Structure;

    class AnonDownload extends BaseClass implements Structure
    {

        /**
         * AnonDownload constructor.
         */

        public function __construct()
        {

            parent::__construct( true );
        }

        /**
         * Allows for anonymous downloads
         *
         * @return array
         */

        public function configuration()
        {

            return array(
                'allowlocal'        => false,
                'allowsoftware'    => true,
                'allowanonymous'    => true,
                'requiresoftware'  => true,
                'requireloggedin'   => false,
            );
        }

        /**
         * Called when the operation is created
         *
         * @param $timecompleted
         *
         * @param $computerid
         *
         * @param $userid
         *
         * @param $process
         *
         * @param array $data
         *
         * @return bool
         */

        public function onCreation($timecompleted, $computerid, $userid, $process, array $data)
        {

            if( $this->checkData( $data ) == false )
            {

                return false;
            }

            if( $this->computers->getComputerType( $this->getComputerId( $data['ipaddress'] ) ) !== Settings::getSetting('syscrack_computers_download_type') )
            {

                $this->redirectError('This action can only be used on a download server', $this->getRedirect( $data['ipaddress'] ) );
            }

            if( $this->hasSpace( $this->computers->getCurrentUserComputer(), $this->software->getSoftware( $data['softwareid'] )->size ) == false )
            {

                $this->redirectError('Sorry, you dont have the free space for this download.', $this->getRedirect( $data['ipaddress'] ) );
            }

            if( $this->software->isAnonDownloadSoftware( $data['softwareid'] ) == false )
            {

                return false;
            }

            return true;
        }

        /**
         * Processes the operations completion
         *
         * @param $timecompleted
         *
         * @param $timestarted
         *
         * @param $computerid
         *
         * @param $userid
         *
         * @param $process
         *
         * @param array $data
         */

        public function onCompletion($timecompleted, $timestarted, $computerid, $userid, $process, array $data)
        {

            if( $this->checkData( $data ) == false )
            {

                throw new SyscrackException();
            }

            if( $this->internet->ipExists( $data['ipaddress'] ) == false )
            {

                $this->redirectError('Sorry, this ip address does not exist anymore', $this->getRedirect() );
            }

            if( $this->software->softwareExists( $data['softwareid'] ) == false )
            {

                $this->redirectError('Sorry, it looks like this software might have been deleted');;
            }

            $softwareid = $this->software->copySoftware( $data['softwareid'], $this->computers->getCurrentUserComputer(), $userid );

            if( empty( $softwareid ) )
            {

                throw new SyscrackException();
            }

            $software = $this->software->getSoftware( $softwareid );

            if( $software == null )
            {

                throw new SyscrackException();
            }

            $this->computers->addSoftware( $this->computers->getCurrentUserComputer(), $software->softwareid, $software->type, $software->softwarename );

            if( isset( $data['redirect'] ) )
            {

                $this->redirectSuccess( $data['redirect'] );
            }
            else
            {

                $this->redirectSuccess( $this->getRedirect( $data['ipaddress'] ) );
            }
        }

        /**
         * Gets the completion speed of this operation
         *
         * @param $computerid
         *
         * @param $ipaddress
         *
         * @param $softwareid
         *
         * @return int
         */

        public function getCompletionSpeed($computerid, $ipaddress, $softwareid=null )
        {

            if( $this->software->softwareExists( $softwareid ) == false )
            {

                throw new SyscrackException();
            }

            return $this->calculateProcessingTime( $computerid, Settings::getSetting('syscrack_hardware_download_type'), $this->software->getSoftware( $softwareid )->size / 5, $softwareid );
        }

        /**
         * Gets the custom data for this operation
         *
         * @param $ipaddress
         *
         * @param $userid
         *
         * @return array
         */

        public function getCustomData($ipaddress, $userid)
        {

            return array();
        }

        /**
         * Called upon a post request to this operation
         *
         * @param $data
         *
         * @param $ipaddress
         *
         * @param $userid
         *
         * @return bool
         */

        public function onPost($data, $ipaddress, $userid)
        {

            return true;
        }
    }