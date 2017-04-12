<?php
namespace Framework\Application\Utilities;

/**
 * Lewis Lancaster 2017
 *
 * Class ArrayHelper
 *
 * @package Framework\Application\Utilities
 */

class ArrayHelper
{

    /**
     * Converts the array into an object ( ignoring the type of its key values )
     *
     * @param array $array
     *
     * @return \stdClass
     */

    public static function toObject( array $array )
    {

        $object = new \stdClass();

        foreach( $array as $key=>$value )
        {

            if( isset( $object->$key ) == false )
            {

                $object->$key = $value;
            }
        }

        return $object;
    }

    /**
     * Converts everything ( including nested arrays and other objects ) into an stdClass
     *
     * @param array $array
     *
     * @return mixed
     */

    public static function allToObject( array $array )
    {

        return json_decode( json_encode( $array ) );
    }
}