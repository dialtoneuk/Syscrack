<?php

    use Framework\Application\Container;
    use Framework\Application\Settings;
    use Framework\Syscrack\Game\Computers;
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

    if( isset( $computers) == false )
    {

        $computers= new Computers();
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
                            Flight::render('syscrack/templates/template.alert', array( 'message' => Settings::getSetting('alert_success_message'), 'alert_type' => 'alert-success' ) );
                    ?>
                </div>
            </div>
            <div class="row">

                <?php

                    Flight::render('syscrack/templates/template.admin.options');
                ?>
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-sm-12">
                            <h5 style="color: #ababab" class="text-uppercase">
                                Displaying <?=Settings::getSetting('syscrack_admin_computer_count')?> latest computers
                            </h5>
                            <form method="post">
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control" name="query" placeholder="1.1.1.1" aria-label="...">
                                    <div class="input-group-btn">
                                        <button class="btn btn-default">
                                            Find
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 1.5%;">
                        <?php

                            $computers = $computers->getAllComputers( Settings::getSetting('syscrack_admin_computer_count'));

                            if( empty( $computers ) )
                            {

                                ?>
                                    <div class="col-sm-12">
                                        <div class="panel panel-danger">
                                            <div class="panel-heading">
                                                No Computers Found
                                            </div>
                                            <div class="panel-body">
                                                It appears that no computers were found on your system... this is strange?
                                            </div>
                                        </div>
                                    </div>
                                <?php
                            }
                            else
                            {

                                $computers = $computers->reverse();

                                foreach( $computers as $key=>$value )
                                {

                                    ?>
                                        <div class="col-sm-12">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <span class="badge"><?=$value->type?></span> <a href="/game/internet/<?=$value->ipaddress?>"><?=$value->ipaddress?></a><span class="badge" style="float: right">#<?=$value->computerid?></span>
                                                </div>
                                                <div class="panel-body">
                                                    <div class="btn-grou btn-group-justified" role="group" aria-label="...">
                                                        <div class="btn-group" role="group" onclick="window.location.href = '/admin/computer/<?=$value->computerid?>'">
                                                            <button type="button" class="btn btn-warning btn-sm">Edit</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php
                                }
                            }
                        ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <p class="text-center">
                        <a onclick="$('html, body').animate({ scrollTop: 0 }, 'fast');" style="cursor: pointer;">
                            Back to the top...
                        </a>
                    </p>
                </div>
            </div>

            <?php

                Flight::render('syscrack/templates/template.footer', array('breadcrumb' => true ) );
            ?>
        </div>
    </body>
</html>
