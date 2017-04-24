<?php
namespace Framework\Syscrack\Game;

/**
 * Lewis Lancaster 2017
 *
 * Class NPC
 *
 * @package Framework\Syscrack\Game
 *
 * TODO: Rewrite this to instead take software level, software type and software name and create a new software instead of using a 'software id'
 */

use Framework\Database\Tables\Computers;

class NPC
{

    /**
     * @var Computers
     */

    protected $computers;

    /**
     * NPC constructor.
     */

    public function __construct()
    {

        $this->computers = new Computers();
    }
}