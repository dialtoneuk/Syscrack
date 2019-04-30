<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 05/08/2018
 * Time: 21:43
 */

namespace Framework\Application\UtilitiesV2;


use Framework\Application\UtilitiesV2\Interfaces\Test;

class Tests
{

    /**
     * @var Constructor
     */

    protected $tests;

    /**
     * Tests constructor.
     * @param bool $auto_create
     * @throws \RuntimeException
     */

    public function __construct( $auto_create = true )
    {

        $this->tests = new Constructor(TESTS_ROOT, TESTS_NAMESPACE );

        if( $auto_create )
            $this->create();
    }

    /**
     * @throws \RuntimeException
     */

    public function process()
    {

        if( $this->tests->isEmpty() )
            $this->create();

        $tests = $this->tests->getAll();

        if( Debug::isCMD() )
            Debug::echo("\n Starting tests\n");

        foreach( $tests as $name=>$test )
        {

            if( Debug::isCMD() )
                Debug::echo("Working with test: " . $name , 5 );


            /** @var Test $test */

            if( $test instanceof Test == false )
                throw new \RuntimeException("Invalid class: " . $name );

            $result = $test->execute();

            if( $result == false )
                return([
                    "success"   => false,
                    "test"      => $name
                ]);
        }

        return([
            "success" => true,
        ]);
    }

    /**
     * @throws \RuntimeException
     */

    public function create()
    {

        $this->tests->createAll();
    }

    /**
     * @param $test
     * @return bool
     */

    public function exists( $test )
    {

        return( $this->tests->exist( $test ) );
    }

    /**
     * @param $test
     * @return \stdClass
     */

    public function get( $test )
    {

        return( $this->tests->get( $test ) );
    }
}