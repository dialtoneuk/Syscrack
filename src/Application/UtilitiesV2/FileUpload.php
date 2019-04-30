<?php

namespace Framework\Application\UtilitiesV2;

/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 29/08/2018
 * Time: 21:46
 */

use Delight\FileUpload\FileUpload as Delight;
use Delight\FileUpload\Throwable\Error;
use Delight\FileUpload\Throwable\FileTooLargeException;
use Delight\FileUpload\Throwable\InputNotFoundException;
use Delight\FileUpload\Throwable\InputNotSpecifiedError;
use Delight\FileUpload\Throwable\InvalidExtensionException;
use Delight\FileUpload\Throwable\TempDirectoryNotFoundError;
use Delight\FileUpload\Throwable\TempFileWriteError;
use Delight\FileUpload\Throwable\UploadCancelledError;
use Delight\FileUpload\Throwable\UploadCancelledException;

class FileUpload
{

    /**
     * @var \Exception|null
     */

    protected static $last_error = null;

    /**
     * @var Delight
     */

    protected $delight;

    /**
     * FileUpload constructor.
     * @param string $temporary_directory
     * @param string $upload_key
     */

    public function __construct( $temporary_directory=UPLOADS_TEMPORARY_DIRECTORY, $upload_key=UPLOADS_POST_KEY )
    {

        if( self::hasLastError() )
            self::setLastError();

        $this->delight = new Delight();
        $this->delight->withTargetDirectory( $temporary_directory );
        $this->delight->from( $upload_key );
    }

    /**
     * @param array $extensions
     */

    public function setAllowedExtensions( array $extensions )
    {

        $this->delight->withAllowedExtensions( $extensions );
    }

    /**
     * @param $file_name
     * @return bool|\Delight\FileUpload\File
     */

    public function save( $file_name )
    {

        if( self::hasLastError() )
            self::setLastError();

        $this->delight->withTargetFilename( $file_name );
        $result = null;

        try {
            $result = $this->delight->save();
        } catch (InputNotSpecifiedError $e) {
            self::setLastError( $e );
        } catch (TempDirectoryNotFoundError $e) {
            self::setLastError( $e );
        } catch (TempFileWriteError $e) {
            self::setLastError( $e );
        } catch (UploadCancelledError $e) {
            self::setLastError( $e );
        } catch (Error $e) {
            self::setLastError( $e );
        } catch (FileTooLargeException $e) {
            self::setLastError( $e );
        } catch (InputNotFoundException $e) {
            self::setLastError( $e );
        } catch (InvalidExtensionException $e) {
            self::setLastError( $e );
        } catch (UploadCancelledException $e) {
            self::setLastError( $e );
        }

        if( self::hasLastError() )
            return (false);

        return( $result );
    }

    /**
     * Sets the max file size in megabytes
     *
     * @param $file_size
     */

    public function setMaxFileSize( $file_size )
    {

        $this->delight->withMaximumSizeInMegabytes( $file_size );
    }

    private static function setLastError( \Exception $error=null )
    {

        self::$last_error = $error;
    }

    /**
     * @return \Exception|string
     */

    public static function getLastError()
    {

        return( self::$last_error );
    }

    /**
     * @return bool
     */

    public static function hasLastError()
    {

        return( self::$last_error === null );
    }
}