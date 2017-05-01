<?php

    $session = \Framework\Application\Container::getObject('session');

    if( $session->isLoggedIn() )
    {

        $session->updateLastAction();
    }
?>

<!DOCTYPE html>
<html>

    <?php

        Flight::render('syscrack/templates/template.header', array('pagetitle' => 'Syscrack') );
    ?>
    <style>
        .carousel
        {
            -webkit-touch-callout: none; /* iOS Safari */
            -webkit-user-select: none; /* Safari */
            -khtml-user-select: none; /* Konqueror HTML */
            -moz-user-select: none; /* Firefox */
            -ms-user-select: none; /* Internet Explorer/Edge */
            user-select: none; /* N
        }
    </style>
    <body>
        <div class="container">

            <?php

                Flight::render('syscrack/templates/template.navigation');
            ?>
            <div class="row">
                <div class="col-lg-12">
                    <div style="background: black" id="carousel" class="carousel slide" data-ride="carousel">
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
                                <div style="color: white; width: 100%; padding: 5%; padding-left: 10%; padding-bottom: 10%;">
                                    <h2>
                                        SC:\\hacks
                                    </h2>
                                    <p>
                                        0 Hacks
                                    </p>
                                    <p>
                                        0 Infections
                                    </p>
                                    <p>
                                        0 Transfers
                                    </p>
                                </div>
                                <div class="carousel-caption">
                                    <p>Hack your victims and infect them with your doom</p>
                                </div>
                            </div>
                            <div class="item">
                                <div style="color: white; width: 100%; padding: 5%; padding-left: 10%; padding-bottom: 10%;">
                                    <h2>
                                        SC:\\computers
                                    </h2>
                                    <p>
                                        0 Computers
                                    </p>
                                    <p>
                                        0 Softwares on computers
                                    </p>
                                    <p>
                                        $0 Spent on computers
                                    </p>
                                </div>
                                <div class="carousel-caption">
                                    <p>Control multiple computers</p>
                                </div>
                            </div>
                            <div class="item">
                                <div style="color: white; width: 100%; padding: 5%; padding-left: 10%; padding-bottom: 10%;">
                                    <h2>
                                        SC:\\market
                                    </h2>
                                    <p>
                                        0 Markets
                                    </p>
                                    <p>
                                        $0 Spent on software
                                    </p>
                                    <p>
                                        $0 Spent on hardware
                                    </p>
                                </div>
                                <div class="carousel-caption">
                                    <p>Have your own virtual marketplace and sell software and hardware</p>
                                </div>
                            </div>
                            <div class="item">
                                <div style="color: white; width: 100%; padding: 5%; padding-left: 10%; padding-bottom: 10%;">
                                    <h2>
                                        SC:\\bitcoins
                                    </h2>
                                    <p>
                                        0 Bitcoin servers
                                    </p>
                                    <p>
                                        $0 Spent on bitcoin
                                    </p>
                                    <p>
                                        0 Bitcoins Transfered
                                    </p>
                                </div>
                                <div class="carousel-caption">
                                    <p>Become a bitcoin barron, host your own bitcoin exchanges</p>
                                </div>
                            </div>
                            <div class="item">
                                <div style="color: white; width: 100%; padding: 5%; padding-left: 10%; padding-bottom: 10%;">
                                    <h2>
                                        SC:\\freeforever
                                    </h2>
                                    <p>
                                        Never 'pay-to'win
                                    </p>
                                    <p>
                                        Ad Free
                                    </p>
                                    <p>
                                        Open Source
                                    </p>
                                </div>
                                <div class="carousel-caption">
                                    <p>Syscrack is a completely free and open source game</p>
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
                <div class="col-lg-12">
                    <div class="panel panel-default" style="padding-bottom: 0.5%;">
                        <h3 class="text-center text-capitalize">
                            An Open Source Hacking Simulator, simulated on a Virtual Internet
                        </h3>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <h1 class="page-header">
                        SC:\\hack_the_planet
                    </h1>
                    <p>
                        Syscrack is a <strong>hacking simulator</strong> built from the ground up
                        to be expandable and modded. Users are thrown into a virtual internet and are
                        given a set of tools. The aim is simple, become <i>the best hacker in the world.</i>
                    </p>
                    <p>
                        Its <strong>simple and easy to start playing.</strong> This game has been designed
                        from scratch to be simple to use, fast to play, and a ton of fun. Inspired by games like
                        <a href="http://www.slavehack.com">Slavehack</a> and <a href="http://www.hackerexperience.com">Hacker Experience</a>,
                        players familiar with both games will feel right at home.
                    </p>
                    <p>

                        <?php
                            if( $session->isLoggedIn() )
                            {

                                ?>

                                    So what are you waiting for? <a href="/game/">Get hacking!</a>
                                <?php
                            }
                            else
                            {

                                ?>

                                    So what are you waiting for? <a href="/register/">Register a new account and get hacking!</a>
                                <?php
                            }
                        ?>
                    </p>
                </div>
                <div class="col-lg-6">
                    <h1 class="page-header">
                        SC:\\features
                    </h1>
                    <ul class="list-group">
                        <li class="list-group-item"><span class="glyphicon glyphicon-certificate"></span> Hack and infect users with your deadly viruses.</li>
                        <li class="list-group-item"><span class="glyphicon glyphicon glyphicon-usd"></span> Make money to buy better softwares, hardwares and computer customizations.</li>
                        <li class="list-group-item"><span class="glyphicon glyphicon glyphicon-briefcase"></span> Be your own bank and bitcoin exchange.</li>
                        <li class="list-group-item"><span class="glyphicon glyphicon-wrench"></span> Sell hardwares and softwares on your own marketplace.</li>
                        <li class="list-group-item"><span class="glyphicon glyphicon-flash"></span> Show your power by DDoSing your victims.</li>
                        <li class="list-group-item"><span class="glyphicon glyphicon-tasks"></span> Control multiple computers at once.</li>
                        <li class="list-group-item"><span class="glyphicon glyphicon-thumbs-down"></span> No 'Freemium' practices or 'pay to win' business models.</li>
                        <li class="list-group-item"><span class="glyphicon glyphicon-scissors"></span> Built from the ground up to be modded and expanded.</li>
                        <li class="list-group-item"><span class="glyphicon glyphicon-sunglasses"></span> Completely free and <a href="https://github.com/dialtoneuk/Syscrack2017/">open source</a>, no ads.</li>
                        <li class="list-group-item"><span class="glyphicon glyphicon-heart-empty"></span> and the list goes on... with more being added every update!</li>
                    </ul>
                </div>
            </div>
            <?php

                Flight::render('syscrack/templates/template.footer', array('breadcrumb' => true ) );
            ?>
        </div>
    </body>
</html>