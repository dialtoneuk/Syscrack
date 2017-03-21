<?php
namespace Framework\Ajax\Types;

/**
 * Lewis Lancaster 2016
 * 
 * Class Error
 *
 * @package Framework\Ajax\Types
 */

use Framework\Ajax\Structures\Types;

class Error implements Types
{

    /**
     * @var string
     */

    protected $message;

    /**
     * Error constructor.
     *
     * @param $message
     */

    public function __construct( $message )
    {

        $this->message = $message;
    }

    /**
     * Returns the error
     *
     * @return array
     */

    public function getResult()
    {

        return array(
            'error' => true,
            'stack' => array(
                'message' => $this->message
            )
        );
    }
}