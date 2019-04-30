<?php
namespace Framework\Application\UtilitiesV2;


class Debug
{

    /**
     * @var \stdClass
     */

    protected static $objects;

    /**
     * @var bool
     */

    protected static $supressed = false;

    /**
     *
     */

    public static function initialization()
    {

        self::$objects = new \stdClass();
        self::$objects->timers = new \stdClass();
    }

    /**
     * @param string $message
     * @param bool $include_time
     * @throws \RuntimeException
     */

    public static function message( string $message, bool $include_time=true )
    {

        if( DEBUG_ENABLED == false )
            return;

        if( self::isInit() == false )
            self::initialization();

        if( Debug::isCMD() )
            Debug::echo( "debug message: " . $message, 4 );

        if( isset( self::$objects->messages ) == false )
            self::$objects->messages = [];

        if( $include_time )
            $time = time();
        else
            $time = false;

        self::$objects->messages[] = [
            'message'   => $message,
            'time'      => $time
        ];
    }

    /**
     * Shorthand msg
     *
     * @param string $msg
     */

    public static function msg( string $msg )
    {

        self::message( $msg );
    }

    /**
     * @return bool
     */

    public static function isEnabled()
    {

        return( DEBUG_ENABLED );
    }

    /**
     * @param $name
     * @param $time
     * @throws \RuntimeException
     */

    public static function setStartTime( $name, $time=null )
    {

        if( DEBUG_ENABLED == false )
            return;

        if( $time == null )
            $time = time();

        if( self::isInit() == false )
            throw new \RuntimeException('Please enable error debugging');

        if( isset( self::$objects->timers->$name ) )
        {

            if(isset( self::$objects->timers->$name["start"] ) )
                throw new \RuntimeException("Start time has already been set");
        }

        self::$objects->timers->$name = [
            "start" => $time
        ];
    }

    /**
     * @throws \RuntimeException
     */

    public static function stashMessages()
    {

        if( DEBUG_ENABLED == false )
            return;

        if( self::hasMessages() == false )
            return;


        if( file_exists( SYSCRACK_ROOT . DEBUG_MESSAGES_FILE ) == false )
            self::checkDirectory();

        file_put_contents(SYSCRACK_ROOT . DEBUG_MESSAGES_FILE, json_encode( self::getMessages(), JSON_PRETTY_PRINT ) );
    }

    /**
     * @throws \RuntimeException
     */

    public static function stashTimers()
    {

        if( DEBUG_ENABLED == false )
            return;

        if( self::hasTimers() == false )
            return;

        if( file_exists( SYSCRACK_ROOT . DEBUG_TIMERS_FILE ) == false )
            self::checkDirectory();

        file_put_contents(SYSCRACK_ROOT . DEBUG_TIMERS_FILE, json_encode( self::getTimers(), JSON_PRETTY_PRINT ) );
    }

    /**
     * @param $name
     * @param $time
     * @throws \RuntimeException
     */

    public static function setEndTime( $name, $time=null )
    {

        if( DEBUG_ENABLED == false )
            return;

        if( $time == null )
            $time = time();

        if( self::isInit() == false )
            throw new \RuntimeException('Please enable error debugging');

        if( isset( self::$objects->timers->$name ) )
        {

            if(isset( self::$objects->timers->$name["end"] ) )
                throw new \RuntimeException("End time has already been set");
        }
        else
            throw new \RuntimeException('Invalid timer');

        self::$objects->timers->$name['end'] = $time;
    }

    /**
     * @param $name
     * @return float
     * @throws \RuntimeException
     */

    public static function getDifference( $name )
    {

        if( isset( self::$objects->timers->$name ) == false )
            throw new \RuntimeException('Invalid timer');

        $times = self::$objects->timers->$name;

        return( $times['end'] - $times['start'] );
    }


    /**
     * @param $name
     * @return bool
     */

    public static function hasTimer( $name )
    {

        return( isset( self::$objects->timers->$name ) );
    }

    /**
     * @return mixed
     */

    public static function getMessages()
    {

        return( self::$objects->messages );
    }

    /**
     * @return mixed
     */

    public static function getTimers()
    {

        return( self::$objects->timers );
    }

    /**
     * @return bool
     */

    public static function hasMessages()
    {

        if( isset( self::$objects->messages ) == false )
            return false;

        if( empty( self::$objects->messages ) )
            return false;

        return true;
    }

    /**
     * @param string $prompt
     * @return string
     */

    public static function getLine( $prompt="Enter")
    {
        echo( $prompt . "\\\\: " );

        $result = readline();

        if( empty( $result ) )
            throw new \RuntimeException("no input");

        return( $result );
    }
    /**
     * @return bool
     */

    public static function hasTimers()
    {

        if( isset( self::$objects->timers ) == false )
            return false;

        if( empty( self::$objects->timers ) )
            return false;

        return true;
    }

    /**
     * @return bool
     */

    public static function isCMD()
    {

        return( defined( "CMD" ) );
    }

    /**
     * Sets CMD mode
     */

    public static function setCMD()
    {

        define("CMD", true );
    }

    /**
     * @return bool
     */

    public static function isTest()
    {

        return( defined( "TEST") );
    }

    /**
     * Sets test mode
     */

    public static function setTest()
    {

        define("TEST", true );
    }

    /**
     * @param $message
     * @param int $tabs
     */

    public static function echo( $message, $tabs=0 )
    {

        //We don't want any straight up msg's to make their way onto the users HTML
        if( Debug::isCMD() == false )
            return;

        if( self::$supressed )
            return;

        if( $tabs == 0 )
        {

            if( is_string( $message ) == false )
                $message = print_r( $message );

            echo( $message . "\n" );
            return;
        }

        $prefix = "-";

        for( $i = 0; $i < $tabs; $i++ )
            $prefix = $prefix . "-";

        if( $tabs !== 1 )
            $prefix .= ">";

        echo( $prefix . " " . $message . "\n");
    }

    /**
     * @return bool
     */

    public static function isSuppressed()
    {

        return( self::$supressed );
    }

    /**
     * @param bool $bool
     */

    public static function setSupressed( $bool=true )
    {

        self::$supressed = $bool;
    }

    /**
     * @return bool
     */

    private static function isInit()
    {

        if( self::$objects instanceof \stdClass == false )
            return false;

        return true;
    }

    /**
     * @throws \RuntimeException
     */

    private static function checkDirectory()
    {

        $removed_filename = explode('/', DEBUG_MESSAGES_FILE );
        array_pop( $removed_filename );

        $filename = implode( "/", $removed_filename ) . "/";

        if( is_file( SYSCRACK_ROOT . $filename ) )
            throw new \RuntimeException('Returned path is not a directory');

        if( file_exists( SYSCRACK_ROOT . $filename ) == false )
            mkdir( SYSCRACK_ROOT . $filename );
    }
}