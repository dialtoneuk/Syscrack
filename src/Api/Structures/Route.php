<?php
namespace Framework\Api\Structures;

/**
 * Lewis Lancaster 2016
 *
 * Interface Route
 *
 * @package Framework\Api\Structures
 */

interface Route
{

    /**
     * @return Authenticator
     */

    public function authenticator();

    /**
     * @return array
     */

    public function routes();
}