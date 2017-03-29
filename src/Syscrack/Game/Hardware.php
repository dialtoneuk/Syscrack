<?php
namespace Framework\Syscrack\Game;

/**
 * Lewis Lancaster 2016
 *
 * Class Hardware
 *
 * @package Framework\Syscrack\Game
 */

use Framework\Database\Tables\Computers;

class Hardware
{

    protected $computers;

    public function __construct()
    {

        $this->computers = new Computers();
    }
}