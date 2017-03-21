<?php
namespace Framework\Api\Structures;

/**
 * Lewis Lancaster 2016
 *
 * Interface Authenticator
 *
 * @package Framework\Api\Structures
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