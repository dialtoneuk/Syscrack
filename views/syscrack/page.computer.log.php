<?php


    use Framework\Application\Container;
    use Framework\Syscrack\Game\Computer;
    use Framework\Syscrack\Game\Utilities\PageHelper;
    use Framework\Syscrack\Game\Log;

    $computer = new Computer();

    $pagehelper = new PageHelper();

    $log = new Log();

    $session = Container::getObject('session');

    if( $session->isLoggedIn() )
    {

        $session->updateLastAction();
    }

    $currentcomputer = $computer->getComputer( $computer->getCurrentUserComputer() );
?>
<html>

    <?php

        Flight::render('syscrack/templates/template.header', array('pagetitle' => 'Syscrack | Game') );
    ?>
    <body>
        <div class="container">

            <?php

                Flight::render('syscrack/templates/template.navigation');
            ?>
            <div class="row">

                <?php

                    if( isset( $_GET['error'] ) )
                        Flight::render('syscrack/templates/template.alert', array( 'message' => $_GET['error'] ) );
                    elseif( isset( $_GET['success'] ) )
                        Flight::render('syscrack/templates/template.alert', array( 'message' => 'Success', 'alert_type' => 'alert-success' ) );
                ?>
                <div class="col-lg-12">
                    <h1 class="page-header" style="cursor: hand" onclick="window.location.href = '/game/computer/'">
                        <span class="badge"><?=$currentcomputer->type?></span> <?=$currentcomputer->ipaddress?>
                    </h1>
                </div>

                <?php

                    Flight::render('syscrack/templates/template.computer.actions', array( 'computer' => $computer ) );
                ?>

                <div class="col-lg-8">
                    
                    <?php
                    
                        Flight::render('syscrack/templates/template.log', array( 'ipaddress' => $currentcomputer->ipaddress, 'log' => $log, 'hideoptions' => true ))
                    ?>

                    <form method="post">
                        <button name="action" value="clear" style="width: 100%;" class="btn btn-danger" type="submit">
                            <span class="glyphicon glyphicon-alert" aria-hidden="true"></span> Clear Log
                        </button>
                    </form>
                    <button style="width: 100%;" class="btn btn-success" type="button" onclick="window.location.href = '/game/computer/log'">
                        <span class="glyphicon glyphicon-circle-arrow-down" aria-hidden="true"></span> Refresh Log
                    </button>
                </div>
            </div>

            <?php

                Flight::render('syscrack/templates/template.footer', array('breadcrumb' => true ) );
            ?>
        </div>
    </body>
</html>