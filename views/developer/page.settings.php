<?php

    use Framework\Application\Settings;

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
            ?>
            <div class="row">
                <div class="col-lg-12">
                    <?php

                        if( isset( $_GET['error'] ) )
                            Flight::render('syscrack/templates/template.alert', array( 'message' => $_GET['error'] ) );
                        elseif( isset( $_GET['success'] ) )
                            Flight::render('syscrack/templates/template.alert', array( 'message' => Settings::getSetting('alert_success_message'), 'alert_type' => 'alert-success' ) );
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <h5 style="color: #ababab" class="text-uppercase">
                        Settings Manager
                    </h5>
                    <p class="lead">
                        To your right, you'll find a list of all the settings you currently have in your settings file, use this
                        tool to edit, delete or add new settings to the framework
                    </p>
                    <p>
                        To save a setting, select a setting you would like to modify and edit the corresponding input box and then
                        press the save button. To delete a setting, simply hit the delete button corresponding to the setting you want
                        to remove.
                    </p>
                    <p>
                        It is strongly advised you <strong>do not edit, delete or modify in any way any of the various developer_,
                        middlewares_, database_ and filesytem_ settings unless you know what you are doing. Use this area with extreme caution!</strong>
                        If you are editing a setting that you want to be a boolean, make sure to simply use 'true' or 'false'. It is highly recommended
                        that you <strong>keep strict to the type</strong> of the setting, meaning don't change an array to a bool, else you will most probably
                        experience errors!
                    </p>
                    <p>
                        The framework automatically parses anything in between <strong><?=htmlspecialchars('<>')?></strong> into a php eval string, this can be disabled
                        via editing <a href="#settings_php_enabled">this setting</a> to false.
                    </p>
                    <div class="row">
                        <div class="col-md-12">
                            <h5 style="color: #ababab" class="text-uppercase">
                                Settings Creator
                            </h5>
                            <div class="panel panel-default" id="settingcreator">
                                <div class="panel-body">
                                    <form method="post" style="margin-top: 2.5%;">
                                        <div class="input-group input-group-md">
                                            <span class="input-group-addon" id="settingname">@</span>
                                            <input type="text" name="setting_name" class="form-control" placeholder="Setting Name" aria-describedby="settingname">
                                            <span class="input-group-addon" id="settingvalue">=</span>
                                            <input type="text" name="setting_value" class="form-control" placeholder="Setting Value" aria-describedby="settingvalue">
                                        </div>
                                        <button class="btn btn-default btn-block btn-sm" type="submit" name="action" value="create" style="margin-top: 2.5%">
                                            Create
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5 style="color: #ababab" class="text-uppercase">
                                Search for a setting
                            </h5>
                            <div class="panel panel-default" id="settingsearch">
                                <div class="panel-body">
                                    <form method="post" style="margin-top: 2.5%;">
                                        <div class="input-group input-group-md">
                                            <span class="input-group-addon" id="settingname">@</span>
                                            <input type="text" id="setting" class="form-control" placeholder="Setting Name" aria-describedby="settingname">
                                        </div>
                                        <button class="btn btn-default btn-block btn-sm" type="button" onclick="window.location.href = '/developer/settings/#' + document.getElementById('setting').value" style="margin-top: 2.5%">
                                            Search
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6" >

                    <h5 style="color: #ababab" class="text-uppercase">
                        Your Settings
                    </h5>

                    <?php

                        $settings = Settings::getSettings();

                        try
                        {

                            foreach( $settings as $key=>$value )
                            {
                                ?>
                                <form method="post">
                                    <div class="panel panel-default" id="<?=$key?>">
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
                                                        <?=htmlspecialchars( addslashes( Settings::parseSetting( $value ) ) )?>
                                                    </p>
                                                    <div class="input-group">
                                                        <span class="input-group-btn">
                                                            <button class="btn btn-default" type="submit" name="action" value="save">Save</button>
                                                            <button class="btn btn-default" type="submit" name="action" value="delete">Delete</button>
                                                            <button class="btn btn-default" type="button" onclick='window.prompt("Copy to clipboard: Ctrl+C, Enter","<?='http://' . $_SERVER['HTTP_HOST'] . '/developer/settings/#' . $key?>");'>Link</button>
                                                        </span>
                                                        <input name="setting_value" type="text" class="form-control" value="<?=htmlspecialchars( $value )?>">
                                                        <input type="hidden" name="setting_name" value="<?=$key?>">
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

                                                                <input name="setting_value" type="text" class="form-control" value="True">
                                                                <?php

                                                            }
                                                            else
                                                            {

                                                                ?>

                                                                <input name="setting_value" type="text" class="form-control" value="False">
                                                                <?php
                                                            }
                                                        ?>

                                                        <input type="hidden" name="setting_name" value="<?=$key?>">
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
                                                        <input name="setting_value" type="text" class="form-control" value="<?=htmlspecialchars( $value )?>">
                                                        <input type="hidden" name="setting_name" value="<?=$key?>">
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
                                                        <input name="setting_value" type="text" class="form-control" value="<?=htmlspecialchars( json_encode( $value ) )?>">
                                                        <input type="hidden" name="setting_name" value="<?=$key?>">
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
                                                        <input name="setting_value" type="text" class="form-control" value="<?=htmlspecialchars( $value )?>">
                                                        <input type="hidden" name="setting_name" value="<?=$key?>">
                                                    </div>
                                                    <?php
                                                }
                                            ?>
                                        </div>
                                    </div>
                                </form>
                                <?php
                            }
                        }
                        catch( Exception $error )
                        {

                            ?>
                            <div class="panel panel-danger">
                                <div class="panel-heading">
                                    Failed to display setting
                                </div>
                                <div class="panel-body">
                                    <?=$error->getMessage()?>
                                </div>
                            </div>
                            <?php
                        }
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <p class="text-center">
                        <a href="#settingcreator">
                            Back to the top...
                        </a>
                    </p>
                </div>
            </div>

            <?php

                Flight::render('developer/templates/template.footer', array( 'breadcrumb' => true ));
            ?>
        </div>
    </body>
</html>