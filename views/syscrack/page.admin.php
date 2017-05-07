<?php

    use Framework\Application\Container;
    use Framework\Application\Settings;
    use Framework\Syscrack\Game\Utilities\PageHelper;
    use Framework\Syscrack\User;

    $session = Container::getObject('session');

    if( $session->isLoggedIn() )
    {

        $session->updateLastAction();
    }

    if( isset( $user ) == false )
    {

        $user = new User();
    }

    if( isset( $pagehelper ) == false )
    {

        $pagehelper = new PageHelper();
    }
?>
<html>

    <?php

        Flight::render('syscrack/templates/template.header', array('pagetitle' => 'Syscrack | Admin'));
    ?>
    <body>
        <div class="container">

            <?php

                Flight::render('syscrack/templates/template.navigation');
            ?>
            <div class="row">
                <div class="col-lg-12">
                    <?php

                        if( isset( $_GET['error'] ) )
                            Flight::render('syscrack/templates/template.alert', array( 'message' => $_GET['error'] ) );
                        elseif( isset( $_GET['success'] ) )
                            Flight::render('syscrack/templates/template.alert', array( 'message' => 'Success', 'alert_type' => 'alert-success' ) );
                    ?>
                </div>
            </div>
            <div class="row">

                <?php

                    Flight::render('syscrack/templates/template.admin.options');
                ?>
                <div class="col-lg-8">
                    <div class="panel panel-default">
                        <div class="panel-body" style="padding: 10px 15px;">
                            <h4>
                                Admin Panel
                            </h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="panel panel-success">
                                <div class="panel-heading">
                                    Users
                                </div>
                                <div class="panel-body text-center">
                                    <h3><?=$user->getUsersCount()?></h3><span class="small"> Users</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="panel panel-success">
                                <div class="panel-heading">
                                    Online
                                </div>
                                <div class="panel-body text-center">
                                    <h3><?=$session->getActiveSessions()->count()?></h3><span class="small"> Users</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                        if( Settings::getSetting('developer_disabled') == false )
                        {

                            ?>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="panel panel-danger">
                                            <div class="panel-heading">
                                                Warning
                                            </div>
                                            <div class="panel-body">
                                                Your developer area is still enabled, it is highly suggested that if you are currently running a live version of Syscrack that you
                                                <strong>disable the developer area.</strong> Please <a href="/developer/disable/">click here to do so!</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php
                        }
                    ?>
                </div>
            </div>

            <?php

                Flight::render('syscrack/templates/template.footer', array('breadcrumb' => true ) );
            ?>
        </div>
    </body>
</html>
