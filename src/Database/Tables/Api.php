<?php
namespace Framework\Database\Tables;

/**
 * Lewis Lancaster 2017
 *
 * Class Api
 *
 * @package Framework\Database\Tables
 */

use Framework\Database\Table;

class Api extends Table
{

    /**
     * Gets the Api by the API key
     *
     * @param $apikey
     *
     * @return mixed
     */

    public function getApiByKey( $apikey )
    {

        $array = array(
            'apikey' => $apikey
        );

        $result = $this->getTable()->where( $array )->get();

        return ( empty( $result ) ) ? null : reset( $result );
    }

    /**
     * Gets all the API by a user
     *
     * @param $userid
     *
     * @return array|null|static[]
     */

    public function getApiByUser( $userid )
    {

        $array = array(
            'userid' => $userid
        );

        $result = $this->getTable()->where( $array )->get();

        return ( empty( $result ) ) ? null : $result;
    }

    /**
     * Gets the API through the Api ID
     *
     * @param $apiid
     *
     * @return mixed
     */

    public function getApi( $apiid )
    {

        $array = array(
            'apiid' => $apiid
        );

        $result = $this->getTable()->where( $array )->get();

        return ( empty( $result ) ) ? null : reset( $result );
    }
}