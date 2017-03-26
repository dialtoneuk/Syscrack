<?php

    $session = \Framework\Application\Container::getObject('session');

    if( $session->isLoggedIn() )
    {

        $session->updateLastAction();
    }

    $pagehelper = new \Framework\Syscrack\Game\Utility\PageHelper();
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
                <div class="col-lg-6">
                    <h1 class="page-header">
                        Hello <?=$pagehelper->getUsername()?>!
                    </h1>

                    <div class="panel panel-default">
                        <div class="panel-heading">Debug Information</div>
                        <div class="panel-body">
                            <p>
                                You currently have <strong><?=$pagehelper->getCash()?></strong>.
                            </p>

                            <div class="well">
                                <?=json_encode( $pagehelper->getComputerSoftware(), JSON_PRETTY_PRINT )?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <h1 class="page-header">
                        Other Information
                    </h1>


                </div>
            </div>

            <?php

                Flight::render('syscrack/templates/template.footer', array('breadcrumb' => true ) );
            ?>
        </div>
    </body>
</html>