<?php
namespace Framework\Application\Utilities;

/**
 * Lewis Lancaster 2016
 *
 * Class Factory
 *
 * @package Framework\Views
 */

use Framework\Exceptions\ViewException;
use Framework\Syscrack\Game\Structures\Software;
use ReflectionClass;

class Factory
{

	/**
	 * Holds the namespace
	 *
	 * @var string
	 */

	public $namespace;

	/**
	 * Holds an array of the created classes
	 *
	 * @var array
	 */

	protected $classes = array();

	/**
	 * Factory constructor.
	 *
	 * @param string $namespace
	 */

	public function __construct ( $namespace = 'Framework\\Views\\Pages\\' )
	{

		$this->namespace = $namespace;
	}

	/**
	 * Creates the class
	 *
	 * @param $class
	 *
	 * @return mixed
	 */

	public function createClass( $class )
	{

		$classnamespace = $this->getClass( $class );

		if( $classnamespace == $this->namespace )
		{

			throw new ViewException('No Class Given');
		}

		$pageclass = new $classnamespace;

		if( empty( $pageclass) )
		{

			throw new ViewException('Class is Empty');
		}

        $this->classes[ $class ] = $pageclass;

		return $pageclass;
	}

    /**
     * Returns true if a clas exists
     *
     * @param $class
     *
     * @return bool
     */

	public function classExists( $class )
    {

        if( class_exists( $this->namespace . ucfirst( $class ) ) )
        {

            return true;
        }

        return false;
    }

    /**
     * Returns true if the factory has this kind of class
     *
     * @param $name
     *
     * @return bool
     */

    public function hasClass( $name )
    {

        if( $this->findClass( $name ) == null )
        {

            return false;
        }

        return true;
    }

	/**
	 * Finds a class by its name
	 *
	 * @param $name
	 *
	 * @return mixed|null
	 */

	public function findClass( $name )
	{
		
		foreach( $this->classes as $class )
		{
			
			$reflection = new ReflectionClass( $class );
			
			if( empty( $reflection ) )
			{
				
				throw new ViewException();
			}
			
			if( strtolower( $reflection->getShortName() ) == strtolower( $name ) )
			{
				
				return $class;
			}
		}
		
		return null;
	}

    /**
     * Gets all of the classes
     *
     * @return array|\stdClass|Software
     */

	public function getAllClasses()
    {

        return $this->classes;
    }

	/**
	 * Returns the path of this class
	 *
	 * @param $class
	 *
	 * @return string
	 */

	private function getClass( $class )
	{

		return sprintf('%s%s', $this->namespace, ucfirst( $class ) );
	}
}