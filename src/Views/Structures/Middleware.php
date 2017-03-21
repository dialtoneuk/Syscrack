<?php
namespace Framework\Views\Structures;

/**
 * Lewis Lancaster 2017
 *
 * Interface Middleware
 *
 * @package Framework\Views\Structures
 */

interface Middleware
{

    /**
     * Called just when a page is requested and just before any rendering occurs
     *
     * @return mixed
     */

    public function onRequest();

    /**
     * Called when the user passes the middleware
     *
     * @return mixed
     */

    public function onSuccess();

    /**
     * Called when the user fails the middleware
     *
     * @return mixed
     */

    public function onFailure();
}