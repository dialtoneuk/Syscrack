<html lang="en">

    <?php

        Flight::render('developer/templates/template.header', array( 'pagetitle' => 'Developer / Disable'));
    ?>
    <body>
        <div class="container">

            <?php

                Flight::render('developer/templates/template.navigation');
            ?>
            <div class="row">
                <div class="col-md-6">
                    <h5 style="color: #ababab" class="text-uppercase">
                        Disable
                    </h5>
                    <p class="lead">
                        Disable your developer section for when you are launching live to disallow settings from being edited and other
                        framework operations from being executed.
                    </p>

                    <p>
                        It is important that you disable this area from access as users might be able to change your
                        framework settings as well as view your database password, so it is very important that you remember!
                    </p>
                </div>
                <div class="col-md-6">
                    <h5 style="color: #ababab" class="text-uppercase">
                        Danger Zone
                    </h5>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <p class="text-center">
                                "Success consists of going from failure to failure without loss of enthusiasm." <strong>Winston Churchill</strong>
                            </p>
                            <button class="btn btn-danger btn-block btn-sm" data-toggle="modal" data-target="#disablemodal">
                                Make It Live
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="disablemodal" tabindex="-1" role="dialog" aria-labelledby="disable-modal-label">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form method="post" >
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="myModalLabel">You ready to do this?</h4>
                                </div>
                                <div class="modal-body">
                                    <p>
                                        Are you sure you want to disable the developer section?
                                    </p>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" name="action" value="disable" class="btn btn-primary">Yes</button>
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