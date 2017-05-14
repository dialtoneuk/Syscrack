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
     * Gets an index from the post array
     *
     * @param $index
     *
     * @param bool $escape
     *
     * @return string
     */

    public static function getPostData( $index, $escape=false )
    {

        if( $escape == true )
        {

            return htmlspecialchars( $_POST[ $index ], ENT_QUOTES, 'UTF-8' );
        }

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

            return false;
        }

        $post = self::getPost();

        foreach( $requirements as $requirement )
        {

            if( isset( $post[ $requirement ] ) == false )
            {

                return false;
            }
            else
            {

                if( $post[ $requirement ] == '0' )
                {

                    continue;
                }

                if( $post[ $requirement ] == null || $post[ $requirement ] == "" || empty( $post[ $requirement ] ) )
                {

                    return false;
                }
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

    public static function getPost()
    {

        return $_POST;
    }

}