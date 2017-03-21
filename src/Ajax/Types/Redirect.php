<?php
namespace Framework\Ajax\Types;

/**
 * Lewis Lancaster 2016
 *
 * Class Redirect
 *
 * @package Framework\Ajax\Types
 */

use Framework\Ajax\Structures\Types;
use Flight;

class Redirect implements Types
{

    /**
     * @var string
     */

    protected $location;

    /**
     * Redirect constructor.
     *
     * @param $location
     */

    public function __construct( $location )
    {

        $this->location = $location;
    }

    /**
     * Redirects the user to a page
     */

    public function getResult()
    {

        Flight::redirect( $this->location );
    }
}