<?php
namespace Framework\Syscrack\Game\Softwares;

/**
 * Lewis Lancaster 2017
 *
 * Class Honeypot
 *
 * @package Framework\Syscrack\Game\Collector
 *
 * It is very important that you do not autoload the software classes inside a software class.... this will cause a loop...
 */

use Framework\Application\Settings;
use Framework\Syscrack\Game\Viruses;
use Framework\Syscrack\Game\AddressDatabase;
use Framework\Syscrack\Game\Structures\Software as Structure;
use Framework\Syscrack\Game\Software as BaseClass;

class Collector extends BaseClass implements Structure
{

    /**
     * @var Viruses
     */

    protected $viruses;

    /**
     * @var AddressDatabase
     */

    protected $addressdatabase;

    /**
     * Collector constructor.
     */

    public function __construct()
    {

        parent::__construct();

        $this->viruses = new Viruses();

        $this->addressdatabase = new AddressDatabase();
    }

    /**
     * The configuration of this Structure
     *
     * @return array
     */

    public function configuration()
    {

        return array(
            'uniquename'    => 'collector',
            'extension'     => '.col',
            'type'          => 'collector',
            'installable'   => false
        );
    }

    /**
     * Collects the users viruses
     *
     * @param $softwareid
     *
     * @param $userid
     *
     * @param $computerid
     *
     * @return array|bool
     *
     * //TOOD: Improve this to instead or returning 'false', returning a sort of class which can display an error
     */

    public function onExecuted( $softwareid, $userid, $computerid )
    {

        if( $this->softwares->getSoftware( $softwareid )->type !== Settings::getSetting('syscrack_collector_type') )
        {

            return false;
        }

        $addresses = $this->addressdatabase->getDatabase( $userid );

        if( empty( $addresses ) )
        {

            return false;
        }

        $profit = [];

        foreach( $addresses as $address )
        {

            if( $this->viruses->hasVirusesOnComputer( $this->internet->getComputer( $address['ipaddress'] )->computerid, $userid ) == false )
            {

                continue;
            }

            $viruses = $this->viruses->getVirusesOnComputer( $this->internet->getComputer( $address['ipaddress'] )->computerid, $userid );

            foreach( $viruses as $virus )
            {

                $result = $this->softwares->executeSoftwareMethod( $this->softwares->getSoftware( $virus->softwareid ), 'onCollect', array(
                    'softwareid'    => $virus->softwareid,
                    'userid'        => $userid,
                    'computerid'    => $computerid,
                    'timeran'       => time() - $viruses->lastmodified
                ));

                if( $viruses->uniquename == Settings::getSetting('syscrack_vminer_uniquename') )
                {

                    if( empty( $result ) || $result == null )
                    {

                        $profit['btc'][] = array(
                            'profit'    => $result,
                            'timeran'   => time() - $viruses->lastmodified,
                            'ipaddress' => $address['ipaddress']
                        );
                    }
                    else
                    {

                        $profit['cash'][] = array(
                            'profit'    => $result,
                            'timeran'   => time() - $viruses->lastmodified,
                            'ipaddress' => $address['ipaddress']
                        );
                    }
                }
                else
                {

                    if( empty( $result ) || $result == null )
                    {

                        $profit['cash'][] = array(
                            'profit'    => Settings::getSetting('syscrack_collector_btc_amount'),
                            'timeran'   => time() - $viruses->lastmodified,
                            'ipaddress' => $address['ipaddress']
                        );
                    }
                    else
                    {

                        $profit['cash'][] = array(
                            'profit'    => $result,
                            'timeran'   => time() - $viruses->lastmodified,
                            'ipaddress' => $address['ipaddress']
                        );
                    }
                }

                $this->viruses->updateVirusModified( $virus->softwareid );
            }
        }

        return $profit;
    }

    public function onInstalled( $softwareid, $userid, $computerid )
    {

        return;
    }

    public function onCollect( $softwareid, $userid, $computerid, $timeran )
    {

        return;
    }

    /**
     * Default size of 10.0
     *
     * @return float
     */

    public function getDefaultSize()
    {

        return 10.0;
    }

    /**
     * Default level of 1.0
     *
     * @return float
     */

    public function getDefaultLevel()
    {

        return 1.0;
    }
}