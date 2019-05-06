<?php

    use Framework\Application\Container;
    use Framework\Application\Settings;
use Framework\Application\Render;

    if( isset( $id ) == false )
    {

        Flight::redirect( $settings['controller_index_root'] . $settings['developer_page'] );
    }

    $error = Container::getObject('application')->getErrorHandler()->getErrorLog()[ $id ];
?>
<html lang="en">

    <?php

        Render::view('developer/templates/template.header', array( 'pagetitle' => 'Developer / Errors / '. $id ));
    ?>
    <body>
        <div class="container">


            <?php

                Render::view('developer/templates/template.navigation');
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
                    <a href="/developer/errors/">
                        Return To Previous Page
                    </a>

                    <?php

                        if( isset( Container::getObject('application')->getErrorHandler()->getErrorLog()[ $id + 1 ]) )
                        {
                            ?>
                            <p style="float: right; padding-left: 0.25%;">
                                /
                                <a href="/developer/errors/<?=$id+1?>">
                                    Next Error
                                </a>
                            </p>
                            <?php
                        }

                        if( $id - 1 != -1 )
                        {
                            ?>
                                <p style="float: right;">
                                    <a href="/developer/errors/<?=$id-1?>">
                                        Previous Error
                                    </a>
                                </p>
                            <?php
                        }
                    ?>
                </div>
            </div>

            <?php

                Render::view('developer/templates/template.footer');
            ?>
        </div>
    </body>
</html>
