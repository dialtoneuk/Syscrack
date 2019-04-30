<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 22/07/2018
 * Time: 01:01
 */

namespace Framework\Application\UtilitiesV2\Scripts;

use Framework\Application\UtilitiesV2\Debug;

use Framework\Application\UtilitiesV2\Container;


class GetConnection extends Base
{

    /**
     * @param $arguments
     * @return bool
     * @throws \RuntimeException
     */

    public function execute($arguments)
    {

        if( Container::exist("application") == false )
            $this->initContainer();

        Debug::echo( print_r( Container::get("application")->connection->connection ) );
        return( true );
    }

    /**
     * @return array|null
     */

    public function requiredArguments()
    {

        return( null );
    }
}