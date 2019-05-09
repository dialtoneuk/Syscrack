<?php
    namespace Framework\Syscrack\Game\Softwares;

/**
 * Lewis Lancaster 2017
 *
 * Class Text
 *
 * @package Framework\Syscrack\Game\Softwares
 */

use Framework\Syscrack\Game\BaseClasses\BaseSoftware;


class Text extends BaseSoftware
{

    /**
     * The configuration of this Structure
     *
     * @return array
     */

    public function configuration()
    {

        return array(
            'uniquename'    => 'text',
            'extension'     => '.txt',
            'type'          => 'text',
            'viewable'      => true,
            'removable'    => true,
            'installable'   => false,
            'executable'    => true,
            'keepdata'      => true
        );
    }
}