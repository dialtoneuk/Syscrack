<?php
namespace Framework\Ajax\Types;

/**
 * Lewis Lancaster 2016
 *
 * Class Success
 *
 * @package Framework\Ajax\Types
 */

use Framework\Ajax\Structures\Types;

class Success implements Types
{

    /**
     * Returns a simple true
     *
     * @return array
     */

    public function getResult()
    {

        return array(
            'error' => false
        );
    }
}