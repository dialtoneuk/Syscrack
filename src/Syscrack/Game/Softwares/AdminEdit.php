<?php
namespace Framework\Syscrack\Game\Softwares;

/**
 * Lewis Lancaster 2017
 *
 * Class AdminEdit
 *
 * @package Framework\Syscrack\Game\Softwares
 */

use Framework\Syscrack\Game\BaseClasses\BaseSoftware;
use Framework\Syscrack\Game\Tool;

class AdminEdit extends BaseSoftware
{

    /**
     * The configuration of this Structure
     *
     * @return array
     */

    public function configuration()
    {

        return array(
            'uniquename'        => 'adminedit',
            'extension'         => '.admin',
            'type'              => 'adminedit',
            'installable'       => true,
            'executable'        => true,
            'localexecuteonly'  => true,
        );
    }

    /**
     * @param null $userid
     * @param null $sofwareid
     * @param null $computerid
     * @return Tool
     */

    public function tool($userid = null, $sofwareid = null, $computerid = null): Tool
    {

        $tool = new Tool("Admin Edit", "success");
        $tool->setAction('adminedit');
        $tool->admin();
        $tool->localAllowed();
        $tool->icon = "wrench";

        return( $tool );
    }
}