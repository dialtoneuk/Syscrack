<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 30/06/2018
 * Time: 00:25
 */
namespace Framework\Application\UtilitiesV2;

class Constructor
{

    /**
     * @var \stdClass
     */

    private $objects;

    /**
     * @var string
     */

    private $filepath;

    /**
     * @var string
     */

    private $namespace;

    /**
     * Factory constructor.
     * @param $filepath
     * @param $namespace
     * @throws \RuntimeException
     */

    public function __construct( $filepath, $namespace )
    {

        Debug::message('Constructor created with file_path ' . $filepath . ' and namespace of ' . $namespace );

        $this->objects = new \stdClass();

        if ( file_exists( SYSCRACK_ROOT . $filepath ) == false || is_dir( SYSCRACK_ROOT . $filepath ) == false )
            throw new \RuntimeException('Root filepath is invalid');

        $this->file_path = SYSCRACK_ROOT . $filepath;
        $this->namespace = $namespace;
    }

    /**
     * Destructor
     */

    public function __destruct()
    {

        unset( $this->objects );
    }

    /**
     * @return bool
     */

    public function isEmpty()
    {

        return( empty( $this->objects ) );
    }

    /**
     * @param bool $overwrite
     * @return \stdClass
     */

    public function createAll( $overwrite=true )
    {

        Debug::message('Creating classes in directory');

        $files = $this->crawl();

        if ( empty( $files ) )
            throw new \RuntimeException('No files found');

        if( $overwrite )
            if( empty($this->objects ) == false )
                $this->objects = new \stdClass();

        if( $this->check( $files ) == false )
            throw new \RuntimeException('Either one or more classes do not exist in namespace ' . $this->namespace . ' : ' . print_r( $files ));

        foreach ( $files as $file )
        {

            if( strtolower( $file ) == FRAMEWORK_BASECLASS )
                continue;

            $namespace = $this->build( $file );

            Debug::message('Working with class: ' . $namespace );

            $class = new $namespace;

            $file = strtolower( $file );

            if( isset( $this->objects->$file ) )
                if( $this->objects->$file === $class )
                    continue;

            $this->objects->$file = $class;
        }

        Debug::message('Finished creating classes');

        return $this->objects;
    }

    /**
     * @param $class_name
     * @return mixed
     * @throws \RuntimeException
     */

    public function createSingular( $class_name )
    {

        if( class_exists( $this->namespace . $class_name ) == false )
            throw new \RuntimeException('Class does not exist');

        $namespace = $this->build( $class_name );
        $class_name = strtolower( $class_name );

        $this->objects->$class_name = new $namespace;

        return $this->objects->$class_name;
    }

    /**
     * @param bool $array
     * @return array|\stdClass
     */

    public function getAll( $array=false )
    {

        if( empty( $this->objects ) )
            return null;

        if( $array )
            return Format::toArray( $this->objects );

        return $this->objects;
    }

    /**
     * @param $class_name
     * @return mixed
     */

    public function get( $class_name )
    {

        $class_name = strtolower( $class_name );

        return $this->objects->$class_name;
    }

    public function getClassNames()
    {


    }

    /**
     * @param $class_name
     * @return bool
     */

    public function existsInDir( $class_name )
    {

        $files = $this->crawl();

        foreach( $files as $file )
        {

            if( strtolower( $file ) == strtolower( $class_name ) )
                return true;
        }

        return false;
    }

    /**
     * @param $class_name
     */

    public function remove( $class_name)
    {

        unset( $this->objects->$class_name );
    }

    /**
     * @param string $class_name
     * @return bool
     */

    public function exist( string $class_name )
    {

        $class_name = strtolower( $class_name  );

        return( isset( $this->objects-> $class_name ) );
    }

    /**
     * @return array
     */

    private function crawl()
    {

        $files = glob( $this->file_path . '*.php' );

        foreach ( $files as $key=>$file )
        {

            $files[ $key ] = $this->trim( $file );
        }

        return $files;
    }

    /**
     * @param array $class_names
     * @return bool
     * @throws \RuntimeException
     */

    private function check( array $class_names )
    {

        foreach ( $class_names as $class )
        {

            if ( is_string( $class ) == false )
                throw new \RuntimeException('Type Error');

            if ( class_exists( $this->namespace . $class ) == false )
                return false;
        }

        return true;
    }

    /**
     * @param $class_name
     * @return string
     */

    private function build( $class_name )
    {

        return( $this->namespace . $class_name );
    }

    /**
     * @param $filename
     * @return string
     */

    private function trim( $filename )
    {

        $exploded = explode("/", $filename );#
        $file = end( $exploded );
        $filename = explode('.', $file );

        return( $filename[0] );
    }
}