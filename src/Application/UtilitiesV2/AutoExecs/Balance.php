<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 06/08/2018
 * Time: 01:50
 */

namespace Framework\Application\UtilitiesV2\AutoExecs;

use Framework\Application\UtilitiesV2\Balance as BalanceManager;
use Framework\Application\UtilitiesV2\Debug;

class Balance extends Base
{

    /**
     * @var BalanceManager
     */

    protected $balancemanager;

    /**
     * Balance constructor.
     * @throws \RuntimeException
     */

    public function __construct()
    {

        $this->balancemanager = new BalanceManager();

        parent::__construct();
    }

    /**
     * @param array $data
     * @return mixed|void
     * @throws \RuntimeException
     */

    public function execute(array $data)
    {

        if( isset( $data["userid"] ) == false )
            throw new \RuntimeException("Expecting userid");

        Debug::message("Checking if user has balance");

        if( $this->balancemanager->hasBalance( $data["userid"] ) )
        {
            Debug::message("User has balance");
            return;
        }

        Debug::message("Creating new balance with amount: " . BALANCE_DEFAULT_AMOUNT );

        $this->balancemanager->create( $data["userid"], BALANCE_DEFAULT_AMOUNT );
    }
}