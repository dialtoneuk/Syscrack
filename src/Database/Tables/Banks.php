<?php
namespace Framework\Database\Tables;

/**
 * Lewis Lancaster 2016
 *
 * Class Banks
 *
 * @package Framework\Database\Tables
 */

use Framework\Database\Table;

class Banks extends Table
{

    /**
     * Lewis Lancaster 2017
     *
     * @param $userid
     *
     * @return \Illuminate\Support\Collection|null
     */

	public function getUserAccounts( $userid )
    {

        $array = array(
            'userid' => $userid
        );

        $result = $this->getTable()->where( $array )->get();

        return( $result->isEmpty() ) ? null : $result;
    }

    /**
     * Gets the account number
     *
     * @param $accountnumber
     *
     * @return \Illuminate\Support\Collection|null|\stdClass
     */

    public function getByAccountNumber( $accountnumber )
    {

        $array = array(
            'accountnumber' => $accountnumber
        );

        $result = $this->getTable()->where( $array )->get();

        return( $result->isEmpty() ) ? null : $result[0];
    }

    /**
     * Gets the accounts on the computer
     *
     * @param $computerid
     *
     * @return \Illuminate\Support\Collection|null
     */

    public function getAccountsOnComputer( $computerid )
    {

        $array = array(
            'computerid' => $computerid
        );

        $result = $this->getTable()->where( $array )->get();

        return( $result->isEmpty() ) ? null : $result;
    }

    /**
     * Inserts an account into the database
     *
     * @param array $array
     *
     * @return int
     */

    public function insertAccount( array $array )
    {

        return $this->getTable()->insertGetId( $array );
    }

    public function deleteAccount( $computerid, $userid )
    {

        $array = array(
            'computerid' => $computerid,
            'userid'     => $userid
        );

        $this->getTable()->where( $array )->delete();
    }

    /**
     * Updates a users financial account
     *
     * @param $computerid
     *
     * @param $userid
     *
     * @param array $values
     */

    public function updateAccount( $computerid, $userid, array $values )
    {

        $array = array(
            'computerid' => $computerid,
            'userid'     => $userid
        );

        $this->getTable()->where( $array )->update( $values );
    }
}