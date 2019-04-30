<?php

namespace Framework\Application\UtilitiesV2;
use Framework\Application\UtilitiesV2\Interfaces\Upload;

/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 29/08/2018
 * Time: 21:35
 */
abstract class Collection
{

    /**
     * @var Constructor
     */

    protected $constructor;

    /**
     * @var \RuntimeException|null
     */

    protected $last_error = null;

    /**
     * ConstructorClass constructor.
     * @param $filepath
     * @param $namespace
     * @param bool $auto_create
     */

    public function __construct( $filepath, $namespace, $auto_create=true )
    {

        $this->constructor = new Constructor( $filepath, $namespace );

        if( $auto_create )
            $this->create();
    }

    /**
     * @return bool
     */

    protected final function create()
    {

        if( $this->getLastError() == null )
            $this->setLastError();

        try
        {

            $this->constructor->createAll();
            return true;
        }
        catch ( \RuntimeException $error )
        {

           $this->setLastError( $error );
        }

        return false;
    }

    /**
     * @param callable $callback
     */

    public final function iterate( callable $callback )
    {

        if( $this->constructor->isEmpty() )
            throw new \RuntimeException("constructor is empty");

        $instances = $this->constructor->getAll( true );

        foreach( $instances as $key=>$instance )
            $callback( $instance, $key, $this->constructor );
    }

    /**
     * @param $class_name
     * @return mixed
     */

    public final function get( $class_name )
    {

        return( $this->constructor->get( $class_name ) );
    }

    /**
     * Creates a single class
     *
     * @param $class_name
     */

    public final function single( $class_name )
    {

        $this->constructor->createSingular( $class_name );
    }

    /**
     * @param $class_name
     * @return bool
     */

    public function exist( $class_name )
    {

        return( $this->constructor->exist( $class_name ) );
    }

    /**
     * @return \Exception null
     */

    public final function getLastError()
    {

        if( empty( $this->last_error ) || $this->last_error == null )
            return null;

        return( $this->last_error );
    }

    /**
     * @param null|\Exception $error
     */

    protected final function setLastError( $error=null )
    {

        if( $error !== null )
            if( $error instanceof \Exception == false )
                throw new \RuntimeException("invalid error type");

        $this->last_error = $error;
    }

    /**
     * iteration test
     *
     * @param $data
     * @param $userid
     */

    private function interationTest( $data, $userid )
    {

        $results = [];

        $this->iterate( function( $instance, $key, $collection ) use ( $data, $userid, $results )
        {

            /** @var Upload $instance */
            if( $instance instanceof Upload == false )
                throw new \RuntimeException("Invalid");

            $results[$key] = $instance->authenticate($data, $userid );
        });

        print_r( $results );
    }
}