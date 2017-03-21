<?php
namespace Framework\Ajax\Structures;

/**
 * Lewis Lancaster 2016
 *
 * Interface Route
 *
 * @package Framework\Ajax\Structures
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