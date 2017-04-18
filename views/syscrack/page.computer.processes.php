<?php

    use Framework\Application\Container;
    use Framework\Syscrack\Game\Computer;
    use Framework\Syscrack\Game\Operations;
    use Framework\Syscrack\Game\Utilities\PageHelper;

    $computer = new Computer();

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

                        if( empty( $processes ) == false )
                        {

                            foreach( $processes as $process )
                            {

                                Flight::render('syscrack/templates/template.process',array('processid' => $process->processid, 'processcclass' => $operations->findProcessClass( $process->process ) ) );
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
                </div>
            </div>

            <?php

                Flight::render('syscrack/templates/template.footer', array('breadcrumb' => true ) );
            ?>
        </div>
    </body>
</html>