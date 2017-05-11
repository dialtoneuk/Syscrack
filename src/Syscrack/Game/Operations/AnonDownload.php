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
                'allowsoftwares'    => true,
                'allowanonymous'    => true,
                'requiresoftwares'  => true,
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

            if( $this->computer->getComputerType( $this->internet->getComputer( $data['ipaddress'] )->computerid ) !== Settings::getSetting('syscrack_downloadserver_type') )
            {

                $this->redirectError('This action can only be used on a download server', $this->getRedirect( $data['ipaddress'] ) );
            }

            $softwaredata = json_decode( $this->softwares->getSoftware( $data['softwareid'] )->data, true );

            if( isset( $softwaredata['allowanondownloads'] ) == false )
            {

                return false;
            }

            if( $softwaredata['allowanondownloads'] == false )
            {

                return false;
            }

            $software = $this->softwares->getSoftware( $data['softwareid'] );

            $softwares = $this->computer->getComputerSoftware( $this->computer->getCurrentUserComputer() );

            foreach( $softwares as $value )
            {

                if( $value['type'] == $software->type )
                {

                    if( $this->softwares->getSoftware( $value['softwareid'] )->softwarename == $software->softwarename )
                    {

                        $this->redirectError('You already have this software on your computer', $this->getRedirect( $data['ipaddress'] ) );
                    }
                }
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

            $softwareid = $this->softwares->copySoftware( $data['softwareid'], $this->computer->getCurrentUserComputer(), $userid );

            if( empty( $softwareid ) )
            {

                throw new SyscrackException();
            }

            $software = $this->softwares->getSoftware( $data['softwareid'] );

            if( $software == null )
            {

                throw new SyscrackException();
            }

            $this->computer->addSoftware( $this->computer->getCurrentUserComputer(), $softwareid, $software->type, $software->softwarename );

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

        public function getCompletionSpeed($computerid, $ipaddress, $softwareid )
        {

            if( $this->softwares->softwareExists( $softwareid ) == false )
            {

                throw new SyscrackException();
            }

            return $this->calculateProcessingTime( $computerid, Settings::getSetting('syscrack_download_type'), $this->softwares->getSoftware( $softwareid )->size / 10, $softwareid );
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