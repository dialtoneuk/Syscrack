<?php
namespace Framework\Syscrack\Game;

/**
 * Lewis Lancaster 2017
 *
 * Class Bitcoin
 *
 * @package Framework\Syscrack\Game
 */

use Framework\Application\Settings;
use Framework\Application\Utilities\Hashes;
use Framework\Database\Tables\Bitcoin as Database;
use Framework\Database\Tables\Computer;
use Framework\Exceptions\SyscrackException;
use Unirest\Request;

class Bitcoin
{

    /**
     * @var Database
     */

    protected $database;

    /**
     * @var Computer
     */

    protected $computers;

    /**
     * Bitcoin constructor.
     */

    public function __construct()
    {

        $this->database = new Database();

        $this->computers = new Computer();
    }

    /**
     * Gets the users wallet
     *
     * @param $userid
     *
     * @return mixed|null
     */

    public function getWallets( $userid )
    {

        return $this->database->getUserBitcoinWallets( $userid );
    }

    /**
     * Sets the current active wallet
     *
     * @param $walletid
     */

    public function setCurrentActiveWallet( $walletid )
    {

        if( session_status() !== PHP_SESSION_ACTIVE )
        {

            throw new SyscrackException();
        }

        $_SESSION['activewallet'] = $walletid;
    }

    /**
     * Gets the current active wallet
     *
     * @return mixed
     */

    public function getCurrentActiveWallet()
    {

        return $_SESSION['activewallet'];
    }

    /**
     * Returns true if the user has a current active wallet
     *
     * @return bool
     */

    public function hasCurrentActiveWallet()
    {

        if( isset( $_SESSION['activewallet'] ) == false )
        {

            return false;
        }

        if( $_SESSION['activewallet'] == null )
        {

            return false;
        }

        return true;
    }

    /**
     * Gets the bitcoin servers
     *
     * @return mixed|null
     */

    public function getBitcoinServers()
    {

        return $this->computers->getComputerByType( Settings::getSetting('syscrack_computers_bitcoin_type') );
    }

    /**
     * Gets the wallets by the server
     *
     * @param $computerid
     *
     * @return \Illuminate\Support\Collection|null
     */

    public function getWalletsByServer( $computerid )
    {

        return $this->database->getByServer( $computerid );
    }

    /**
     * Returns true if we have the wallet on the server
     *
     * @param $wallet
     *
     * @param $computerid
     *
     * @return bool
     */

    public function hasWalletOnServer( $wallet, $computerid )
    {

        $wallets = $this->getWalletsByServer( $computerid );

        foreach( $wallets as $value )
        {

            if( $value->wallet != $wallet )
            {

                return false;
            }
        }

        return true;
    }

    /**
     * Returns true if the computer is a bitcoin server
     *
     * @param $computerid
     *
     * @return bool
     */

    public function isBitcoinServer( $computerid )
    {

        if( $this->computers->getComputer( $computerid )->type != Settings::getSetting('syscrack_computers_bitcoin_type') )
        {

            return false;
        }

        return true;
    }

    /**
     * Deposits bitcoins into a wallet
     *
     * @param $wallet
     *
     * @param float $bitcoins
     */

    public function deposit( $wallet, float $bitcoins )
    {

        $this->database->updateWallet( $wallet, array( 'bitcoins' => $this->database->findBitcoinWallet( $wallet )->bitcoins + $bitcoins ) );
    }

    /**
     * Withdraws from a wallet
     *
     * @param $wallet
     *
     * @param float $bitcoins
     */

    public function withdraw( $wallet, float $bitcoins )
    {

        if( $this->canAfford( $wallet, $bitcoins ) == false )
        {

            throw new SyscrackException();
        }

        $this->database->updateWallet( $wallet, array( 'bitcoins' => $this->database->findBitcoinWallet( $wallet )->bitcoins - $bitcoins ) );
    }

    /**
     * Transfers bitcoins
     *
     * @param $wallet
     *
     * @param $receiver
     *
     * @param $bitcoins
     */

    public function transfer( $wallet, $receiver, $bitcoins )
    {

        $user = $this->database->findBitcoinWallet( $wallet );

        if( $this->canAfford( $wallet, $bitcoins ) == false )
        {

            throw new SyscrackException();
        }

        $this->database->updateWallet( $wallet, array( 'bitcoins' => $user->bitcoins - $bitcoins ) );

        $this->database->updateWallet( $wallet, array( 'bitcoins' =>
            $this->database->findBitcoinWallet( $receiver )->bitcoins + $bitcoins ) );
    }

    /**
     * Returns true if the user can afford ths transaction
     *
     * @param $wallet
     *
     * @param $price
     *
     * @return bool
     */

    public function canAfford( $wallet, $price )
    {

        $currentbitcoins = $this->database->findBitcoinWallet( $wallet )->bitcoins;

        if( $currentbitcoins - $price < 0 )
        {

            return false;
        }

        return true;
    }

    /**
     * Returns true if the user has a wallet
     *
     * @param $userid
     *
     * @return bool
     */

    public function hasWallet( $userid )
    {

        if( $this->database->getUserBitcoinWallets( $userid ) == null )
        {

            return false;
        }

        return true;
    }

    /**
     * Checks that there isn't an unbalance in the market
     *
     * @return bool
     */

    public function checkExchange()
    {

        if( $this->getBitcoinBuyPrice() < $this->getBitcoinSellPrice() )
        {

            return false;
        }

        return true;
    }

    /**
     * Attempts to get the price of bitcoin to sell
     *
     * @return null|int
     */

    public function getBitcoinSellPrice()
    {

        if( Settings::getSetting('syscrack_bitcoin_live') == false )
        {

            return Settings::getSetting('syscrack_bitcoin_sellprice');
        }

        $result = json_decode( Request::post( Settings::getSetting('syscrack_bitcoin_url') )->body, true );

        if( isset( $result[ strtoupper( Settings::getSetting('syscrack_bitcoin_country') ) ] ) == false )
        {

            throw new SyscrackException();
        }

        return $result[ strtoupper( Settings::getSetting('syscrack_bitcoin_country') ) ]['sell'];
    }

    /**
     * Creates a new wallet
     *
     * @param $userid
     *
     * @param $computerid
     */

    public function createWallet( $userid, $computerid )
    {

        $array = array(
            'userid'        => $userid,
            'wallet'        => $this->randomBytes(),
            'key'           => $this->randomBytes(),
            'computerid'    => $computerid
        );

        $this->database->insertWallet( $array );
    }

    /**
     * Gets the price to buy bitcoin
     *
     * @return mixed|null
     */

    public function getBitcoinBuyPrice()
    {

        if( Settings::getSetting('syscrack_bitcoin_live') == false )
        {

            return Settings::getSetting('syscrack_bitcoin_buyprice');
        }

        $result = json_decode( Request::post( Settings::getSetting('syscrack_bitcoin_url') )->body, true );

        if( empty( $result ) )
        {

            return null;
        }

        return $result[ strtoupper( Settings::getSetting('syscrack_bitcoin_country') ) ]['buy'];
    }

    /**
     * Returns random bytes
     *
     * @return string
     */

    private function randomBytes()
    {

        return Hashes::randomBytes();
    }
}