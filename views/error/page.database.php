<html lang="en">

    <?php

        Flight::render('developer/templates/template.header', array( 'pagetitle' => 'Connection Tester'));
    ?>

    <body>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header text-center">
                        Critical Error
                    </h1>
                    <div class="panel-heading">
                        Major error
                    </div>
                    <div class="panel panel-danger">
                        <div class="panel-body text-center">
                            Your database has failed to connect, this usually means there there is an issue with your settings, please refer to
                            the installation guide if you are having trouble.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>