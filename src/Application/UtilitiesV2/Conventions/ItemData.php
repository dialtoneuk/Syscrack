<?php

namespace Framework\Application\UtilitiesV2\Conventions;


/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 31/08/2018
 * Time: 01:16
 */

use Framework\Application\UtilitiesV2\Convention;

/**
 * Class UploadData
 * @package Framework\Application\UtilitiesV2\Conventions
 *
 * @property string name
 * @property string item
 * @property string description
 * @property int cost
 * @property array requirements
 * @property bool onetime
 * @property bool discontinued
 */

class ItemData extends Convention
{

    /**
     * @var array
     */

    protected $requirements = [
        "name"          => "string",
        "item"          => "string",
        "description"   => "string",
        "cost"          => "int",
        "requirements"  => "array",
        "onetime"       => "bool",
        "discontinued"  => "bool"
    ];
}