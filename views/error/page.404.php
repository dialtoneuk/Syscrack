<html lang="en">

    <?php

        Flight::render('developer/templates/template.header', array( 'pagetitle' => 'Not Found'));
    ?>

    <body>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header text-center">
                        SC:\\404
                    </h1>
                    <div class="panel panel-default">
                        <div class="panel-body text-center">
                            You've hit rock bottom my friend, scoot on <a href="/">back to the index.</a>
                        </div>
                    </div>
                </div>
            </div>

            <?php

                Flight::render('syscrack/templates/template.footer', array('breadcrumb' => true ) );
            ?>
        </div>
    </body>
</html>