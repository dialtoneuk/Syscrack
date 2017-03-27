<?php

    $session = \Framework\Application\Container::getObject('session');

    if( $session->isLoggedIn() )
    {

        $session->updateLastAction();
    }

    $pagehelper = new \Framework\Syscrack\Game\Utilities\PageHelper();
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

                    <?php

                        Flight::render( 'syscrack/templates/template.debug', array( 'pagehelper' => $pagehelper ) );
                    ?>
                </div>
                <div class="col-lg-6">
                    <h1 class="page-header">
                        Other Information
                    </h1>

                    <p>
                        The sky is blue.
                    </p>
                </div>
            </div>

            <?php

                Flight::render('syscrack/templates/template.footer', array('breadcrumb' => true ) );
            ?>
        </div>
    </body>
</html>