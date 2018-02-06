<?php

use Framework\Application\Container;
use Framework\Application\Render;
use Framework\Application\Settings;

$session = Container::getObject('session');

if ($session->isLoggedIn()) {

    $session->updateLastAction();
}
?>

<!DOCTYPE html>
<html>

<?php

Render::view('syscrack/templates/template.header', array('pagetitle' => 'Syscrack'));
?>
<style>
    .carousel {
        -webkit-touch-callout: none; /* iOS Safari */
        -webkit-user-select: none; /* Safari */
        -khtml-user-select: none; /* Konqueror HTML */
        -moz-user-select: none; /* Firefox */
        -ms-user-select: none; /* Internet Explorer/Edge */
        user-select: none;
    }
</style>
<body>
<div class="container">

    <?php

    Render::view('syscrack/templates/template.navigation');
    ?>
    <div class="row">
        <div class="col-sm-12">
            <?php

            if (isset($_GET['error']))
                Render::view('syscrack/templates/template.alert', array('message' => $_GET['error']));
            elseif (isset($_GET['success']))
                Render::view('syscrack/templates/template.alert', array('message' => Settings::getSetting('alert_success_message'), 'alert_type' => 'alert-success'));
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div style="background: black; height: 400px;" id="carousel" class="carousel slide" data-ride="carousel">
                <!-- Indicators -->
                <ol class="carousel-indicators">
                    <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
                    <li data-target="#carousel-example-generic" data-slide-to="1"></li>
                    <li data-target="#carousel-example-generic" data-slide-to="2"></li>
                    <li data-target="#carousel-example-generic" data-slide-to="3"></li>
                    <li data-target="#carousel-example-generic" data-slide-to="4"></li>
                </ol>

                <!-- Wrapper for slides -->
                <div class="carousel-inner" role="listbox">
                    <div class="item active">
                        <div style="color: white; width: 100%; height: 400px; background: url('<?=Render::getAssetsLocation()?>img/art/art_prompt.png') center no-repeat;"></div>
                        <div class="carousel-caption">
                            <p>Hack your victims and infect them with your doom.</p>
                        </div>
                    </div>
                    <div class="item">
                        <div style="color: white; width: 100%; height: 400px; background: url('<?=Render::getAssetsLocation()?>img/art/art_monitor.png') center no-repeat;"></div>
                        <div class="carousel-caption">
                            <p>Control multiple computers.</p>
                        </div>
                    </div>
                    <div class="item">
                        <div style="color: white; width: 100%; height: 400px; background: url('<?=Render::getAssetsLocation()?>img/art/art_synth.png') center no-repeat;"></div>
                        <div class="carousel-caption">
                            <p>Have your own virtual marketplace and sell software and hardware.</p>
                        </div>
                    </div>
                    <div class="item">
                        <div style="color: white; width: 100%; height: 400px; background: url('<?=Render::getAssetsLocation()?>img/art/art_stockmarket.png') center no-repeat;"></div>
                        <div class="carousel-caption">
                            <p>Become a bitcoin barron, host your own bitcoin exchanges.</p>
                        </div>
                    </div>
                    <div class="item">
                        <div style="color: white; width: 100%; height: 400px; background: url('/assets/img/art/art_code.png') center no-repeat;"></div>
                        <div class="carousel-caption">
                            <p>Syscrack is a completely free and open source game.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Controls -->
            <a class="left carousel-control" style="background: none" href="#carousel" role="button" data-slide="prev">
                <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="right carousel-control" style="background: none" href="#carousel" role="button" data-slide="next">
                <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
    </div>
    <div class="row" style="margin-top: 2.5%;">
        <div class="col-sm-12">
            <div class="panel panel-default" style="padding: 2%;">
                <h5 class="text-center text-uppercase">
                    An Open Source Hacking Simulator, simulated on a Virtual Internet
                </h5>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <div class="panel panel-default">
                <div class="panel-body text-center">
                    <h1>
                        <span class="glyphicon glyphicon-arrow-up"></span>
                    </h1>
                    <p>
                        Find new and stronger softwares to increase your hacking power.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="panel panel-default">
                <div class="panel-body text-center">
                    <h1>
                        <span class="glyphicon glyphicon-certificate"></span>
                    </h1>
                    <p>
                        Install a wide array of viruses onto your victims in order to
                        raise funds.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="panel panel-default">
                <div class="panel-body text-center">
                    <h1>
                        <span class="glyphicon glyphicon-hdd"></span>
                    </h1>
                    <p>
                        Become stronger by buying new computers and hardwares.
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default" style="padding: 2%;">
                <h5 class="text-center text-uppercase">
                    Become the hacker you always wanted to be
                </h5>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <h5 style="color: #ababab" class="text-uppercase">
                Hack The Planet
            </h5>
            <p>
                Set in <strong>1986</strong> on a forever evolving timeline, you are an
                underground computer hacker in the hay-days of virus creation for fun. You have no
                objective and are free to do as you please.
            </p>
            <p>
                Time will progress around you, you will watch the NSA's surveillance service be born and
                slowly watch the world become as it is today. It is up to you and your actions to shape
                the future of the internet.
            </p>
            <p>
                Using the best softwares you can find, and the best hardware money can buy. It is up to you
                how far you want to go. Become a hacker for good, or for bad. The choice is in your hands.
            </p>
            <p>

                <?php
                if ($session->isLoggedIn()) {

                    ?>

                    So what are you waiting for? <a href="/game/">Get hacking!</a>
                    <?php
                } else {

                    ?>

                    So what are you waiting for? <a href="/register/">Register a new account and get hacking!</a>
                    <?php
                }
                ?>
            </p>
            <h5 style="color: #ababab" class="text-uppercase">
                FAQ
            </h5>
            <p>
                This game is a hacking simulator and does not involve any real hacking.
            </p>
            <p>
                The game is 100% free to play, and always will be. We will never hide features from you and
                ask you to pay.
            </p>
            <p>
                The game is constantly being updated and worked on, each update will be different to the rest
                and truly add something amazing.
            </p>
        </div>
        <div class="col-md-6">
            <h5 style="color: #ababab" class="text-uppercase">
                Features
            </h5>
            <ul class="list-group">
                <li class="list-group-item"><span class="glyphicon glyphicon-certificate"></span> Hack and infect users
                    with your deadly viruses.
                </li>
                <li class="list-group-item"><span class="glyphicon glyphicon glyphicon-usd"></span> Make money to buy
                    better softwares, hardwares and computer customizations.
                </li>
                <li class="list-group-item"><span class="glyphicon glyphicon glyphicon-briefcase"></span> Be your own
                    bank and bitcoin exchange.
                </li>
                <li class="list-group-item"><span class="glyphicon glyphicon-wrench"></span> Sell hardwares and
                    softwares on your own marketplace.
                </li>
                <li class="list-group-item"><span class="glyphicon glyphicon-flash"></span> Show your power by DDoSing
                    your victims.
                </li>
                <li class="list-group-item"><span class="glyphicon glyphicon-tasks"></span> Control multiple computers
                    at once.
                </li>
                <li class="list-group-item"><span class="glyphicon glyphicon-thumbs-down"></span> No 'Freemium'
                    practices or 'pay to win' business models.
                </li>
                <li class="list-group-item"><span class="glyphicon glyphicon-scissors"></span> Built from the ground up
                    to be modded and expanded.
                </li>
                <li class="list-group-item"><span class="glyphicon glyphicon-sunglasses"></span> Completely free and <a
                            href="https://github.com/dialtoneuk/Syscrack2017/">open source</a>, no ads.
                </li>
                <li class="list-group-item"><span class="glyphicon glyphicon-heart-empty"></span> and the list goes
                    on... with more being added every update!
                </li>
            </ul>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default" style="padding: 2%;">
                <h5 class="text-center text-uppercase">
                    Change the future and be the controller of the fate of the internet
                </h5>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <div class="panel panel-default">
                <div class="panel-body text-center">
                    <h1>
                        <span class="glyphicon glyphicon-phone"></span>
                    </h1>
                    <p>
                        Join our discord and chat to other players and show off your ego.
                    </p>
                    <button style="width: 100%;" class="btn btn-info"
                            onclick="window.location.href = '<?= Settings::getSetting('syscrack_discord_main') ?>'">
                        Join Discord
                    </button>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="panel panel-default">
                <div class="panel-body text-center">
                    <h1>
                        <span class="glyphicon glyphicon-open"></span>
                    </h1>
                    <p>
                        Join the modders discord and get help with modding the game.
                    </p>
                    <button style="width: 100%;" class="btn btn-info"
                            onclick="window.location.href = '<?= Settings::getSetting('syscrack_discord_modders') ?>'">
                        Join Discord
                    </button>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="panel panel-default">
                <div class="panel-body text-center">
                    <h1>
                        <span class="glyphicon glyphicon-thumbs-up"></span>
                    </h1>
                    <p>
                        Like us on facebook and message us with your suggestions.
                    </p>
                    <button style="width: 100%;" class="btn btn-info"
                            onclick="window.location.href = '<?= Settings::getSetting('syscrack_facebook_page') ?>'">
                        Facebook
                    </button>
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