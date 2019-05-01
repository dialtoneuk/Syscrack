<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 26/01/2018
 * Time: 22:31
 */

namespace Framework\Application;

use Flight;
use Framework\Application\Utilities\FileSystem;
use Framework\Exceptions\ApplicationException;

class Render
{

    /**
     * @var array
     */

    public static $stack = [];

    /**
     * Renders a template, takes a model if the mode is MVC
     *
     * @param $template
     *
     * @param array $array
     *
     * @param mixed $model
     */
    
    public static function view($template, $array=[], $model=null )
    {

        if ( Settings::getSetting('render_log') )
        {

            self::$stack[] = [
                'template' => $template,
                'array' => $array
            ];
        }

        if ( empty( $model ) == false )
        {

            if ( Settings::getSetting('render_mvc_output') == true  )
            {

                if ( Settings::getSetting('render_json_mode') == true )
                {

                    Flight::json(array(
                        'model' => $model,
                        'data' => $array ));
                }
                else
                {
                    Flight::render( self::getViewFolder() . DIRECTORY_SEPARATOR . $template, array(
                        'model' => $model,
                        'data' => $array
                    ));
                }
            }
        }
        else
        {

            Flight::render( self::getViewFolder() . DIRECTORY_SEPARATOR . $template, $array );
        }
    }

    /**
     * Redirects the header
     *
     * @param $url
     *
     * @param int $code
     */

    public static function redirect( $url, $code=303 )
    {

        if ( Settings::getSetting('render_mvc_output') == true  )
        {
            if ( Settings::getSetting('render_json_mode') == true )
            {

                Flight::json( array('redirect' => $url, 'session' => $_SESSION ) );
            }
            else
            {

                Flight::redirect( $url, $code );
            }
        }
        else
        {

            Flight::redirect( $url, $code );
        }
    }

    public static function getAssetsLocation()
    {

        return '/'
            . Settings::getSetting('syscrack_view_location')
            . '/'
            . Settings::getSetting('render_folder')
            . '/';
    }

    /**
     * Gets the current view folder
     *
     * @return mixed
     */

    private static function getViewFolder()
    {

        return Settings::getSetting('render_folder');
    }
}