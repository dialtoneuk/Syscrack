<?php

    $session = \Framework\Application\Container::getObject('session');

    if( $session->isLoggedIn() )
    {

        $session->updateLastAction();
    }

    $pagehelper = new \Framework\Syscrack\Game\Utilities\PageHelper();

    if( isset( $processclass ) == false )
    {

        $processclass = new \Framework\Syscrack\Game\Operations();
    }
?>
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

                <?php
                    if( isset( $processid ) )
                    {

                        Flight::render('syscrack/templates/template.process',array('processid' => $processid, 'processcclass' => $processclass ) );
                    }
                ?>

                <div class="col-lg-12">
                    <button style="width: 100%; margin-top: 2.5%;" class="btn btn-primary" type="button" onclick="window.location.reload()">
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