<?php
namespace Framework\Syscrack\Game\Utilities;

/**
 * Lewis Lancaster 2017
 *
 * Class TimeHelper
 *
 * @package Framework\Syscrack\Game\Utilities
 */

class TimeHelper
{

    /**
     * Returns the time seconds into the future ( trippy )
     *
     * @param $seconds
     *
     * @return int
     */

    public function getSecondsInFuture( $seconds )
    {

        return time() + ( $seconds );
    }
}