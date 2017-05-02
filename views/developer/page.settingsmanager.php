<?php

    use Framework\Application\Settings;
    use Framework\Application\Utilities\PostHelper;

    /**
 * Lewis Lancaster 2017
 *
 * Class SettingsManager
 */

class SettingsManager
{

    /**
     * Gets our settings
     *
     * @return array
     */

    public function getSettings()
    {

        if( Settings::checkSettings() == false )
        {

            self::displayError('Unable to get settings');
        }

        return Settings::getSettings();
    }

    /**
     * Updates the setting
     *
     * @param $setting_name
     *
     * @param $setting_value
     */

    public function updateSetting( $setting_name, $setting_value )
    {

        if( Settings::hasSetting( $setting_name ) == false )
        {

            self::displayError('Setting does not exist');
        }

        Settings::updateSetting( $setting_name, $setting_value );

        Settings::writeSettings();
    }

    /**
     * Creaates a new setting
     *
     * @param $setting_name
     *
     * @param $setting_value
     */

    public function createSetting( $setting_name, $setting_value )
    {

        if( Settings::hasSetting( $setting_name ) )
        {

            self::displayError('Setting already exist, please choose another name');
        }

        Settings::addSetting( $setting_name, $setting_value );

        Settings::writeSettings();
    }

    /**
     * Deletes a setting
     *
     * @param $setting_name
     */

    public function deleteSetting( $setting_name )
    {

        if( Settings::hasSetting( $setting_name ) == false )
        {

            self::displayError('Setting does not exist');
        }

        Settings::removeSetting( $setting_name );

        Settings::writeSettings();
    }

    /**
     * Checks if a string is json or not
     *
     * @param $setting_value
     *
     * @return bool
     */

    public function isJson( $setting_value )
    {

        json_encode( $setting_value );

        if( json_last_error() !== JSON_ERROR_NONE )
        {

            return false;
        }

        return true;
    }

    /**
     * Converts a string to a bool
     *
     * @param $setting_value
     *
     * @return bool
     */

    public function stringToBool( $setting_value )
    {

        if( strtolower( $setting_value ) == 'true' )
        {

            return true;
        }

        return false;
    }

    /**
     * Displays an error
     *
     * @param $error
     */

    public function displayError( $error )
    {

        Flight::redirect('/developer/settingsmanager/?error='. $error);
    }
}

