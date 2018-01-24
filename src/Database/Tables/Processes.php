<?php
namespace Framework\Database\Tables;

/**
 * Lewis Lancater 2017
 *
 * Class Processes
 *
 * @package Framework\Database\Tables
 */

use Framework\Database\Table;

class Processes extends Table
{

    public function getProcess( $processid )
    {

        $array = array(
            'processid' => $processid
        );

        $result = $this->getTable()->where( $array )->get();

        return ( $result->isEmpty() ) ? null : $result[0];
    }

    public function getUserProcesses( $userid )
    {

        $array = array(
            'userid' => $userid
        );

        $result = $this->getTable()->where( $array )->get();

        return ( $result->isEmpty() ) ? null : $result;
    }

    public function getComputerProcesses( $computerid )
    {

        $array = array(
            'computerid' => $computerid
        );

        $result = $this->getTable()->where( $array )->get();

        return ( $result->isEmpty() ) ? null : $result;
    }

    public function insertProcess( $array )
    {

        return $this->getTable()->insertGetId( $array );
    }

    public function updateProcess( $processid, $values )
    {

        $array = array(
            'processid' => $processid
        );

        $this->getTable()->where( $array )->update( $values );
    }

    public function trashProcess( $processid )
    {
        $array = array(
            'processid' => $processid
        );

        $this->getTable()->where( $array )->delete();
    }
}