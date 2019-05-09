<?php
/**
 * Created by PhpStorm.
 * User: newsy
 * Date: 09/05/2019
 * Time: 10:13
 */

namespace Framework\Syscrack\Game\Utilities;


use Framework\Syscrack\Game\Tool;

class EmptyTool extends Tool
{

    public function getRequirements()
    {

        return(["empty" => true ]);
    }
}