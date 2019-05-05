<?php
namespace Framework\Application\UtilitiesV2;

use Framework\Application\UtilitiesV2\Conventions\FileData;
use Framework\Application\UtilitiesV2\Conventions\TokenData;

/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 31/08/2018
 * Time: 20:58
 */
class TokenReader
{

    /**
     * @var \Error|null
     */

    protected static $last_error = null;

    /**
     * @var string
     */

    protected $token;

    /**
     * TokenReader constructor.
     * @param string $token
     */

    public function __construct( $token="_" )
    {

        if( self::hasLastError() )
            self::setLastError();

        $this->token = $token;
    }

    /**
     * @param FileData $file
     * @param TokenData $values
     * @param $path
     * @param bool $object
     * @return FileData|bool
     */

    public function parse( FileData $file, TokenData $values, $path, $object=true )
    {

        try
        {

            $contents = $file->contents;

            foreach( $values->values as $key=>$value )
                $contents = str_replace( $this->token . $key . $this->token, $value, $contents );

            $this->save( $contents, $path );

            if( file_exists( SYSCRACK_ROOT . $path ) == false )
                throw new \Error("Failed to save file");
        }
        catch ( \Error $exception )
        {

            self::setLastError( $exception );
            return( false );
        }

        if( $object )
            return( $this->object( $path ) );

        return( $contents );
    }

    /**
     * @param $path
     * @return FileData
     */

    private function object( $path )
    {

        return( FileOperator::pathDataInstance( $path ) );
    }

    /**
     * @param $contents
     * @param $path
     */

    private function save( $contents, $path )
    {

        file_put_contents( SYSCRACK_ROOT . $path, $contents );
    }

    /**
     * @param array $values
     * @return TokenData
     */

    public static function dataInstance( $values ): TokenData
    {

        return( new TokenData( $values ) );
    }

    /**
     * @return bool
     */

    public static function hasLastError()
    {

        if( self::$last_error == null )
            return false;

        return true;
    }

    /**
     * @param \Error|null $exception
     */

    public static function setLastError( \Error $exception=null )
    {

        self::$last_error = $exception;
    }

    /**
     * @return \Error
     */

    public static function getLastError(): \Error
    {

        return( self::$last_error );
    }
}