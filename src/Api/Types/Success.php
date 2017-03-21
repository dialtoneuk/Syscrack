<?php
namespace Framework\Api\Types;

/**
 * Lewis Lancaster 2016
 *
 * Class Success
 *
 * @package Framework\Api\Types
 */

use Framework\Api\Structures\Types;

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