<?php

    use Framework\Application\Container;
    use Framework\Syscrack\Game\Computers;
    use Framework\Syscrack\Game\Operations;
    use Framework\Syscrack\Game\Utilities\PageHelper;

    $computer = new Computers();

    $pagehelper = new PageHelper();

    $operations = new Operations();

    $session = Container::getObject('session');

    if( $session->isLoggedIn() )
    {

        $session->updateLastAction();
    }

    $currentcomputer = $computer->getComputer( $computer->getCurrentUserComputer() );

    $processes = $operations->getComputerProcesses( $currentcomputer->computerid );
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
                <div class="col-lg-8">

                    <?php

                        if( empty( $processes ) == false )
                        {

                            foreach( $processes as $process )
                            {

                                ?>
                                    <div class="row">
                                        <?php Flight::render('syscrack/templates/template.process',array('processid' => $process->processid, 'processcclass' => $operations->findProcessClass( $process->process ), 'refresh' => true ) ); ?>
                                    </div>
                                <?php
                            }
                        }
                        else
                        {

                            ?>
                            <div class="panel panel-danger">
                                <div class="panel-heading">
                                    Notice
                                </div>
                                <div class="panel-body">
                                    Computer currently has no processes, maybe you should hack something?
                                </div>
                            </div>
                            <?php
                        }
                    ?>
                    <div class="row">
                        <div class="col-sm-12">
                            <button style="width: 100%;" class="btn btn-info" onclick="window.location.reload()">
                                Refresh
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <?php

                Flight::render('syscrack/templates/template.footer', array('breadcrumb' => true ) );
            ?>
        </div>
    </body>
</html>