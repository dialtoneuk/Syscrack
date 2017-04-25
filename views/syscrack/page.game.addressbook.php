<?php

    $session = \Framework\Application\Container::getObject('session');

    if( $session->isLoggedIn() )
    {

        $session->updateLastAction();
    }

    $pagehelper = new \Framework\Syscrack\Game\Utilities\PageHelper();

    $computer = new \Framework\Syscrack\Game\Computer();

    $addressbook = new \Framework\Syscrack\Game\AddressDatabase( $session->getSessionUser(), true );

    $internet = new \Framework\Syscrack\Game\Internet();

    $virus = new \Framework\Syscrack\Game\Viruses();
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
                    <h1 class="page-header">
                        Address Book
                    </h1>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <span class="glyphicon glyphicon-paperclip"></span> Address Book
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <span class="glyphicon glyphicon-briefcase"></span> Account Book
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" aria-label="...">
                        <div class="input-group-btn">
                            <button class="btn btn-default">
                                Find
                            </button>
                        </div>
                    </div>
                    <div style="margin-top: 2.5%">

                        <?php

                            $addresses = array_reverse( $addressbook->getDatabase( $session->getSessionUser() ) );

                            $removed = [];

                            if( empty( $addresses ) )
                            {

                                ?>
                                    <div class="panel panel-danger">
                                        <div class="panel-heading">
                                            Addressbook Empty
                                        </div>
                                        <div class="panel-body">
                                            Your addressbook appears to be empty, go hack somebody!
                                        </div>
                                    </div>
                                <?php
                            }
                            else
                            {

                                foreach( $addresses as $key=>$value )
                                {

                                    if( $internet->ipExists( $value['ipaddress'] ) == false )
                                    {

                                        $removed[] = array(
                                            'ipaddress' => $value['ipaddress'],
                                            'reason'    => 'Not Responding'
                                        );

                                        $addressbook->removeComputer( $value['computerid'] );

                                        $addressbook->saveDatabase();
                                    }
                                }

                                if( empty( $removed ) == false )
                                {

                                    ?>
                                    <div class="panel panel-danger">
                                        <div class="panel-heading">
                                            Attention!
                                        </div>
                                        <div class="panel-body">

                                            <?php

                                                foreach( $removed as $value )
                                                {

                                                    ?>

                                                    <p>
                                                        IP [<?=$value['ipaddress']?>] Removed < <?=$value['reason']?> >
                                                    </p>
                                                    <?php
                                                }
                                            ?>
                                        </div>
                                    </div>
                                    <?php
                                }

                                $addresses = $addressbook->getDatabase( $session->getSessionUser() );

                                array_reverse( $addresses );

                                foreach( $addresses as $key=>$value )
                                {

                                    Flight::render('syscrack/templates/template.address', array('key' => $key, 'value' => $value, 'computer' => $computer ) );
                                }
                            }
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