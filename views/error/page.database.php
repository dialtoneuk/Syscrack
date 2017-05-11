<html lang="en">

    <?php

        Flight::render('developer/templates/template.header', array( 'pagetitle' => 'Database Error'));
    ?>

    <body>
        <div class="container">
            <div class="row" style="margin-top: 5%;">
                <div class="col-lg-12">
                    <h5 style="color: #ababab" class="text-uppercase text-center">
                        Database error
                    </h5>
                    <div class="panel panel-danger">
                        <div class="panel-heading">
                            Major error
                        </div>
                        <div class="panel-body text-center">
                            Your database connection file was either not found, or it was found, and the database failed to connect. If you have not made
                            a database connection file yet, do so by visiting <a href="/developer/connection/creator/">the connection creator.</a> You
                            may check the status of your database connection by visiting the <A href="/developer/connection/">connection page.</A>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>