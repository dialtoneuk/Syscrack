<?php

    use Framework\Application\Container;

    if( Container::hasObject('session') == false )
    {

        Flight::notFound();
    }
    else
    {

        $session = Container::getObject('session');

        if( $session->isLoggedIn() == false )
        {

            Flight::notFound();
        }
    }
?>

<html lang="en">

    <?php

        Flight::render('developer/templates/template.header', array( 'pagetitle' => 'Database Error'));
    ?>

    <body>
        <div class="container">
            <div class="row" style="margin-top: 5%;">
                <div class="col-lg-12">
                    <h5 style="color: #ababab" class="text-uppercase text-center">
                        Session Error
                    </h5>
                    <div class="panel panel-danger">
                        <div class="panel-heading">
                            Major error
                        </div>
                        <div class="panel-body text-center">
                            Your session appears to have become invalid, this could be due to a few reasons, but the best thing you can do is
                            <a onclick="clearCookies()">clear your cookies and attempt to login again.</a> If you are presistantly getting this
                            error, it might be best to notify a developer.
                            <h5 style="color: #ababab" class="text-uppercase">
                                Session Information
                            </h5>
                            <div class="well">
                                <?php

                                    if( empty( $_SESSION ) )
                                    {

                                        echo('Session is empty');
                                    }

                                    print_r( $_SESSION );
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>

    <footer>
        <script>
            function clearCookies()
            {

                document.cookie.split(";").forEach(function(c) { document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/"); });

                window.location.href = '<?=\Framework\Application\Settings::getSetting("controller_index_root")?>';
            }
        </script>
    </footer>
</html>