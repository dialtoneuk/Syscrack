<?php

    use Framework\Application\Container;
    use Framework\Application\Settings;
    use Framework\Application\Utilities\IPAddress;
    use Framework\Syscrack\User;
    use Framework\Application\Render;

    try
    {

        if($settings['error_logging'] == false ||$settings['error_display_page'] == false )
        {

            Flight::notFound();

            exit;
        }

        $error_handler = Container::getObject('application')->getErrorHandler();

        if( $error_handler->hasErrors() == false || $error_handler->hasLogFile() == false )
        {

            Flight::redirect('/');
        }

        $last_error = $error_handler->getLastError();

        if( $last_error['ip'] != IPAddress::getAddress() )
        {

            @$user = new User();

            if(  Container::getObject('session')->isLoggedIn() !== true || $user->isAdmin( Container::getObject('session')->getSessionUser() ) == false )
            {

                Flight::redirect('/');
            }

            unset( $user );
        }
    }
    catch( Exception $error )
    {

        ob_clean();

        ?>
        <html>
            <head>
                <meta charset="utf-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1">

                <title>Critical Error</title>

                <!-- Stylesheets -->
                <link href="/assets/css/bootstrap.dark.css" rel="stylesheet">
                <link href="/assets/css/bootstrap-combobox.css" rel="stylesheet">

                <!--[if lt IE 9]>
                <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
                <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
                <![endif]-->
            </head>
            <body>
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <h5 style="color: #ababab" class="text-uppercase text-center">
                            Critical Error
                        </h5>
                        <div class="panel panel-danger">
                            <div class="panel-heading">
                                <?=$error->getMessage()?> @ <?=$error->getFile()?> line <?=$error->getLine()?>
                            </div>
                            <div class="panel-body text-center">
                                <p>
                                    An error occurred outside of the framework, this is usually due to a permission error, a rewrite error, or something completely different, check out the error stack below.
                                </p>

                                <div class="well">
    <pre>
        <?=htmlspecialchars( $error->getTraceAsString(), ENT_QUOTES, 'utf-8' )?>
    </pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </body>
            </html>
        <?php

        exit;
    }
?>
<html lang="en">

    <?php

        Render::view('developer/templates/template.header', array( 'pagetitle' => 'Error'));
    ?>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h5>
                        Exception by <?= $last_error['ip'] ?> on <?= date('Y-m-d g:i a', $last_error['timestamp'] ) ?>
                        <span style="float:right" class="label label-<?php if( $last_error['type'] == 'frameworkerror'){ echo 'danger';}elseif( $last_error['type'] == 'rendererror'){echo 'warning';}else{ echo 'default'; }?>">
                                <?= $last_error['type'] ?>
                            </span>
                    </h5>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <p style='color: #adadad' class="small text-uppercase">
                                File
                            </p>
                            <div class="well">
                                <?= $last_error['details']['file'] ?> at line <?= $last_error['details']['line'] ?>
                            </div>
                            <p style='color: #adadad' class="small text-uppercase">
                                Error Message
                            </p>
                            <div class="well">
                                <?= $last_error['message'] ?>
                            </div>
                            <p style='color: #adadad' class="small text-uppercase">
                                Trace
                            </p>
                            <div class="well">
                                <pre>
<?=htmlspecialchars( $last_error['details']['trace'], ENT_QUOTES, 'UTF-8' )?>
                                </pre>
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

                                $_GET['redirect'] = htmlspecialchars( $_GET['redirect'], ENT_QUOTES, 'UTF-8' );

                                if( strlen( $_GET['redirect'] ) <$settings['controller_url_length'] )
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

            Render::view('developer/templates/template.footer');
        ?>
    </body>
</html>

