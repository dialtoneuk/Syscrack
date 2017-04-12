<?php

    $session = \Framework\Application\Container::getObject('session');

    if( $session->isLoggedIn() )
    {

        $session->updateLastAction();
    }

    $pagehelper = new \Framework\Syscrack\Game\Utilities\PageHelper();

    $computer = new \Framework\Syscrack\Game\Computer();
?>
<html>

    <?php

        Flight::render('syscrack/templates/template.header', array('pagetitle' => 'Syscrack | Game', 'scripts' => '<script src="/assets/js/webglearth.js"></script>') );
    ?>

    <style>
        #stats div {

            -webkit-animation: fadein 3.5s; /* Safari, Chrome and Opera > 12.1 */
            -moz-animation: fadein 3.5s; /* Firefox < 16 */
            -ms-animation: fadein 3.5s; /* Internet Explorer */
            -o-animation: fadein 3.5s; /* Opera < 12.1 */
            animation: fadein 3.5s;
        }

        @keyframes fadein {
            from { opacity: 0; }
            to   { opacity: 1; }
        }

        /* Firefox < 16 */
        @-moz-keyframes fadein {
            from { opacity: 0; }
            to   { opacity: 1; }
        }

        /* Safari, Chrome and Opera > 12.1 */
        @-webkit-keyframes fadein {
            from { opacity: 0; }
            to   { opacity: 1; }
        }

        /* Internet Explorer */
        @-ms-keyframes fadein {
            from { opacity: 0; }
            to   { opacity: 1; }
        }

        /* Opera < 12.1 */
        @-o-keyframes fadein {
            from { opacity: 0; }
            to   { opacity: 1; }
        }

        .jumbotron
        {
            -webkit-touch-callout: none; /* iOS Safari */
            -webkit-user-select: none; /* Safari */
            -moz-user-select: none; /* Firefox */
            -ms-user-select: none; /* Internet Explorer/Edge */
            user-select: none; /* N */

            -webkit-box-shadow: inset 0 0 229px -66px rgba(0, 0, 0, 1);
            -moz-box-shadow: inset 0 0 229px -66px rgba(0, 0, 0, 1);
            box-shadow: inset 0 0 229px -66px rgba(0, 0, 0, 1);
        }
    </style>
    <body>
        <div class="container">

            <?php

                Flight::render('syscrack/templates/template.navigation');
            ?>

            <div class="row" id="stats">
                <div class="col-lg-12">
                    <div class="jumbotron" style="padding: 0; background: black; box-shadow: #0f0f0f ">
                        <div style="position: absolute; width: 100%; height: 100%; padding: 2.5%; color:white; z-index: 2;">
                            <h1>
                                SC:\\ <?=\Framework\Application\Settings::getSetting('syscrack_game_name')?>
                            </h1>
                            <p>
                                $0 Earned
                            </p>
                            <p>
                                0 BTC mined
                            </p>
                            <p>
                                0 Virus Installs
                            </p>
                            <p>
                                0 Hacks
                            </p>
                        </div>
                        <div id="earth_div" style="width: 100%; height: 40%; position: absolute; z-index: 1;"></div>
                    </div>
                </div>
                <script>

                    <?php

                        try
                        {

                            if( $_SERVER['REMOTE_ADDR'] == "::1" || $_SERVER['REMOTE_ADDR'] == 'localhost' )
                            {

                                $location = json_decode( file_get_contents('http://freegeoip.net/json/') );
                            }
                            else
                            {

                                $location = json_decode( file_get_contents('http://freegeoip.net/json/' . $_SERVER['REMOTE_ADDR'] ) );
                            }
                        }
                        catch( Exception $error )
                        {

                            $location = new stdClass();

                            $location->longitude = 0;

                            $location->latitude = 0;
                        }
                    ?>

                    var options = { zoom: 10, position: [<?=$location->latitude?>,<?=$location->longitude?>] };
                    var earth = new WE.map('earth_div', options);
                    // Start a simple rotation animation
                    var before = null;
                    var zoom = 10;

                    var bingKey = 'AsLurrtJotbxkJmnsefUYbatUuBkeBTzTL930TvcOekeG8SaQPY9Z5LDKtiuzAOu';

                    bingA = earth.initMap(WebGLEarth.Maps.BING, ['Aerial', bingKey]);
                    bingAWL = earth.initMap(WebGLEarth.Maps.BING, ['AerialWithLabels', bingKey]);
                    bingR = earth.initMap(WebGLEarth.Maps.BING, ['Road', bingKey]);

                    earth.setBaseMap(bingAWL);

                    requestAnimationFrame(function animate(now) {
                        var c = earth.getPosition();
                        var elapsed = before? now - before: 0;
                        zoom = zoom - 0.001;

                        if( zoom < 3 )
                        {

                            zoom = zoom + 0.0005
                        }

                        if( zoom < 2 )
                        {

                            zoom = zoom + 0.00025;
                        }

                        if( zoom < 1 )
                        {

                            zoom = zoom + 0.000245;
                        }

                        before = now;
                        earth.setCenter([c[0], c[1] + 0.1*(elapsed/150)]);
                        earth.setZoom( zoom );
                        requestAnimationFrame(animate);
                    });
                </script>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <h2 class="page-header">
                        VPC's
                    </h2>
                    <div class="panel panel-default">
                        <div class="panel-body">

                            <?php

                                $computers = $computer->getUserComputers( $session->getSessionUser() );

                                foreach( $computers as $value )
                                {

                                    if( $computer->getCurrentUserComputer() == $value->computerid )
                                    {

                                        ?>

                                            <div class="panel panel-primary">
                                                <div class="panel-heading">
                                                    <?=$value->ipaddress?>
                                                    <small>
                                                        <span class="glyphicon glyphicon-modal-window"></span> <?=$value->computerid?>
                                                    </small>
                                                </div>
                                                <div class="panel-body">
                                                    <button style="width: 100%;" class="btn btn-primary" onclick="window.location.href = '/game/computer/'">
                                                        <span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span> View
                                                    </button>
                                                </div>
                                            </div>
                                        <?php
                                    }
                                    else
                                    {

                                        ?>

                                            <form method="post">
                                                <div class="panel panel-default">
                                                    <div class="panel-heading">
                                                        <?=$value->ipaddress?>
                                                        <small>
                                                            <span class="glyphicon glyphicon-modal-window"></span> <?=$value->computerid?>
                                                        </small>
                                                    </div>
                                                    <div class="panel-body">
                                                        <button style="width: 100%;" class="btn btn-success" name="action" value="switch" type="submit">
                                                            <span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Switch
                                                            <input type="hidden" name="computerid" value="<?=$value->computerid?>">
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        <?php
                                    }
                                }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <h2 class="page-header">
                        News
                    </h2>
                    <h2 class="page-header">
                        Internets Most Wanted
                    </h2>
                </div>
            </div>

            <?php

                Flight::render('syscrack/templates/template.footer', array('breadcrumb' => true ) );
            ?>
        </div>
    </body>
</html>