<?php

    use Framework\Application\Settings;
    use Framework\Syscrack\Game\Computers;
    use Framework\Syscrack\Game\Internet;
    use Framework\Syscrack\Game\Utilities\PageHelper;

    if( isset( $computer ) == false )
    {

        $computer = new Computers();
    }

    if( isset( $pagehelper ) == false )
    {

        $pagehelper = new PageHelper();
    }

    if( isset( $internet ) == false )
    {

        $internet = new Internet();
    }
?>
<html>
    <?php

        Flight::render('syscrack/templates/template.header', array('pagetitle' => 'Syscrack | Game') );
    ?>
    <body>
        <div class="container">
            <?php

                Flight::render('syscrack/templates/template.navigation');
            ?>
            <div class="row">
                <div class="col-lg-12">
                    <?php

                        if( isset( $_GET['error'] ) )
                            Flight::render('syscrack/templates/template.alert', array( 'message' => $_GET['error'] ) );
                        elseif( isset( $_GET['success'] ) )
                            Flight::render('syscrack/templates/template.alert', array( 'message' => Settings::getSetting('alert_success_message'), 'alert_type' => 'alert-success' ) );
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <h5 style="color: #ababab" class="text-uppercase">
                        Research Center
                    </h5>
                </div>
            </div>
            <div class="row">

            </div>
        </div>
    </body>
</html>