$class = new SettingsManager();
?>
<html lang="en">

    <?php

        Flight::render('developer/templates/template.header', array( 'pagetitle' => 'Settings Manager'));
    ?>

    <style>
        :target {
            animation: border-pulsate 5s;
            border: 1px solid #ddd;
        }

        @keyframes border-pulsate {
            0%   { border-color: #337ab7; }
            100% { border-color: #ddd; }
        }
    </style>

    <body>
        <div class="container">

            <?php

                Flight::render('developer/templates/template.navigation');

                if( isset( $_GET['error'] ) )
                    Flight::render('developer/templates/template.alert', array( 'message' => $_GET['error'] ) );
                elseif( isset( $_GET['success'] ) )
                    Flight::render('developer/templates/template.alert', array( 'message' => 'Success', 'alert_type' => 'alert-success' ) );
            ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header">
                        <h1>Settings Manager</h1>
                    </div>
                    <p class="lead">
                        Below, you'll find a list of all the settings you currently have in your settings file, use this
                        tool to edit, delete or add new settings to the framework
                    </p>
                    <p>
                        To save a setting, select a setting you would like to modify and edit the corresponding input box and then
                        press the save button. To delete a setting, simply hit the delete button corresponding to the setting you want
                        to remove. To add a new setting, use the form at the <a href="#createsetting">bottom of this page.</a>
                    </p>
                    <p>
                        It is strongly advised you <strong>do not edit, delete or modify in any way any of the various developer
                        and filesystem settings. Use this area with extreme caution!</strong> If you are editing a setting that you
                        want to be a boolean, make sure to simply use 'true' or 'false'.
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">

                    <h5 style="color: #ababab" class="text-uppercase">
                        Settings
                    </h5>

                    <?php

                        $settings = $class->getSettings();

                        foreach( $settings as $key=>$value )
                        {
                    ?>
                        <form method="post">
                            <div class="panel panel-default" id="setting_<?=$key?>">
                                <div class="panel-body">
                                    <p>
                                        <?=$key?>
                                    </p>

                                    <?php

                                        if( is_array( $value ) == false && is_bool( $value ) == false && Settings::hasParsableData( $value ) )
                                        {

                                            ?>

                                                <p class="small text-uppercase" style="color: #ababab">
                                                    PHP Eval String
                                                </p>
                                                <p class="small" style="color: #ababab">
                                                    <?=addslashes( Settings::parseSetting( $value ) )?>
                                                </p>
                                                <div class="input-group">
                                                    <span class="input-group-btn">
                                                        <button class="btn btn-default" type="submit" name="action" value="save">Save</button>
                                                        <button class="btn btn-default" type="submit" name="action" value="delete">Delete</button>
                                                        <button class="btn btn-default" type="button" onclick='window.prompt("Copy to clipboard: Ctrl+C, Enter","<?='http://' . $_SERVER['HTTP_HOST'] . '/developer/settingsmanager/#setting_' . $key?>");'>Link</button>
                                                    </span>
                                                    <input name="<?=$key?>" type="text" class="form-control" value="<?=$value?>">
                                                </div>
                                            <?php
                                        }
                                        elseif( is_bool( $value ) )
                                        {

                                    ?>
                                        <p class="small text-uppercase" style="color: #ababab">
                                            Boolean
                                        </p>
                                        <div class="input-group">
                                             <span class="input-group-btn">
                                                <button class="btn btn-default" type="submit" name="action" value="save">Save</button>
                                                <button class="btn btn-default" type="submit" name="action" value="delete">Delete</button>
                                                <button class="btn btn-default" type="button" onclick='window.prompt("Copy to clipboard: Ctrl+C, Enter","<?='http://' . $_SERVER['HTTP_HOST'] . '/developer/settingsmanager/#setting_' . $key?>");'>Link</button>
                                            </span>

                                            <?php

                                                if( $value == true )
                                                {

                                                    ?>

                                                        <input name="<?=$key?>" type="text" class="form-control" value="True">
                                                    <?php

                                                }
                                                else
                                                {

                                                    ?>

                                                        <input name="<?=$key?>" type="text" class="form-control" value="False">
                                                    <?php
                                                }
                                            ?>
                                        </div>

                                    <?php
                                        }
                                        elseif( empty( $value ) )
                                        {
                                            ?>

                                                <p class="small text-uppercase" style="color: #ababab">
                                                    Empty
                                                </p>
                                                <div class="input-group">
                                                        <span class="input-group-btn">
                                                            <button class="btn btn-default" type="submit" name="action" value="save">Save</button>
                                                            <button class="btn btn-default" type="submit" name="action" value="delete">Delete</button>
                                                            <button class="btn btn-default" type="button" onclick='window.prompt("Copy to clipboard: Ctrl+C, Enter","<?='http://' . $_SERVER['HTTP_HOST'] . '/developer/settingsmanager/#setting_' . $key?>");'>Link</button>
                                                        </span>
                                                    <input name="<?=$key?>" type="text" class="form-control" value="<?=$value?>">
                                                </div>
                                            <?php
                                        }
                                        elseif( is_array( $value ) )
                                        {

                                            ?>

                                                <p class="small text-uppercase" style="color: #ababab">
                                                    Array
                                                </p>
                                                <div class="input-group">
                                                            <span class="input-group-btn">
                                                                <button class="btn btn-default" type="submit" name="action" value="save">Save</button>
                                                                <button class="btn btn-default" type="submit" name="action" value="delete">Delete</button>
                                                                <button class="btn btn-default" type="button" onclick='window.prompt("Copy to clipboard: Ctrl+C, Enter","<?='http://' . $_SERVER['HTTP_HOST'] . '/developer/settingsmanager/#setting_' . $key?>");'>Link</button>
                                                            </span>
                                                    <input name="<?=$key?>" type="text" class="form-control" value="<?=htmlspecialchars( json_encode( $value ) )?>">
                                                </div>
                                            <?php
                                        }
                                        else
                                        {
                                            ?>

                                                <p class="small text-uppercase" style="color: #ababab">
                                                    String
                                                </p>
                                                <div class="input-group">
                                                    <span class="input-group-btn">
                                                        <button class="btn btn-default" type="submit" name="action" value="save">Save</button>
                                                        <button class="btn btn-default" type="submit" name="action" value="delete">Delete</button>
                                                        <button class="btn btn-default" type="button" onclick='window.prompt("Copy to clipboard: Ctrl+C, Enter","<?='http://' . $_SERVER['HTTP_HOST'] . '/developer/settingsmanager/#setting_' . $key?>");'>Link</button>
                                                    </span>
                                                    <input name="<?=$key?>" type="text" class="form-control" value="<?=$value?>">
                                                </div>
                                            <?php
                                        }
                                    ?>
                                </div>
                            </div>
                        </form>
                    <?php
                        }
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary" id="settings_creator">
                        <div class="panel-heading">
                            <h3 class="panel-title">Settings Creator</h3>
                        </div>
                        <div class="panel-body">
                            <form method="post">
                                <div class="input-group input-group-lg">
                                    <span class="input-group-addon" id="settingname">@</span>
                                    <input type="text" name="settings_name" class="form-control" placeholder="Setting Name" aria-describedby="settingname">
                                    <span class="input-group-addon" id="settingvalue">=</span>
                                    <input type="text" name="settings_value" class="form-control" placeholder="Setting Value" aria-describedby="settingvalue">
                                </div>
                                <button class="btn btn-primary btn-block btn-lg" type="submit" name="action" value="create" style="margin-top: 2.5%">
                                    Create
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <?php

                Flight::render('developer/templates/template.footer', array( 'breadcrumb' => true ));
            ?>
        </div>
    </body>
</html>

<?php

if( PostHelper::checkPostData( ['action'] ) )
{

    $action = $_POST['action'];

    if( $action == 'save' )
    {

        foreach( $_POST as $key=>$value )
        {

            if( $key == 'action' )
            {
                continue;
            }

            if( Settings::hasSetting( $key ) == false )
            {

                $class->displayError('Settings given by post data have invalid keys');

                exit;
            }

            if( strtolower( $value ) == 'true' || strtolower( $value ) == 'false' )
            {

                $value = $class->stringToBool( $value );
            }

            if( $class->isJson( $value ) && is_string( $value ) )
            {

                $value = json_decode( $value, true );
            }

            $class->updateSetting( $key, $value );

            Flight::redirect( '/developer/settingsmanager?success' );

            exit;
        }
    }

    if( $action == 'delete' )
    {

        foreach( $_POST as $key=>$value )
        {

            if( $key == 'action' )
            {

                continue;
            }

            if( Settings::hasSetting( $key ) == false )
            {

                $class->displayError('Settings given by post data have invalid keys');

                exit;
            }

            $class->deleteSetting( $key );

            Flight::redirect( '/developer/settingsmanager?success' );

            exit;
        }
    }

    if( $action == 'create' )
    {

        if( PostHelper::checkForRequirements( ['settings_name','settings_value'] ) == false )
        {

            $class->displayError('Missing required information, fill out the fields!');

            exit;
        }

        $data = PostHelper::returnRequirements( ['settings_name', 'settings_value'] );

        if( $data['settings_name'] == 'action' )
        {

            $class->displayError('You cant name your setting that, sorry');

            exit;
        }

        if( Settings::hasSetting( $data['settings_name'] ) )
        {

            $class->displayError('Setting already exists, try picking a different name');

            exit;
        }

        if( strtolower( $data['settings_value'] ) == 'true' || strtolower( $data['settings_value'] ) == 'false' )
        {

            $data['settings_value'] = $class->stringToBool( $data['settings_value'] );
        }

        $class->createSetting( $data['settings_name'], $data['settings_value'] );

        Flight::redirect( '/developer/settingsmanager?success' );

        exit;
    }

    $class->displayError('Invalid action');
}
?>