<?php
namespace Framework\Application\Utilities;

/**
 * Lewis Lancaster 2017
 *
 * Class Online
 *
 * @package Framework\Application
 */

use Framework\Database\Tables\Sessions;

class Online
{

    /**
     * @var Sessions
     */

    protected $database;

    /**
     * Online constructor.
     */

    public function __construct()
    {

        $this->database = new Sessions();
    }

    /**
     * Gets the count
     *
     * @return int|null
     */

    public function getCount()
    {

        $sessions = $this->database->getAllSessions();

        if( $sessions->isEmpty() )
        {

            return null;
        }

        return $sessions->count();
    }

    /**
     * Gets the most recent online users ( default is within an hour )
     *
     * @param $timeframe
     *
     * @return mixed
     */

    public function getRecent( $timeframe )
    {

        $sessions = $this->database->getAllSessions();

        if( $sessions->isEmpty() )
        {

            return null;
        }

        return $sessions->where('lastaction','>', $timeframe );
    }
}