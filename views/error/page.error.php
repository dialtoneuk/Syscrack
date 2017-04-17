<?php

    use Framework\Application\Container;
    use Framework\Application\Session;
    use Framework\Syscrack\User;

    if( \Framework\Application\Settings::getSetting('error_logging') == false || \Framework\Application\Settings::getSetting('error_display_page') == false )
    {

        Flight::notFound();

        exit;
    }

    if( session_status() !== PHP_SESSION_ACTIVE )
    {

        session_start();
    }

    if( Container::hasObject('session') == false )
    {

        Container::setObject('session', new Session() );
    }

    $error_handler = \Framework\Application\Container::getObject('application')->getErrorHandler();

    $user = new User();

    if( $error_handler->hasErrors() == false || $error_handler->hasLogFile() == false )
    {

        Flight::redirect('/');
    }

    $last_error = $error_handler->getLastError();

    if( $last_error['ip'] !== \Framework\Application\Utilities\IPAddress::getAddress() )
    {

        if(  Container::getObject('session')->isLoggedIn() !== true || $user->isAdmin( Container::getObject('session')->getSessionUser() ) == false )
        {

            Flight::redirect('/');
        }
    }

    unset( $user );
?>
<html lang="en">

    <?php

        Flight::render('developer/templates/template.header', array( 'pagetitle' => 'Error'));
    ?>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h1>
                            Exception by <?= $last_error['ip'] ?> on <?= date('Y-m-d g:i a', $last_error['timestamp'] ) ?>
                            <span style="float:right" class="label label-<?php if( $last_error['type'] == 'error'){ echo 'danger';}else{echo 'default';}?>">
                                <?= $last_error['type'] ?>
                            </span>
                        </h1>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <p style='color: #adadad' class="small text-uppercase">
                                File
                            </p>
                            <div class="well">
                                <?= $last_error['details']['file'] ?> at line <?= $last_error['details']['line'] ?>
                            </div>
                            <p style='color: #adadad' class="small text-uppercase">
                                Trace
                            </p>
                            <div class="well">
                                <pre>
<?= $last_error['details']['trace']?>
                                </pre>
                            </div>
                            <p style='color: #adadad' class="small text-uppercase">
                                Error Message
                            </p>
                            <div class="well">
                                <?= $last_error['message'] ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <a href="/">
                        Return To Index
                    </a>
                    <?php
                        if( $_GET )
                        {

                            if( $_GET['redirect'] )
                            {

                                if( strlen( $_GET['redirect'] ) < \Framework\Application\Settings::getSetting('controller_url_length') )
                                {

                                    ?>
                                    /
                                    <a href="<?=$_GET['redirect']?>">
                                        Go Back
                                    </a>
                                    <?php
                                }
                            }
                        }
                    ?>
                </div>
            </div>
        </div>

        <?php

            Flight::render('developer/templates/template.footer');
        ?>
    </body>
</html>

