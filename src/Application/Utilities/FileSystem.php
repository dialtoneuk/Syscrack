<?php
namespace Framework\Application\Utilities;

/**
 * Lewis Lancaster 2016
 *
 * Class FileSystem
 *
 * @package Framework\Application\Utilities
 */

use Framework\Application\Settings;
use Framework\Exceptions\ApplicationException;

class FileSystem
{

	/**
	 * Reads a file
	 *
	 * @param $file
	 *
	 * @return string
	 */

	public static function read( $file )
	{

        if( is_dir( $file ) )
        {

            throw new ApplicationException();
        }

        if( self::hasFileExtension( $file ) == false )
        {

            $file = $file . Settings::getSetting('filesystem_default_extension');
        }

		if( file_exists( self::getFilePath( $file ) ) == false )
		{

			throw new ApplicationException();
		}

		$file = file_get_contents( self::getFilePath( $file ) );

		if( empty( $file ) )
		{

			throw new ApplicationException();
		}

		return $file;
	}

    /**
     * Reads Json
     *
     * @param $file
     *
     * @return mixed
     */

	public static function readJson( $file )
    {

        if( is_dir( $file ) )
        {

            throw new ApplicationException();
        }

        if( self::hasFileExtension( $file ) == false )
        {

            $file = $file . '.json';
        }

        if( self::fileExists( $file ) == false )
        {

            return null;
        }

        return json_decode( self::read( $file ), true );
    }

    /**
     * Writes Json
     *
     * @param $file
     *
     * @param array $array
     */

    public static function writeJson( $file, array $array = [] )
    {

        if( is_dir( $file ) )
        {

            throw new ApplicationException();
        }

        if( self::hasFileExtension( $file ) == false )
        {

            $file = $file . '.json';
        }

        self::write( $file, json_encode( $array, JSON_PRETTY_PRINT ) );
    }

	/**
	 * Writes a file
	 *
	 * @param $file
	 *
	 * @param $data
	 */

	public static function write( $file, $data )
	{

        if( is_dir( $file ) )
        {

            throw new ApplicationException();
        }

        if( self::hasFileExtension( $file ) == false )
        {

            $file = $file . Settings::getSetting('filesystem_default_extension');
        }

	    $directories = self::getDirectories( $file );

	    if( self::directoryExists( $directories ) == false )
        {

            throw new ApplicationException( 'Directory does not exist: ' . $directories );
        }

        if( is_string( $data ) == false )
		{

			$data = (string)$data;
		}

		file_put_contents( self::getFilePath( $file ), $data );
	}

	/**
	 * Returns true if the file exists
	 *
	 * @param $file
	 *
	 * @return bool
	 */

	public static function fileExists( $file )
	{

        if( is_dir( $file ) )
        {

            throw new ApplicationException();
        }

        if( self::hasFileExtension( $file ) == false )
        {

            $file = $file . Settings::getSetting('filesystem_default_extension');
        }

		if( file_exists( self::getFilePath( $file ) ) == false )
		{

			return false;
		}

		if( is_file( $file ) == false )
		{

			return false;
		}

		return true;
	}

	/**
	 * Checks if a directory exists
	 *
	 * @param $path
	 *
	 * @return bool
	 */

	public static function directoryExists( $path )
	{

	    if( is_dir( $path ) == false )
        {

            throw new ApplicationException();
        }

		if( file_exists( self::getFilePath( $path ) ) == false )
		{

			return false;
		}

		return true;
	}

	/**
	 * Appends a files
	 *
	 * @param $file
	 *
	 * @param $data
	 */

	public static function append( $file, $data )
	{

	    if( is_dir( $file ) )
        {

            throw new ApplicationException();
        }

	    if( self::hasFileExtension( $file ) == false )
        {

            $file = $file . Settings::getSetting('filesystem_default_extension');
        }

		if( file_exists( self::getFilePath( $file ) ) == false )
		{

			throw new ApplicationException();
		}

		$old_file = file_get_contents( self::getFilePath( $file ) );

		if( empty( $old_File ) )
		{

			$old_file = "";
		}

		file_put_contents( self::getFilePath( $file ), self::addNewLine( $old_file, $data ) );
	}


