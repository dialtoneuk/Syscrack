<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 05/08/2018
 * Time: 02:13
 */

namespace Framework\Application\UtilitiesV2\Controller;


use Framework\Application\UtilitiesV2\Interfaces\Response;

class FormError implements Response
{

    /**
     * @var string
     */

    protected $message;

    /**
     * @var string
     */

    protected $type;

    /**
     * @var bool
     */

    protected $success = true;

    /**
     * FormError constructor.
     * @param string $type
     * @param string $message
     * @param null $success
     * @throws \RuntimeException
     */

    public function __construct( $type=FORM_ERROR_GENERAL, $message="", $success=null )
    {

        if( is_string( $message ) == false || is_string( $type ) == false )
            throw new \RuntimeException("Invalid param types");

        if( $success !== null )
            if( is_bool( $success ) )
                $this->success = $success;

        $this->message = $message;
        $this->type = $type;
    }

    /**
     * @return array
     */

    public function get()
    {

        return( array(
            "success"   => $this->success,
            "message"   => $this->message,
            "type"      => $this->type
        ));
    }
}