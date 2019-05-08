<?php
/**
 * Created by PhpStorm.
 * User: newsy
 * Date: 08/05/2019
 * Time: 12:50
 */

namespace Framework\Syscrack\Game;


class Tool
{

    protected $inputs = [];
    protected $requirements = [];

    public function getInputs()
    {

        return( $this->inputs );
    }

    public function getRequirements()
    {

        return( $this->getRequirements() );
    }

    public function isConnected()
    {

        $this->requirements['connected'] = true;
    }

    public function isComputerType( string $type )
    {

        $this->requirements['type'] = $type;
    }

    public function hasSoftwareInstalled( string $type )
    {

        $this->requirements['software'] = $type;
    }

    public function addInput( $name, $type, $value="",  $placeholder="", $class="form-control", $html=true ) : void
    {

        if( $html )
            $html = $this->html([
                'name'          => $name,
                'type'          => $type,
                'value'         => $value,
                'placeholder'   => $placeholder,
                'class'         => $class
            ]);
        else
            $html = null;

        $this->inputs[ $name ] = [
            'name'  => $name,
            'type'  => $type,
            'value' => $value,
            'html'  => $html
        ];
    }

    private function html( array $values ) : string
    {

        $html = "<input %s >";
        $str = "";

        foreach( $values as $key=>$value )
            $str .= sprintf("'%s'", $key ) . "=" . sprintf("'%s'", $value );

        return( sprintf( $html, $str ) );
    }
}