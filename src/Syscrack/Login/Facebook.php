<?php
namespace Framework\Syscrack\Login;

/**
 * Lewis Lancaster 2017
 *
 * Class Facebook
 *
 * @package Framework\Syscrack\Login
 */

use Facebook\Facebook as NSA;
use Framework\Application\Settings;
use Framework\Exceptions\SyscrackException;

class Facebook
{

    /**
     * @var NSA
     */

    protected $facebook;

    /**
     * Facebook constructor.
     */

    public function __construct()
    {

        $this->facebook = new NSA([
            'api_id'                => Settings::getSetting('facebook_app_id'),
            'api_secret'            => Settings::getSetting('facebook_app_secret'),
            'default_graph_version' => Settings::getSetting('facebook_app_version')
        ]);
    }

    /**
     * Gets the login URL
     *
     * @return string
     */

    public function getLoginURL()
    {

        $helper = $this->facebook->getRedirectLoginHelper();

        if( empty( $helper ) )
        {

            throw new SyscrackException();
        }

        return $helper->getLoginUrl( Settings::getSetting('facebook_redirect_url'), ['email'] );
    }

    public function getAccessToken()
    {

        $helper = $this->facebook->getRedirectLoginHelper();

        if( empty( $helper ) )
        {

            throw new SyscrackException();
        }

        return $helper->getAccessToken();
    }
}