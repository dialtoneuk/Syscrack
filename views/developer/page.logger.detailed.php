<?php

    if( isset( $id ) == false )
    {

        Flight::redirect('/developer/logger/');
    }

    $error_handler = \Framework\Application\Container::getObject('application')->getErrorHandler();

    $error_log = $error_handler->getErrorLog();

    if( isset( $error_log[ $id ] ) == false )
    {

        Flight::redirect('/developer/logger/?error=Unable to find that error');
    }

    $error = $error_log[ $id ];
?>
<html lang="en">

    <?php

        Flight::render('developer/templates/template.header', array( 'pagetitle' => 'Custom Tools'));
    ?>
    <body>
        <div class="container">


            <?php

                Flight::render('developer/templates/template.navigation');
            ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                By
                                <?= $error['ip'] ?> on <?= date('Y-m-d g:i a', $error['timestamp'] ) ?>
                                <span style="float:right" class="label label-<?php if( $error['type'] == 'error'){ echo 'danger';}else{echo 'default';}?>">
                                    #<?=$id?>
                                </span>
                                @
                                <a class='small' href="<?=$error['details']['url']?>">
                                    <?=$error['details']['url']?>
                                </a>
                            </h3>
                        </div>
                        <div class="panel-body">
                            <p style='color: #adadad' class="small text-uppercase">
                                File
                            </p>
                            <div class="well">
                                <?= $error['details']['file'] ?> at line <?= $error['details']['line'] ?>
                            </div>
                            <p style='color: #adadad' class="small text-uppercase">
                                Trace
                            </p>
                            <div class="well">
                                <pre>
<?= $error['details']['trace']?>
                                </pre>
                            </div>
                            <p style='color: #adadad' class="small text-uppercase">
                                Error Message
                            </p>
                            <div class="well">
                                <?= $error['message'] ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12" style="height: 4%;">
                    <a href="/developer/logger/">
                        Return To Previous Page
                    </a>

                    <?php
                        if( $id - 1 != -1 )
                        {
                            ?>
                                /
                                <a href="/developer/logger/<?=$id-1?>">
                                    Previous Error
                                </a>
                            <?php
                        }

                        if( isset( $error_log[ $id + 1 ]) )
                        {
                            ?>
                                /
                                <a href="/developer/logger/<?=$id+1?>">
                                    Next Error
                                </a>
                            <?php
                        }
                    ?>
                </div>
            </div>

            <?php

                Flight::render('developer/templates/template.footer');
            ?>
        </div>
    </body>
</html>