    /**
     * Gets the files in a directory
     *
     * @param $path
     *
     * @param string $suffix
     *
     * @return array|null
     */

	public static function getFilesInDirectory( $path, $suffix='php' )
    {

        if( is_dir( $path ) == false )
        {

            throw new ApplicationException();
        }

        if( self::directoryExists( $path ) == false )
        {

            throw new ApplicationException();
        }

        $files = glob( self::getFilePath( $path ) . "*.{$suffix}" );

        if( empty( $files ) )
        {

            return null;
        }

        return $files;
    }

	/**
	 * Creates a directory
	 *
	 * @param $path
	 */

	public static function createDirectory( $path, $access=null )
	{

	    if( is_dir( $path ) == false )
        {

            throw new ApplicationException();
        }

        if( $access == null )
        {

            $access = Settings::getSetting('filesystem_default_access');
        }

	    if( substr( $path, -1 ) == '/' )
        {

            $path = substr( $path, 0, -1);
        }

        mkdir( self::getFilePath( $path ), $access, true);
	}

	/**
	 * Deletes a file
	 *
	 * @param $file
	 */

	public static function delete( $file )
	{

	    if( self::hasFileExtension( $file ) == false )
        {

            $file = $file . Settings::getSetting('filesystem_default_extension');
        }

		if( file_exists( self::getFilePath( $file ) ) == false )
		{

			throw new ApplicationException();
		}

		unlink( self::getFilePath( $file ) );
	}

	/**
	 * Gets the file path
	 *
	 * @param $file
	 *
	 * @return string
	 */

	public static function getFilePath( $file )
	{

		return sprintf('%s'.Settings::getSetting('filesystem_seperator').'%s', self::getRoot(), $file );
	}

	/**
	 * Gets the directories of a file
	 *
	 * @param $file
	 *
	 * @return string
	 */

	public static function getDirectories( $file )
	{

		$path = explode( Settings::getSetting('filesystem_seperator'), $file );

		if( empty( $path ) )
		{

			throw new ApplicationException();
		}

		array_pop( $path );

		return implode( Settings::getSetting('filesystem_seperator'), $path );
	}

    /**
     * Removes the file extension
     *
     * @param $file
     *
     * @return mixed
     */

	public static function removeFileExtension( $file )
    {

        $file = explode('.', $file );

        if( empty( $file ) )
        {

            throw new ApplicationException();
        }

        return reset( $file );
    }

    /**
     * Gets the file name
     *
     * @param $file
     *
     * @return mixed
     */

    public static function getFileName( $file )
    {

        if( explode('.', $file ) != null )
        {

            $file = self::removeFileExtension( $file );
        }

        $file = explode( Settings::getSetting('filesystem_seperator'), $file );

        if( empty( $file ) )
        {

            throw new ApplicationException();
        }

        return end( $file );
    }

    /**
     * Returns true if we have a file extension
     *
     * @param $file
     *
     * @return bool
     */

    public static function hasFileExtension( $file )
    {

        if( empty( explode('.', $file ) ) )
        {

            return false;
        }

        return true;
    }

	/**
	 * Adds a new line to the end of each string to help the handle
	 *
	 * @param $blob
	 *
	 * @param $data
	 *
	 * @return string
	 */

	private static function addNewLine( $blob, $data )
	{

		return sprintf("%s\n%s", $blob, $data);
	}

	/**
	 * Gets the root of this application
	 *
	 * @return mixed
	 */

	private static function getRoot()
	{

		return Settings::getSetting('filesystem_root');
	}

	/**
	 * Stitches the pattern to the path
	 *
	 * @param $path
	 *
	 * @param $pattern
	 *
	 * @return string
	 */

	private function stitchPattern( $path, $pattern )
	{

		return sprintf("%s" . Settings::getSetting('filesystem_seperator') . "%s", $path, $pattern);
	}
}