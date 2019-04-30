<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 05/08/2018
 * Time: 21:43
 */

namespace Framework\Application\UtilitiesV2\Tests;


use Framework\Application\UtilitiesV2\Interfaces\Test;

abstract class Base implements Test
{

    public function execute()
    {

        return( true );
    }
}