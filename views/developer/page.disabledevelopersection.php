<html lang="en">

    <?php

        Flight::render('developer/templates/template.header', array( 'pagetitle' => 'Disable Developers Section'));
    ?>
    <body>
        <div class="container">

            <?php

                Flight::render('developer/templates/template.navigation');
            ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="page-header">
                        <h1>Disable Developers Section</h1>
                    </div>

                    <p class="lead">
                        This tool will disable this developer section from being accessed, this is highly recommended
                        when launching your website publically.
                    </p>

                    <p>
                        It is important that you disable this area from access as users might be able to change your
                        framework settings as well as view your database password, so it is very important that you remember!
                    </p>
                </div>
                <div class="col-md-6">
                    <div class="page-header">
                        <h1>Danger zone!</h1>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <p class="text-center text-uppercase">
                                <strong>
                                    Use the button below only if you have 100% finished your web-application and you are ready
                                    to launch
                                </strong>
                            </p>
                            <button class="btn btn-danger btn-block btn-lg" data-toggle="modal" data-target="#disablemodal">
                                Disable Developer Section
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Modal -->
                <div class="modal fade" id="disablemodal" tabindex="-1" role="dialog" aria-labelledby="disable-modal-label">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form method="post" >
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="myModalLabel">You sure?</h4>
                                </div>
                                <div class="modal-body">
                                    <p>
                                        Are you sure you want to disable the developer section?
                                    </p>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" name="action" value="delete" class="btn btn-primary">Yes</button>
                                    <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <?php

                Flight::render('developer/templates/template.footer', array( 'breadcrumb' => true ));
            ?>
        </div>
    </body>
</html>

<?php
    if( $_POST )
        if( \Framework\Application\Utilities\PostHelper::checkForRequirements(['action']) )
        {

            \Framework\Application\Settings::updateSetting('developer_disabled', true );

            \Framework\Application\Settings::writeSettings();

            Flight::redirect('/');
        }
?>