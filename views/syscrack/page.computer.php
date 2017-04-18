<?php

    use Framework\Application\Container;
    use Framework\Syscrack\Game\Computer;
    use Framework\Syscrack\Game\Utilities\PageHelper;

    $computer = new Computer();

    $pagehelper = new PageHelper();

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
                <div class="col-lg-12" onclick="window.location.href = '/game/computer/'">
                    <h1 class="page-header">
                        <span class="badge"><?=$currentcomputer->type?></span> <?=$currentcomputer->ipaddress?>
                    </h1>
                </div>

                <?php

                    Flight::render('syscrack/templates/template.computer.actions', array( 'computer' => $computer ) );
                ?>

                <div class="col-lg-8">
                    
                    <?php
                    
                        Flight::render('syscrack/templates/template.softwares', array('ipaddress' => $currentcomputer->ipaddress, 'computer' => $computer, 'hideoptions' => true ) );
                    ?>
                </div>
            </div>

            <?php

                Flight::render('syscrack/templates/template.footer', array('breadcrumb' => true ) );
            ?>
        </div>
    </body>
</html>