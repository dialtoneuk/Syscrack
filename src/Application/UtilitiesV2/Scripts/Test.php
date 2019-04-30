<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 05/08/2018
 * Time: 22:13
 */

namespace Framework\Application\UtilitiesV2\Scripts;


use Framework\Application\UtilitiesV2\Debug;
use Framework\Application\UtilitiesV2\Interfaces\Test as TestInterface;

class Test extends AutoTest
{

    /**
     * @param $arguments
     * @return bool
     * @throws \RuntimeException
     */

    public final function execute($arguments)
    {

        $this->tests->create();

        if( $this->tests->exists( $arguments["test"] ) == false )
        {

            if( Debug::isCMD() )
                Debug::echo("Test does not exist: " . $arguments["test"], 3 );

            return( false );
        }

        $test = $this->tests->get( strtolower( $arguments["test"] ) );

        /** @var TestInterface $test */

        if( $test->execute() == false )
        {

            if( Debug::isCMD() )
                Debug::echo("Failed test: " . $arguments["test"], 3 );

            return( false );
        }
        else
        {

            if( Debug::isCMD() )
                Debug::echo("Passed test: " . $arguments["test"], 3 );
        }

        return( true );
    }

    /**
     * @return array|null
     */

    public function requiredArguments()
    {

        return([
            "test"
        ]);
    }

    /**
     * @return array
     */

    public function help()
    {

        return( array_merge(parent::help(), ["help" => "Runs through a user defined test and errors if it fails." ] ) );
    }
}