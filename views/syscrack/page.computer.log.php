<?php


    use Framework\Application\Container;
    use Framework\Syscrack\Game\Computers;
    use Framework\Syscrack\Game\Log;
    use Framework\Syscrack\Game\Utilities\PageHelper;

    $computer = new Computers();

    $pagehelper = new PageHelper();

    $log = new Log();

    $session = Container::getObject('session');

    if( $session->isLoggedIn() )
    {

        $session->updateLastAction();
    }

    $currentcomputer = $computer->getComputer( $computer->getCurrentUserComputer() );
?>

<!DOCTYPE html>
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
                <div class="col-lg-12" onclick="window.location.href = '/game/computer/'">
                    <h5 style="color: #ababab" class="text-uppercase">
                        <span class="badge"><?=$currentcomputer->type?></span> <?=$currentcomputer->ipaddress?>
                    </h5>
                </div>
            </div>
            <div class="row" style="margin-top: 1.5%;">

                <?php

                    Flight::render('syscrack/templates/template.computer.actions', array( 'computer' => $computer ) );
                ?>
                <div class="col-md-8">

                    <?php
                    
                        Flight::render('syscrack/templates/template.log', array( 'ipaddress' => $currentcomputer->ipaddress, 'log' => $log, 'hideoptions' => true ))
                    ?>
                    <div class="btn-group-vertical" style="width: 100%;">
                        <button class="btn btn-danger" type="button" onclick="window.location.href = '/computer/actions/clear'">
                            <span class="glyphicon glyphicon-alert" aria-hidden="true"></span> Clear Log
                        </button>
                        <button class="btn btn-success" type="button" onclick="window.location.href = '/computer/log/'">
                            <span class="glyphicon glyphicon-circle-arrow-down" aria-hidden="true"></span> Refresh Log
                        </button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <?php

                        Flight::render('syscrack/templates/template.footer', array('breadcrumb' => true ) );
                    ?>
                </div>
            </div>
        </div>
    </body>
</html>