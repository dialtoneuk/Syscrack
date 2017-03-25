<?php
namespace Framework\Database\Tables;

/**
 * Lewis Lancaster 2017
 *
 * Class Bitcoin
 *
 * @package Framework\Database\Tables
 */

use Framework\Database\Table;

class Bitcoin extends Table
{

    /**
     * Gets a bitcoin wallet
     *
     * @param $bitcoinid
     *
     * @return mixed|null
     */

    public function getBitcoinWallet( $bitcoinid )
    {

        $array = array(
            'bitcoinid' => $bitcoinid
        );

        $result = $this->getTable()->where( $array )->get();

        return ( $result->isEmpty() ) ? null : $result[0];
    }

    public function getUserBitcoinWallet( $userid )
    {

        $array = array(
            'userid' => $userid
        );

        $result = $this->getTable()->where( $array )->get();

        return ( $result->isEmpty() ) ? null : $result[0];
    }

    /**
     * Finds a bitcoin wallet through its wallet
     *
     * @param $wallet
     *
     * @return mixed|null
     */

    public function findBitcoinWallet( $wallet )
    {

        $array = array(
            'wallet' => $wallet
        );

        $result = $this->getTable()->where( $array )->get();

        return ( $result->isEmpty() ) ? null : $result[0];
    }

    /**
     * Finds the wallets by their server
     *
     * @param $computerid
     *
     * @return \Illuminate\Support\Collection|null
     */

    public function findByServer( $computerid )
    {

        $array = array(
            'wallet' => $wallet
        );

        $result = $this->getTable()->where( $array )->get();

        return ( $result->isEmpty() ) ? null : $result;
    }

    public function insertWallet( $array )
    {

        return $this->getTable()->insertGetId( $array );
    }

    public function updateWallet( $wallet, $values )
    {

        $array = array(
            'wallet' => $wallet
        );

        $this->getTable()->where( $array )->update( $values );
    }
}