<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 08/08/2018
 * Time: 22:36
 */

namespace Framework\Application\UtilitiesV2\AutoExecs;

use Framework\Application\UtilitiesV2\UserPermissions as User;


class UserPermissions extends Base
{

    /**
     * @var User
     */

    protected $userpermissions;

    /**
     * Balance constructor.
     * @throws \RuntimeException
     */

    public function __construct()
    {

        $this->userpermissions = new User();

        parent::__construct();
    }

    /**
     * @param array $data
     * @return void
     * @throws \RuntimeException
     */

    public function execute(array $data)
    {

        if( isset( $data["userid"] ) == false )
            throw new \RuntimeException("expecting userid");

        if( $this->userpermissions->exist( $data["userid"] ) )
            $this->userpermissions->remove( $data["userid"] );

        if( isset( $data["group"] ) )
            $group = $data["group"];
        else
            $group = "default";

        $this->userpermissions->create( $data["userid"], $group );

        return;
    }
}