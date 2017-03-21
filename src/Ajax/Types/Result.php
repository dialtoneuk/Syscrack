<?php
namespace Framework\Ajax\Types;

/**
 * Lewis Lancaster 2016
 *
 * Class Result
 *
 * @package Framework\Ajax\Types
 */

use Framework\Ajax\Structures\Types;

class Result implements Types
{

    /**
     * @var string
     */

    protected $data;

    /**
     * Result constructor.
     *
     * @param $data
     */

    public function __construct( $data )
    {

        $this->data = $data;
    }

    /**
     * Returns the data
     *
     * @return array
     */

    public function getResult()
    {

        return array(
            'error' => false,
            'data'  => $this->data
        );
    }
}