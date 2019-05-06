<?php

use Framework\Application\Container;
use Framework\Application\Render;
use Framework\Application\Settings;

$session = Container::getObject('session');

if ($session->isLoggedIn()) {

    $session->updateLastAction();

    Flight::redirect('/game/');

    exit;
}
?>
<!DOCTYPE html>
<html>
<?php

Render::view('syscrack/templates/template.header', array('pagetitle' => 'Syscrack | Login'));
?>
<body>
<div class="container">
    <?php

    Render::view('syscrack/templates/template.navigation');
    ?>
    <div class="row" style="margin-top: 2.5%;">
        <div class="col-sm-12">
            <?php

            if (isset($_GET['error']))
                Render::view('syscrack/templates/template.alert', array('message' => $_GET['error']));
            elseif (isset($_GET['success']))
                Render::view('syscrack/templates/template.alert', array('message' => $settings['alert_success_message'], 'alert_type' => 'alert-success') );
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <form style="margin: 0; padding: 0;">
                <div class="jumbotron">
                    <h1>Verify your email</h1>
                    <p>
                        If you have your token, please enter your token below to verify your email
                    </p>
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon">Token</span>
                        <input type="text" class="form-control" placeholder="da39a3ee5e6b4b0d3255bfef95601890afd80709"
                               name="token" aria-describedby="basic-addon">
                    </div>
                    <div class="btn-group btn-group-justified" role="group" style="margin-top: 2.5%;" aria-label="...">
                        <div class="btn-group" role="group">
                            <button type="submit" class="btn btn-default">Verify</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default" style="padding: 2%;">
                <h5 class="text-center text-uppercase">
                    If you haven't signed up already
                </h5>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <h5 style="color: #ababab" class="text-uppercase">
                Register
            </h5>
            <ul class="list-group">
                <li class="list-group-item"><span class="glyphicon glyphicon-certificate"></span> Hack and infect users
                    with your deadly viruses.
                </li>
                <li class="list-group-item"><span class="glyphicon glyphicon glyphicon-usd"></span> Purchase from
                    various marketplaces softwares and hardwares.
                </li>
                <li class="list-group-item"><span class="glyphicon glyphicon glyphicon-briefcase"></span> Be your own
                    bank and bitcoin exchange.
                </li>
                <li class="list-group-item"><span class="glyphicon glyphicon-wrench"></span> Sell hardwares and
                    softwares on your own marketplace.
                </li>
                <li class="list-group-item"><span class="glyphicon glyphicon-sunglasses"></span> Completely free and <a
                            href="https://github.com/dialtoneuk/Syscrack2017/">open source</a>, no ads.
                </li>
            </ul>
            <div class="btn-group btn-group-justified" role="group" aria-label="...">
                <div class="btn-group" role="group">
                    <button type="button" onclick='window.location.href="/register/"' class="btn btn-default">Register
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <h5 style="color: #ababab" class="text-uppercase">
                Get Social
            </h5>
            <div class="panel panel-default">
                <div class="panel-body text-center">
                    <h1 style="font-size: 4.5em;">
                        <span class="glyphicon glyphicon-phone"></span>
                    </h1>
                    <p>
                        Join our discord and give us suggestions! We have a very active community of hackers,
                        programmers and
                        modders who are always looking to help new players, go give it a spin! We will also announce new
                        updates
                        here just before they are released.
                    </p>
                    <div class="btn-group btn-group-justified" role="group" aria-label="...">
                        <div class="btn-group" role="group">
                            <button type="button"
                                    onclick="window.location.href = '<?= $settings['syscrack_discord_main'] ?>'"
                                    class="btn btn-info">Join Discord
                            </button>
                        </div>
                    </div>
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
