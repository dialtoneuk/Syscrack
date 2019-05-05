<?php

use Framework\Application\Container;
use Framework\Application\Render;
use Framework\Application\Settings;
use Framework\Syscrack\Game\Computer;
use Framework\Syscrack\Game\Utilities\PageHelper;

$session = Container::getObject('session');

if ($session->isLoggedIn()) {

    $session->updateLastAction();
}

$pagehelper = new PageHelper();

$computer_controller = new Computer();
?>

<!DOCTYPE html>
<html>

<?php

if (Settings::getSetting('syscrack_globe_enabled')) {

    Render::view('syscrack/templates/template.header', array('pagetitle' => 'Syscrack | Game', 'scripts' => array(
        '<script src="http://www.webglearth.com/v2/api.js"></script>'
    )));

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
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        /* Firefox < 16 */
        @-moz-keyframes fadein {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        /* Safari, Chrome and Opera > 12.1 */
        @-webkit-keyframes fadein {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        /* Internet Explorer */
        @-ms-keyframes fadein {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        /* Opera < 12.1 */
        @-o-keyframes fadein {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .jumbotron {
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
    <?php
} else {
    Render::view('syscrack/templates/template.header', array('pagetitle' => 'Syscrack | Game'));
}
?>

<body>
<div class="container">
    <?php

    Render::view('syscrack/templates/template.navigation');

    $stats = new \Framework\Syscrack\Game\Statistics();
    ?>
    <div class="row">
        <div class="col-md-4">
            <div class="thumbnail">
                <div class="caption">
                    <h3>Internet Browser</h3>
                    <p>Surf the internet, find new websites to hack. Maybe you'll find some sweet
                        new software too. <b>You may also find helpful information to assist you.</b></p>
                    <p><a href="/game/internet/" class="btn btn-primary" role="button">Open</a></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="thumbnail">
                <div class="caption">
                    <h3>Finances</h3>
                    <p>View your finances, transfer cash between accounts anonymously. Sit meticulously
                        counting the loads of dosh in your accounts.</p>
                    <p><a href="/finances/" class="btn btn-primary" role="button">Open</a></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="thumbnail">
                <div class="caption">
                    <h3>Root</h3>
                    <p>View your current systems root. Check up on your softwares and <b>view your access logs.</b>
                        Remember to keep a close
                        eye out for intruders!</p>
                    <p><a href="/computer/" class="btn btn-primary" role="button">Open</a></p>
                </div>
            </div>
        </div>
    </div>
    <?php

    if (Settings::getSetting('syscrack_globe_enabled')) {

        ?>
        <div class="row" id="stats">
            <div class="col-sm-12">
                <div class="jumbotron" style="height: 375px; padding: 5px; margin: 0; box-shadow: #0f0f0f ">
                    <div style="position: absolute; width: 95%; padding-left: 2.5%; padding-right: 2.5%; height: 365px; color:white; z-index: 2;">
                        <h1>
                            SC:\\<?= \Framework\Application\Settings::getSetting('syscrack_game_name') ?>
                        </h1>

                        <?php
                        if ($stats->hasStatistics() == true) {

                            ?>
                            <p>
                                <?= Settings::getSetting('syscrack_currency') . number_format($stats->getStatistic('collected')) ?>
                                Collected
                            </p>
                            <p>
                                <?= $stats->getStatistic('virusinstalls') ?> Virus Installs
                            </p>
                            <p>
                                <?= $stats->getStatistic('hacks') ?> Hacks
                            </p>
                            <?php
                        }
                        ?>
                    </div>
                    <div id="earth"
                         style="width: 100%; height: 365px; position: absolute; z-index: 1; background: black;"></div>
                </div>
            </div>
            <script>

                <?php

                try {

                    if ($_SERVER['REMOTE_ADDR'] == "::1" || $_SERVER['REMOTE_ADDR'] == 'localhost') {

                        $location = json_decode(file_get_contents('http://freegeoip.net/json/'));
                    } else {

                        $location = json_decode(file_get_contents('http://freegeoip.net/json/' . $_SERVER['REMOTE_ADDR']));
                    }
                } catch (Exception $error) {

                    $location = new stdClass();

                    $location->longitude = 0;

                    $location->latitude = 0;
                }
                ?>

                var options = {zoom: 10, position: [<?=$location->latitude?>,<?=$location->longitude?>]};
                var earth = new WE.map('earth', options);
                // Start a simple rotation animation
                var before = null;
                var zoom = 10;

                var bingKey = 'AsLurrtJotbxkJmnsefUYbatUuBkeBTzTL930TvcOekeG8SaQPY9Z5LDKtiuzAOu';

                bingA = earth.initMap(WebGLEarth.Maps.BING, ['Aerial', bingKey]);

                earth.setBaseMap(bingA);

                requestAnimationFrame(function animate(now) {
                    var c = earth.getPosition();
                    var elapsed = before ? now - before : 0;
                    zoom = zoom - 0.001;

                    if (zoom < 3) {

                        zoom = zoom + 0.0005
                    }

                    if (zoom < 2) {

                        zoom = zoom + 0.00025;
                    }

                    if (zoom < 1) {

                        zoom = zoom + 0.000245;
                    }

                    before = now;
                    earth.setCenter([c[0], c[1] + 0.1 * (elapsed / 150)]);
                    earth.setZoom(zoom);
                    requestAnimationFrame(animate);
                });
            </script>
        </div>
        <?php
    }
    ?>
    <div class="row" style="margin-top: 2.5%;">
        <div class="col-sm-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Virtual Computer
                </div>
                <div class="panel-body">
                    <?php

                    if ($computer_controller->hasCurrentComputer()) {

                        $currentcomputer = $computer_controller->getComputer($computer_controller->getCurrentUserComputer());

                        ?>

                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <?= $currentcomputer->ipaddress ?>
                                <small>
                                    <span class="badge" style="float: right;"><?= $currentcomputer->type ?></span>
                                </small>
                            </div>
                            <div class="panel-body">
                                <button style="width: 100%;" class="btn btn-primary"
                                        onclick="window.location.href = '/computer/'">
                                    <span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span> View
                                </button>
                            </div>
                        </div>
                        <?php
                    }

                    $usercomputers = $computer_controller->getUserComputers($session->getSessionUser());

                    $count = 0;

                    foreach ($usercomputers as $value) {

                        if ($count >= Settings::getSetting('syscrack_vpc_viewcount')) {

                            $count++;

                            continue;
                        }

                        if ($computer_controller->getCurrentUserComputer() == $value->computerid) {

                            $count++;

                            continue;
                        } else {

                            ?>

                            <form method="post">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <?= $value->ipaddress ?>
                                        <small>
                                            <span class="badge" style="float: right;"><?= $value->type ?></span>
                                        </small>
                                    </div>
                                    <div class="panel-body">
                                        <button style="width: 100%;" class="btn btn-success" name="action"
                                                value="switch" type="submit">
                                            <span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Switch
                                            <input type="hidden" name="computerid" value="<?= $value->computerid ?>">
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <?php
                        }

                        $count++;
                    }

                    if ($count >= Settings::getSetting('syscrack_vpc_viewcount')) {

                        ?>

                        <div class="panel panel-danger">
                            <div class="panel-body">
                                Some computers have been removed from this list as it was getting too long, you can
                                <a href="/game/computer">view all your virtual computers here.</a>
                            </div>
                        </div>
                        <?php
                    }

                    ?>
                </div>
                <div class="panel-footer">
                    <div class="badge"><?= $count ?> computers in total</div> <small>Click <a href="/game/computer">here to buy a new computer</a></small>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    News
                </div>
                <div class="panel-body">
                    <p>
                        There is no news to report...
                    </p>
                </div>
            </div>
        </div>
    </div>
    <?php

    Render::view('syscrack/templates/template.footer', array('breadcrumb' => true));
    ?>
</div>
</body>
</html>