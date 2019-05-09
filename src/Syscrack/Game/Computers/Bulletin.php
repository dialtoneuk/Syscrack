<?php
/**
 * Created by PhpStorm.
 * User: newsy
 * Date: 05/05/2019
 * Time: 15:36
 */

namespace Framework\Syscrack\Game\Computers;


use Framework\Syscrack\Game\Structures\Computer;

class Bulletin extends Npc implements Computer
{

    /**
     * The configuration of this computer
     *
     * @return array
     */

    public function configuration()
    {

        return array(
            'installable' => false,
            'type'        => 'bulletin'
        );
    }
}