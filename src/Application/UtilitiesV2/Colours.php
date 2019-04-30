<?php
namespace Framework\Application\UtilitiesV2;

/**
 * Class Colours
 * @package Framework\Application\UtilitiesV2\Util
 * @deprecated
 */

class Colours
{

    /**
     * @param int $output
     * @return string
     * @throws \RuntimeException
     * @deprecated
     */

    public static function generate( $output=COLOURS_OUTPUT_RGB )
    {

        switch( $output )
        {

            case COLOURS_OUTPUT_HEX:
                return( dechex(rand(0x000000, 0xFFFFFF)) );
                break;
            case COLOURS_OUTPUT_RGB:
                return ( rand(0,255) . "," . rand(0,255) . "," . rand(0,255) );
                break;
            default:
                throw new \RuntimeException("Unknown output");
                break;
        }
    }
}