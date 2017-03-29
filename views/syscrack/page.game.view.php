<?php

$session = \Framework\Application\Container::getObject('session');

if( $session->isLoggedIn() )
{

    $session->updateLastAction();
}

$pagehelper = new \Framework\Syscrack\Game\Utilities\PageHelper();

if( isset( $softwares ) == false )
{

    $softwares = new \Framework\Syscrack\Game\Softwares();
}

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
                        Viewing File <?=$softwares->getDatabaseSoftware( $softwareid )->softwarename?><?=$softwares->getSoftwareClassFromID( $softwareid )->configuration()['extension']?>
                    </h1>

                    <?php
                        if( isset( $data ) == false )
                        {

                            ?>
                                <p>
                                    No Contents
                                </p>
                            <?php
                        }
                        else
                        {

                            if( isset( $data['text'] ) )
                            {

                                ?>
                                    <div class="well">
                                        <?=$data['text']?>
                                    </div>
                                <?php
                            }
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