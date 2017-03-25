<?php
namespace Framework\Syscrack\Game;

/**
 * Lewis Lancaster 2017
 *
 * Class Bitcoin
 *
 * @package Framework\Syscrack\Game
 */

use Framework\Application\Utilities\Hashes;
use Framework\Exceptions\SyscrackException;
use Unirest\Request;
use Framework\Application\Settings;
use Framework\Database\Tables\Bitcoin as Database;
use Framework\Database\Tables\Computers;

class Bitcoin
{

    /**
     * @var Database
     */

    protected $database;

    /**
     * @var Computers
     */

    protected $computers;

    /**
     * Bitcoin constructor.
     */

    public function __construct()
    {

        $this->database = new Database();

        $this->computers = new Computers();
    }

    /**
     * Gets the users wallet
     *
     * @param $userid
     *
     * @return mixed|null
     */

    public function getWallet( $userid )
    {

        return $this->database->getUserBitcoinWallet( $userid );
    }

    /**
     * Gets the bitcoin servers
     *
     * @return mixed|null
     */

    public function getBitcoinServers()
    {

        return $this->computers->getComputerByType( Settings::getSetting('syscrack_bitcoin_type') );
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

        return $this->database->findByServer( $computerid );
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

        if( $this->computers->getComputer( $computerid )->type != Settings::getSetting('syscrack_bitcoin_type') )
        {

            return false;
        }

        return true;
    }

    /**
     * Deposits bitcoins into the users wallet
     *
     * @param $userid
     *
     * @param float $bitcoins
     */

    public function deposit( $userid, float $bitcoins )
    {

        $currentbitcoins = $this->database->getUserBitcoinWallet( $userid )->bitcoins;

        if( $bitcoins == null )
        {

            throw new SyscrackException();
        }

        $wallet = $this->getWallet( $userid );

        $this->database->updateWallet( $wallet, array( 'bitcoins' => $currentbitcoins + $bitcoins ) );
    }

    /**
     * Withdraws bitcoins from the wallet
     *
     * @param $userid
     *
     * @param float $bitcoins
     */

    public function withdraw( $userid, float $bitcoins )
    {

        $currentbitcoins = $this->database->getUserBitcoinWallet( $userid )->bitcoins;

        if( $bitcoins == null )
        {

            throw new SyscrackException();
        }

        $wallet = $this->getWallet( $userid );

        $this->database->updateWallet( $wallet, array( 'bitcoins' => $currentbitcoins - $bitcoins ) );
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

    public function transfar( $wallet, $receiver, $bitcoins )
    {

        $user = $this->database->findBitcoinWallet( $wallet );

        if( $this->canAfford( $user->userid, $bitcoins ) == false )
        {

            throw new SyscrackException();
        }

        $this->database->updateWallet( $wallet, array( 'bitcoins' => $user->bitcoins - $bitcoins ) );

        $this->database->updateWallet( $wallet, array( 'bitcoins' =>
            $this->database->getUserBitcoinWallet( $receiver )->bitcoins + $bitcoins ) );
    }

    /**
     * Returns true if the user can afford ths transaction
     *
     * @param $userid
     *
     * @param $price
     *
     * @return bool
     */

    public function canAfford( $userid, $price )
    {

        $currentbitcoins = $this->database->getUserBitcoinWallet( $userid )->bitcoins;

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

        if( $this->database->getUserBitcoinWallet( $userid ) == null )
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

        if( empty( $result ) )
        {

            return null;
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