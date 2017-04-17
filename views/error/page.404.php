<html lang="en">

    <?php

        Flight::render('developer/templates/template.header', array( 'pagetitle' => 'Connection Tester'));
    ?>

    <body>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header text-center">
                        404
                    </h1>
                    <div class="panel panel-danger">
                        <div class="panel-body text-center">
                            You've hit rock bottom my friend, scoot on <a href="/">back to the index.</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>

    <?php

        Flight::render('syscrack/templates/template.footer', array('breadcrumb' => true ) );
    ?>
</html>