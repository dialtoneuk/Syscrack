<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 20/07/2018
 * Time: 20:20
 */

namespace Framework\Application\UtilitiesV2\Migrators;

use Colourspace\Database\Migrator;
use Framework\Application\UtilitiesV2\Container;

class Database extends Base
{

    protected $migrator;

    /**
     * Database constructor.
     * @throws \RuntimeException
     */

    public function __construct()
    {

        $this->migrator = new Migrator();
    }

    /**
     * @throws \RuntimeException
     */

    public function migrate()
    {

        if( Container::exist("application") == false )
            throw new \RuntimeException("Application does not exist");

        if( Container::get("application")->connection->test() == false )
            throw new \RuntimeException("Failed connction test, have you verified that your connection settings are correct?");

        $this->migrator->process();
    }
}