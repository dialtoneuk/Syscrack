<?php
    namespace Framework\Syscrack\Game\Softwares;

/**
 * Lewis Lancaster 2017
 *
 * Class Riddle
 *
 * @package Framework\Syscrack\Game\Softwares
 */

use Framework\Application\Render;
use Framework\Application\Utilities\PostHelper;
use Framework\Syscrack\Game\BaseClasses\Software as BaseClass;
use Framework\Syscrack\Game\Riddles;
use Framework\Syscrack\Game\Structures\Software as Structure;

class Riddle extends BaseClass implements Structure
{

    /**
     * The configuration of this Structure
     *
     * @return array
     */

    public function configuration()
    {

        return array(
            'uniquename'    => 'riddle',
            'extension'     => '.exe',
            'type'          => 'riddle',
            'viewable'      => true,
            'removable'    => true,
            'installable'   => true,
            'executable'    => true,
            'keepdata'      => true
        );
    }

    public function onExecuted( $softwareid, $userid, $computerid )
    {

        if ( PostHelper::hasPostData() == false )
        {

            $software = $this->software->getSoftware( $softwareid );

            if ( empty( $software->data ) )
            {

                $this->redirectError('Sorry, this riddle is invalid and corrupted.');
            }

            $data = json_decode( $software->data, true );

            if ( isset($data['riddleid'] ) == false || isset( $data['computerid'] ) == false )
            {

                $this->redirectError('Sorry, this riddle is invalid and corrupted.');
            }

            if ( $this->computers->computerExists( $data['computerid'] ) == false )
            {

                $this->redirectError('Sorry, this riddle is invalid and corrupted.');
            }

            Render::view('syscrack/operations/operations.riddle', array( 'riddleid' => $data['riddleid']));

            exit;
        }
        else
        {

            $riddles = new Riddles();

            if ( PostHelper::checkForRequirements(['answer', 'riddleid'] ) == false )
            {

                $this->redirectError('Missing information');
            }
            else
            {

                if ( $riddles->hasRiddle( PostHelper::getPostData('riddleid', true )  ) == false )
                {

                    $this->redirectError('Incorrect information');
                }
                else
                {

                    if ( $riddles->checkRiddleAnswer( PostHelper::getPostData('riddleid', true ), PostHelper::getPostData('answer') ) )
                    {


                        $software = $this->software->getSoftware( $softwareid );
                        $data = json_decode( $software->data, true );
                        $computer = $this->computers->getComputer( $data['computerid'] );

                        $this->redirectSuccess('game/internet/' . $computer->ipaddress );
                    }
                    else
                    {

                        $this->redirectError('Sorry, that was the incorrect answer!');
                    }
                }
            }
        }
    }

    public function onInstalled( $softwareid, $userid, $computerid )
    {

        return null;
    }

    public function onUninstalled($softwareid, $userid, $computerid)
    {

        return null;
    }

    public function onCollect( $softwareid, $userid, $computerid, $timeran )
    {

        return null;
    }

    public function getExecuteCompletionTime($softwareid, $computerid)
    {

        return null;
    }

    /**
     * Default size of 16.0
     *
     * @return float
     */

    public function getDefaultSize()
    {

        return 16.0;
    }

    /**
     * Default level of 2.2
     *
     * @return float
     */

    public function getDefaultLevel()
    {

        return 2.2;
    }
}