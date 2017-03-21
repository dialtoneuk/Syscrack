<?php
namespace Framework\Application\Utilities;

use Framework\Exceptions\ApplicationException;

/**
 * Lewis Lancaster 2017
 *
 * Class PostHelper
 *
 * @package Framework\Application\Utilities
 */

class PostHelper
{

    /**
     * Picks from the global post array and brings back only our requirements
     *
     * @param $requirements
     *
     * @return array
     */

    public static function returnRequirements( $requirements )
    {

        $post = self::getPost();

        $return = array();

        foreach( $requirements as $key=>$value )
        {

            if( isset( $post[ $value ] ) == false )
            {

                throw new ApplicationException('Post key invalid');
            }

            $return[ $value ] = $post[ $value ];
        }

        return $return;
    }

    /**
     * Checks the post data
     *
     * @param $requirements
     *
     * @return bool
     */

    public static function checkPostData( $requirements )
    {

        if( self::hasPostData() == false )
        {

            return false;
        }

        if( self::checkForRequirements( $requirements ) == false )
        {

            return false;
        }

        $post = self::getPost();

        foreach( $requirements as $key=>$value )
        {

            if( isset( $post[ $value ] ) )
            {

                if( $post[ $value ] == null || empty( $post[ $value ] ) )
                {

                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Gets the post data at the specified index
     *
     * @param $index
     *
     * @return mixed
     */

    public static function getPostData( $index )
    {

        return $_POST[ $index ];
    }

    /**
     * Checks the post data to see if the keys that we need exist
     *
     * @param array $requirements
     *
     * @return bool
     */

    public static function checkForRequirements( array $requirements )
    {

        if( self::hasPostData() == false )
        {

            throw new ApplicationException();
        }

        $post = self::getPost();

        foreach( $requirements as $requirement )
        {

            if( isset( $post[ $requirement ] ) == false )
            {

                return false;
            }
        }

        return true;
    }

    /**
     * Returns true if post data is present
     *
     * @return bool
     */

    public static function hasPostData()
    {

        if( empty( $_POST ) )
        {

            return false;
        }

        return true;
    }

    /**
     * Gets the post data
     *
     * @return mixed
     */

    private static function getPost()
    {

        return $_POST;
    }

}