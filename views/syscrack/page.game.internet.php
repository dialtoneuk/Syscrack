<?php

    $session = \Framework\Application\Container::getObject('session');

    if( $session->isLoggedIn() )
    {

        $session->updateLastAction();
    }

    $internet = new \Framework\Syscrack\Game\Internet();

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
                <div class="col-lg-12">

                    <?php
                        if( $internet->hasCurrentConnection() )
                        {

                            ?>
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        Notice
                                    </div>

                                    <div class="panel-body">
                                        You are currently connected to <?= $internet->getCurrentConnectedAddress() ?>
                                    </div>
                                </div>
                            <?php
                        }
                    ?>
                    <div class="row">
                        <div class="col-md-8">
                            <form method="post" action="/game/internet/">

                                <?php

                                    if( isset( $_GET['error'] ) )
                                        Flight::render('syscrack/templates/template.alert', array( 'message' => $_GET['error'] ) );
                                    elseif( isset( $_GET['success'] ) )
                                        Flight::render('syscrack/templates/template.alert', array( 'message' => 'Success', 'alert_type' => 'alert-success' ) );
                                ?>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="ipaddress" name="ipaddress" placeholder="<?php if( isset( $ipaddress ) ){ echo $ipaddress; } else { echo "1.2.3.4"; }?>">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" onclick="window.location.href = '/game/internet/' . $('#ipaddress').value()">Connect</button>
                                    </span>
                                </div><!-- /input-group -->
                                <div class="panel panel-default" style="margin-top: 2.5%">
                                    <div class="panel-body">

                                        <?php
                                            if( isset( $ipaddress ) == false )
                                            {
                                                //TODO: Maybe disable this?

                                                $npc = new \Framework\Syscrack\Game\NPC();

                                                if( $npc->isNPC( \Framework\Application\Settings::getSetting('syscrack_whois_computer') ) )
                                                {

                                                    $npc->renderNPCPage( \Framework\Application\Settings::getSetting('syscrack_whois_computer') );
                                                }
                                            }
                                            else
                                            {

                                                if( $internet->ipExists( $ipaddress ) == false )
                                                {

                                                    Flight::redirect('/game/internet?error=404 Not Found');

                                                    exit;
                                                }
                                                else
                                                {

                                                    $computerid = $internet->getComputer( $ipaddress )->computerid;

                                                    $computer = new \Framework\Syscrack\Game\Computer();

                                                    if( $computer->isVPC( $computerid ) )
                                                    {

                                                        //TODO: Custom player HTML area

                                                        ?>

                                                            <h1 class="page-header">
                                                                Player Computer
                                                            </h1>

                                                            <p>
                                                                My IP address is <strong><?=$ipaddress?></strong>, I don't have
                                                                any web-server running, so this is the default message.
                                                            </p>
                                                        <?php
                                                    }
                                                    else
                                                    {

                                                        if( $computer->isNPC( $computerid ) )
                                                        {

                                                            $npc = new \Framework\Syscrack\Game\NPC();

                                                            if( $npc->hasPage( $computerid ) )
                                                            {

                                                                $npc->renderNPCPage( $computerid );
                                                            }
                                                            else
                                                            {

                                                                ?>
                                                                <p>
                                                                    NPC Computer
                                                                </p>
                                                                <?php
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        ?>
                                    </div>
                                    <div class="panel-footer">
                                        <?php if( isset( $ipaddress )){echo strtoupper( $pagehelper->getComputerType( $internet->getComputer( $ipaddress )->computerid ) ); }?>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-4">
                            <form action="/game/internet/<?php if( isset( $ipaddress ) ){ echo $ipaddress; }else{ echo $internet->getComputerAddress( \Framework\Application\Settings::getSetting('syscrack_whois_computer'));}?>/hack">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <button style="width: 100%;" class="btn btn-primary" type="submit"
                                            <?php if( $pagehelper->getInstalledCracker() == null ){ echo 'disabled'; }?> >
                                            <span class="glyphicon glyphicon-lock" aria-hidden="true"></span> Hack <span class="badge"><?=$pagehelper->getSoftwareLevel( $pagehelper->getInstalledCracker()['softwareid'] )?></span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <form action="/game/internet/<?php if( isset( $ipaddress ) ){ echo $ipaddress; }else{ echo $internet->getComputerAddress( \Framework\Application\Settings::getSetting('syscrack_whois_computer'));}?>/exploit">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <button style="width: 100%;" class="btn btn-primary" type="submit"
                                            <?php if( $pagehelper->getInstalledCracker() == null ){ echo 'disabled'; }?> >
                                            <span class="glyphicon glyphicon-lock" aria-hidden="true"></span> Exploit <span class="badge"><?=$pagehelper->getSoftwareLevel( $pagehelper->getInstalledCracker()['softwareid'] )?></span>
                                        </button>
                                    </div>
                                </div>
                            </form>
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