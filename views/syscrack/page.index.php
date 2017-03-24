<?php

    $session = \Framework\Application\Container::getObject('session');

    if( $session->isLoggedIn() )
    {

        $session->updateLastAction();
    }
?>
<html>

    <?php

        Flight::render('syscrack/templates/template.header', array('pagetitle' => 'Syscrack') );
    ?>
    <body>
        <div class="container">

            <?php

                Flight::render('syscrack/templates/template.navigation');
            ?>
            <div class="row">
                <div class="col-lg-6">
                    <div class="page-header">
                        <h1>The virtual Internet</h1>
                    </div>

                    <p>
                        Hack around and play inside a virtual internet.
                    </p>
                </div>

                <div class="col-lg-6">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <span class="badge">0</span>
                            Hacked Computers
                        </li>
                        <li class="list-group-item">
                            <span class="badge">0</span>
                            Viruses Installed
                        </li>
                        <li class="list-group-item">
                            <span class="badge">0 BTC</span>
                            Bitcoins Mined
                        </li>
                        <li class="list-group-item">
                            <span class="badge">$0</span>
                            Profited
                        </li>
                    </ul>
                </div>
            </div>

            <?php

                Flight::render('syscrack/templates/template.footer', array('breadcrumb' => true ) );
            ?>
        </div>
    </body>
</html>