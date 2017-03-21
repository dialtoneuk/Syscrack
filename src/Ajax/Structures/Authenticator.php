<?php
namespace Framework\Ajax\Structures;

/**
 * Lewis Lancaster 2016
 *
 * Interface Authenticator
 *
 * @package Framework\Ajax\Structures
 */

interface Authenticator
{

    /**
     * @param $data
     *
     * @return mixed
     */

    public function getResult( $data );
}