<?php
    use Framework\Application\Render;
?>
<html lang="en">

    <?php

        Render::view('developer/templates/template.header', array( 'pagetitle' => 'Not Found'));
    ?>

    <body>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <img class="img-responsive" src="/assets/img/logos/vibrant_green.png">
                </div>
            </div>
            <div class="row" style="margin-top: 5%;">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-body text-center">
                            You've hit rock bottom my friend, scoot on <a href="/">back to the index.</a>
                        </div>
                    </div>
                </div>
            </div>

            <?php

                Render::view('syscrack/templates/template.footer', array('breadcrumb' => true ) );
            ?>
        </div>
    </body>
</html>