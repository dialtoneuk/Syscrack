<?php

namespace Framework\Application\UtilitiesV2\Conventions;

/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 31/08/2018
 * Time: 21:46
 */

use Framework\Application\UtilitiesV2\Convention;

/**
 * Class FileData
 * @package Framework\Application\UtilitiesV2\Conventions
 * @property string path
 * @property string contents
 * @property array info
 */

class FileData extends Convention
{

    /**
     * @var array
     */

    protected $requirements = [
        "path" => "string",
        "contents" => "string",
        "info" => "array"
    ];
}