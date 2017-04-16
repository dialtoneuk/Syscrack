<?php

    use Framework\Application\Settings;
    use Framework\Application\Container;
    use Framework\Syscrack\Game\Internet;
    use Framework\Syscrack\Game\Utilities\PageHelper;

    $internet = new Internet();

    $pagehelper = new PageHelper();

    $session = Container::getObject('session');

    if( $session->isLoggedIn() )
    {

        $session->updateLastAction();
    }

    if( isset( $ipaddress ) == false )
        $ipaddress = $internet->getComputerAddress( Settings::getSetting('syscrack_whois_computer') );
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
                <div class="col-lg-12">

                    <?php
                        if( $internet->hasCurrentConnection() )
                        {

                            if( $ipaddress !== $internet->getCurrentConnectedAddress() )
                            {

                                ?>
                                    <div class="panel panel-primary">
                                        <div class="panel-heading">
                                            Notice
                                        </div>

                                        <div class="panel-body">
                                            You are currently connected to <?= $internet->getCurrentConnectedAddress() ?>, <a href="/game/internet/<?= $internet->getCurrentConnectedAddress()?>/logout">Logout?</a>
                                        </div>
                                    </div>
                                <?php
                            }
                        }
                    ?>
                    <div class="row">

                        <?php

                            if( $pagehelper->isCurrentlyConnected( $ipaddress ) == false )
                            {
                                Flight::render('syscrack/templates/template.browser', array( 'ipaddress' => $ipaddress, 'internet' => $internet, 'pagehelper' => $pagehelper ) );
                            }
                            else
                            {

                                Flight::render('syscrack/templates/template.computer', array( 'ipaddress' => $ipaddress, 'internet' => $internet, 'pagehelper' => $pagehelper, 'hideoptions' => false ) );
                            }

                            Flight::render('syscrack/templates/template.tools', array( 'ipaddress' => $ipaddress, 'internet' => $internet, 'pagehelper' => $pagehelper ) );
                        ?>

                    </div>
                </div>
            </div>

            <?php

                Flight::render('syscrack/templates/template.footer', array('breadcrumb' => true ) );
            ?>
        </div>
    </body>
</html>