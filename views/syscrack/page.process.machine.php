<?php

    $session = \Framework\Application\Container::getObject('session');

    if( $session->isLoggedIn() )
    {

        $session->updateLastAction();
    }

    $pagehelper = new \Framework\Syscrack\Game\Utilities\PageHelper();

    if( isset( $operations ) == false )
    {

        $operations = new \Framework\Syscrack\Game\Operations();
    }
?>

<!DOCTYPE html>
<html>

    <?php

        Flight::render('syscrack/templates/template.header', array('pagetitle' => 'Syscrack | Processes') );
    ?>
    <body>
        <div class="container">

            <?php

                Flight::render('syscrack/templates/template.navigation');
            ?>
            <div class="row">
                <div class="col-lg-3">
                    <div class="list-group">
                        <a href="/processes/" class="list-group-item">
                            <h4 class="list-group-item-heading">View All Processes</h4>
                            <p class="list-group-item-text">View all of the process currently being conducted
                            on all of your virtual computers.</p>
                        </a>
                    </div>
                    <div class="list-group">
                        <a href="/processes/computer/<?=$computerid?>" class="list-group-item active">
                            <h4 class="list-group-item-heading"><?=$ipaddress?></h4>
                            <p class="list-group-item-text">You may click any of the drop down boxes inside the process panels
                            to delete a process. If a process is complete, refresh the page and use the drop down box again to
                            complete the process</p>
                        </a>
                    </div>
                </div>
                <div class="col-lg-9">
                    <h5 style="color: #ababab" class="text-uppercase">
                        <?=$ipaddress?>
                    </h5>
                    <?php
                    if( isset( $processes ) ||  empty( $processes ) == false || $processes != null)
                    {
                        foreach( $processes as $key=>$value )
                        {
                            ?>
                            <div class="row">
                                <?php
                                Flight::render('syscrack/templates/template.process',array('processid' => $value->processid, 'processcclass' => $operations, 'actions' => true ) );
                                ?>
                            </div>
                            <?php
                        }
                    }
                    else
                    {
                        ?>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="panel panel-info">
                                    <div class="panel-body">
                                        You currently don't have any running processes on this machine
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                    <button style="width: 100%;" class="btn btn-primary" type="button" onclick="window.location.reload()">
                        <span class="glyphicon glyphicon-arrow-down" aria-hidden="true"></span> Refresh Page
                    </button>
                </div>
            </div>
            <?php

                Flight::render('syscrack/templates/template.footer', array('breadcrumb' => true ) );
            ?>
        </div>
    </body>
</html>