<?php
    namespace Framework\Syscrack\Game\Operations;

    /**
     * Lewis Lancaster 2017
     *
     * Class Upload
     *
     * @package Framework\Syscrack\Game\Operations
     */

    use Framework\Application\Settings;
    use Framework\Application\Utilities\PostHelper;
    use Framework\Exceptions\SyscrackException;
    use Framework\Syscrack\Game\BaseClasses\Operation as BaseClass;
    use Framework\Syscrack\Game\Structures\Operation as Structure;

    class Upload extends BaseClass implements Structure
    {

        public function configuration()
        {

            return array(
                'allowsoftware'    => false,
                'allowlocal'        => false,
                'requiresoftware'  => false,
                'requireloggedin'   => true,
                'allowpost'         => false,
                'allowcustomdata'   => true,
            );
        }

        public function onCreation($timecompleted, $computerid, $userid, $process, array $data)
        {

            if( $this->checkData( $data, ['ipaddress'] ) == false )
            {

                return false;
            }

            if( $this->checkCustomData( $data, ['softwareid'] ) == false )
            {

                return false;
            }

            if( self::$software->softwareExists( $data['custom']['softwareid'] ) == false )
            {

                return false;
            }

            $software = self::$software->getSoftware( $data['custom']['softwareid'] );

            if( $this->hasSpace( $this->getComputerId( $data['ipaddress'] ), $software->size ) == false )
            {

                $this->redirectError('Sorry, there is no free space to upload this software!', $this->getRedirect( $data['ipaddress'] ) );

                return false;
            }

            if( self::$computers->hasSoftware( $computerid, $software->softwareid ) == false )
            {

                return false;
            }

            if( self::$computers->isInstalled( $computerid, $software->softwareid ) == true )
            {

                $this->redirectError('Sorry, you cannot upload an installed file', $this->getRedirect( $data['ipaddress'] ) );
            }



            return true;
        }

        public function onCompletion($timecompleted, $timestarted, $computerid, $userid, $process, array $data)
        {

            if( $this->checkData( $data, ['ipaddress'] ) == false )
            {

                throw new SyscrackException();
            }

            if( self::$internet->ipExists( $data['ipaddress'] ) == false )
            {

                $this->redirectError('Sorry, this ip address does not exist anymore', $this->getRedirect() );
            }

            if( $this->checkCustomData( $data, ['softwareid'] ) == false )
            {

                throw new SyscrackException();
            }

            if( self::$software->softwareExists( $data['custom']['softwareid'] ) == false )
            {

                $this->redirectError('Sorry, it looks like this software might have been deleted', $this->getRedirect( $data['ipaddress'] ) );
            }

            $software = self::$software->getSoftware( $data['custom']['softwareid'] );

            if( self::$software->hasData( $software->softwareid ) == true && self::$software->keepData( $software->softwareid ) )
            {

                $softwaredata = self::$software->getSoftwareData( $software->softwareid );

                if( self::$software->checkSoftwareData( $software->softwareid, ['allowanondownloads'] ) == true )
                {

                    unset( $softwaredata['allowanondownloads'] );
                }

                if( self::$software->checkSoftwareData( $software->softwareid, ['editable'] ) == true )
                {

                    unset( $softwaredata['editable'] );
                }

                $new_softwareid = self::$software->copySoftware( $software->softwareid, $this->getComputerId( $data['ipaddress'] ), $userid, false, $softwaredata );
            }
            else
            {

                $new_softwareid = self::$software->copySoftware( $software->softwareid, $this->getComputerId( $data['ipaddress'] ), $userid );
            }

            self::$computers->addSoftware( $this->getComputerId( $data['ipaddress'] ), $new_softwareid, $software->type );

            if( self::$computers->hasSoftware( $this->getComputerId( $data['ipaddress'] ), $new_softwareid ) == false )
            {

                throw new SyscrackException();
            }

            $this->logUpload( $software->softwarename, $this->getComputerId( $data['ipaddress'] ), self::$computers->getComputer( $computerid )->ipaddress );

            $this->logLocal( $software->softwarename, $data['ipaddress'] );

            if( isset( $data['redirect'] ) )
            {

                $this->redirectSuccess( $data['redirect'] );
            }
            else
            {

                $this->redirectSuccess( $this->getRedirect( $data['ipaddress'] ) );
            }
        }

        public function getCompletionSpeed($computerid, $ipaddress, $softwareid = null)
        {

            return $this->calculateProcessingTime( $computerid, Settings::getSetting('syscrack_hardware_upload_type'), 10, $softwareid );
        }

        public function getCustomData($ipaddress, $userid)
        {

            if( PostHelper::hasPostData() == false )
            {

                return null;
            }

            if( PostHelper::checkForRequirements( ['softwareid'] ) == false )
            {

                return null;
            }

            return array(
                'softwareid' => PostHelper::getPostData('softwareid')
            );
        }

        public function onPost($data, $ipaddress, $userid)
        {
            // TODO: Implement onPost() method.
        }

        /**
         * @param $softwarename
         * @param $computerid
         * @param $ipaddress
         */

        private function logUpload( $softwarename, $computerid, $ipaddress )
        {

            $this->logToComputer('Uploaded file (' . $softwarename . ') on root', $computerid, $ipaddress );
        }

        /**
         * Logs to the local log
         *
         * @param $softwarename
         *
         * @param $ipaddress
         */

        private function logLocal( $softwarename, $ipaddress )
        {

            $this->logToComputer('Uploaded file (' . $softwarename . ') on <' . $ipaddress . '>', self::$computers->getComputer( self::$computers->getCurrentUserComputer() )->computerid, 'localhost' );
        }
    }