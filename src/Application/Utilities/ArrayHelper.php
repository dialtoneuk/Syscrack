<?php
namespace Framework\Application\Utilities;

/**
 * Lewis Lancaster 2017
 *
 * Class ArrayHelper
 *
 * @package Framework\Application\Utilities
 */

use Framework\Exceptions\ApplicationException;

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

    /**
     * Sorts an array by a key value
     *
     * @param array $array
     *
     * @param $by
     *
     * @param int $sorttype
     *
     * @return array|mixed
     */

    public static function sortArray( array $array, $by, $sorttype=SORT_DESC )
    {

        if( empty( $array ) || count( $array ) == 1 )
        {

            return $array[0];
        }

        $sort = array();

        foreach( $array as $key=>$value )
        {

            if( is_object( $value ) )
            {

                $sort[] = $value->{ $by };
            }
            else
            {

                if( is_array( $value ) )
                {

                    $soft[] = $value[ $by ];
                }
                else
                {

                    throw new ApplicationException();
                }
            }
        }

        $result = array_multisort( $sort, $sorttype, $array );

        if( $result == false )
        {

            throw new ApplicationException();
        }

        return $array;
    }
}