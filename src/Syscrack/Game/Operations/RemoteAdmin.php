<?php
namespace Framework\Syscrack\Game\Operations;

/**
 * Lewis Lancaster 2017
 *
 * Class RemoteAdmin
 *
 * @package Framework\Syscrack\Game\Operations
 */

use Framework\Application\Settings;
use Framework\Application\Utilities\PostHelper;
use Framework\Exceptions\SyscrackException;
use Framework\Syscrack\Game\BaseClasses\Operation as BaseClass;
use Framework\Syscrack\Game\Finance;
use Framework\Syscrack\Game\Structures\Operation as Structure;

class RemoteAdmin extends BaseClass implements Structure
{

    /**
     * @var Finance
     */

    protected $finance;

    /**
     * View constructor.
     */

    public function __construct()
    {

        parent::__construct( true );

        if( isset( $this->finance ) == false )
        {

            $this->finance = new Finance();
        }
    }

    /**
     * Returns the configuration
     *
     * @return array
     */

    public function configuration()
    {

        return array(
            'allowsoftwares'    => false,
            'allowlocal'        => false,
            'requiresoftwares'  => false,
            'requireloggedin'   => false,
            'allowpost'         => true
        );
    }

    /**
     * Called when this process request is created
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
     * @return mixed
     */

    public function onCreation($timecompleted, $computerid, $userid, $process, array $data)
    {

        if( $this->checkData( $data, ['ipaddress'] ) == false )
        {

            return false;
        }

        $computer = $this->internet->getComputer( $data['ipaddress'] );

        if( $computer->type != Settings::getSetting('syscrack_computer_bank_type') )
        {

            return false;
        }
        if( $this->finance->hasCurrentActiveAccount() == false )
        {

            return false;
        }
        else
        {

            if( $this->finance->accountNumberExists( $this->finance->getCurrentActiveAccount() ) == false )
            {

                if( Settings::getSetting('syscrack_operations_bank_clearonfail') )
                {

                    $this->finance->setCurrentActiveAccount( null );
                }

                $this->redirectError('The account seems to not exist anymore, maybe it has been deleted', $this->getRedirect( $data['ipaddress'] ) );
            }

            if( $this->finance->getByAccountNumber( $this->finance->getCurrentActiveAccount() )->computerid !== $computer->computerid )
            {

                return false;
            }
        }

        return true;
    }

    /**
     * Renders the view page
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

        if( $this->checkData( $data, ['ipaddress'] ) == false )
        {

            throw new SyscrackException();
        }



        $this->getRender('operations/operations.bank.adminaccount', array( 'ipaddress' => $data['ipaddress'], 'userid' => $userid, 'accountnumber' => $this->finance->getCurrentActiveAccount() ), true );
    }

    /**
     * Gets the completion time
     *
     * @param $computerid
     *
     * @param $ipaddress
     *
     * @param null $softwareid
     *
     * @return null
     */

    public function getCompletionSpeed($computerid, $ipaddress, $softwareid=null )
    {

        return null;
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
     * Calls when the operation receives a post request
     *
     * @param $data
     *
     * @return bool
     */

    public function onPost( $data, $ipaddress, $userid )
    {

        if( PostHelper::hasPostData() == false )
        {

            $this->redirect( $this->getRedirect( $ipaddress ) . '/remoteadmin' );
        }

        if( PostHelper::checkForRequirements( ['action'] ) == false )
        {

            $this->redirectError('Missing information', $this->getRedirect( $ipaddress ) . '/remoteadmin');
        }
        else
        {

            if( $this->finance->hasCurrentActiveAccount() == false )
            {

                $this->redirectError('Please hack an account first', $this->getRedirect( $ipaddress ) );
            }

            if( $this->finance->accountNumberExists( $this->finance->getCurrentActiveAccount() ) == false )
            {

                if( Settings::getSetting('syscrack_operations_bank_clearonfail') )
                {

                    $this->finance->setCurrentActiveAccount( null );
                }

                $this->redirectError('The account seems to not exist anymore, maybe it has been deleted', $this->getRedirect( $data['ipaddress'] ) );
            }

            $action = PostHelper::getPostData('action');

            if( $action == 'transfer' )
            {

                if( PostHelper::checkForRequirements( ['accountnumber','ipaddress','amount'] ) )
                {

                    $accountnumber = PostHelper::getPostData('accountnumber');
                    $targetipaddress = PostHelper::getPostData('ipaddress');
                    $amount = PostHelper::getPostData('amount');

                    if( is_numeric( $amount ) == false )
                    {

                        $this->redirectError('Please enter a numeral value of cash to transfer', $this->getRedirect( $ipaddress ) . '/remoteadmin' );
                    }

                    $amount = abs( $amount );

                    if( empty( $amount ) || $amount == 0 )
                    {

                        $this->redirectError('Please enter an amount bigger than zero', $this->getRedirect( $ipaddress ) . '/remoteadmin' );
                    }

                    if( $this->finance->accountNumberExists( $accountnumber ) == false )
                    {

                        $this->redirectError('This number does not exist', $this->getRedirect( $ipaddress ) . '/remoteadmin' );
                    }

                    if( $this->internet->ipExists( $targetipaddress ) == false )
                    {

                        $this->redirectError('Failed to connect to bank of address given, 404 not found', $this->getRedirect( $ipaddress ) . '/remoteadmin');
                    }

                    $account = $this->finance->getByAccountNumber( $accountnumber );

                    $activeaccount = $this->finance->getByAccountNumber( $this->finance->getCurrentActiveAccount() );

                    if( $this->computer->getComputer( $account->computerid )->ipaddress !== $targetipaddress )
                    {

                        $this->redirectError('Account does not exist at remote database', $this->getRedirect( $ipaddress ) . '/remoteadmin' );
                    }

                    if( $this->finance->canAfford( $activeaccount->computerid, $activeaccount->userid, $amount ) == false )
                    {

                        $this->redirectError('This account cannot afford this transaction', $this->getRedirect( $ipaddress ) . '/remoteadmin' );
                    }

                    $this->finance->deposit( $account->computerid, $account->userid, $amount );

                    $this->finance->withdraw( $activeaccount->computerid, $activeaccount->userid, $amount );

                    $this->logActions('Transfered ' . Settings::getSetting('syscrack_currency') . number_format( $amount ) . ' from (' . $this->finance->getCurrentActiveAccount() . ') to (' . $account->accountnumber . ') to bank <' . $targetipaddress . '>',
                        $this->computer->getCurrentUserComputer(),
                        $ipaddress);

                    $this->redirectSuccess( $this->getRedirect( $ipaddress ) . '/remoteadmin');
                }
                else
                {

                    $this->redirectError('Missing information', $this->getRedirect( $ipaddress ) . '/remoteadmin' );
                }
            }
            elseif( $action == "disconnect" )
            {

                $this->finance->setCurrentActiveAccount( null );

                $this->redirect( $this->getRedirect( $ipaddress ) );
            }
        }

        return true;
    }
}
