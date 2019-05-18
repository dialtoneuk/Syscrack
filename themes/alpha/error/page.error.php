<?php

    use Framework\Application\Render;
    ?>
<html lang="en">
    <?php Render::view('developer/templates/template.header', array( 'pagetitle' => 'Error')); ?>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h5>
                        Exception by <?= @$error['ip'] ?> on <?= @date('Y-m-d g:i a', $error['timestamp'] ) ?>
                        <span style="float:right" class="label label-<?php if( $error['type'] == 'frameworkerror'){ echo 'danger';}elseif( $error['type'] == 'rendererror'){echo 'warning';}else{ echo 'default'; }?>">
                                <?= @$error['type'] ?>
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
                                <?= @$error['details']['file'] ?> at line <?= @$error['details']['line'] ?>
                            </div>
                            <p style='color: #adadad' class="small text-uppercase">
                                Error Message
                            </p>
                            <div class="well">
                                <?= @$error['message'] ?>
                            </div>
                            <p style='color: #adadad' class="small text-uppercase">
                                Trace
                            </p>
                            <div class="well">
                                <pre>
<?=htmlspecialchars( @$error['details']['trace'], ENT_QUOTES, 'UTF-8' )?>
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

                                if( strlen( $_GET['redirect'] ) < $settings['controller_url_length'] )
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

