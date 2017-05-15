<?php
    use Framework\Application\Container;
    use Framework\Application\Settings;
    use Framework\Syscrack\Game\Computer;
    use Framework\Syscrack\Game\Finance;
    use Framework\Syscrack\Game\Hardware;
    use Framework\Syscrack\Game\Internet;
    use Framework\Syscrack\Game\Market;
    use Framework\Syscrack\Game\NPC;

    $session = Container::getObject('session');

    if( isset( $internet ) == false )
    {

        $internet = new Internet();
    }

    if( isset( $computer ) == false )
    {

        $computer = new Computer();
    }

    if( isset( $npc ) == false )
    {

        $npc = new NPC();
    }

    if( isset( $market ) == false )
    {

        $market = new Market();
    }

    if( isset( $finance ) == false )
    {

        $finance = new Finance();
    }

    if( isset( $hardware ) == false )
    {

        $hardware = new Hardware();
    }

    $current_computer = $internet->getComputer( $ipaddress );
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
                <div class="col-md-12">
                    <h5 style="color: #ababab" class="text-uppercase">
                        <?php
                            if( $npc->hasNPCFile( $current_computer->computerid ) )
                            {

                                $schema = $npc->getNPCFile( $current_computer->computerid );

                                if( isset( $schema['name'] ) )
                                {

                                    echo $schema['name'];
                                }
                            }
                            else
                            {

                                ?>
                                Market
                                <?php
                            }
                        ?>
                    </h5>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="row">
                                <?php

                                    if( $market->checkMarket( $current_computer->computerid ) == false )
                                    {

                                        ?>
                                        <div class="col-sm-12">
                                            <div class="panel panel-danger">
                                                <div class="panel-heading">
                                                    Market Error
                                                </div>
                                                <div class="panel-body">
                                                    This market has encounted an error, please tell a developer the following information.
                                                    <ul class="list-group">
                                                        <li class="list-group-item">
                                                            Computerid <span class="badge right"><?=$current_computer->computerid?></span>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    else
                                    {

                                        $stocks = $market->getStock( $current_computer->computerid );

                                        if( empty( $stocks ) )
                                        {

                                            ?>
                                            <div class="col-sm-12">
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        Sorry, we are all sold out...
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                        else
                                        {

                                            $accounts = $finance->getUserBankAccounts( $session->getSessionUser() );

                                            if( empty( $accounts ) )
                                            {

                                                ?>
                                                <div class="col-sm-12" style="height: 120px;">
                                                    <div class="panel panel-default">
                                                        <div class="panel-body">
                                                            You currently don't have a bank account, please get one before you attempt to purchase from a market
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                            else
                                            {

                                                ?>
                                                    <div class="col-sm-6">
                                                        <p>
                                                            Welcome to the marketplace, here you can buy all sorts of hardwares and softwares which will make
                                                            you stronger.
                                                        </p>
                                                        <h5 style="color: #ababab" class="text-uppercase">
                                                            Recent Transactions
                                                        </h5>
                                                        <ul class="list-group">
                                                            <?php

                                                                $purchases = $market->getPurchasesByComputer( $current_computer->computerid, $computer->getCurrentUserComputer() );

                                                                if( empty( $purchases ) )
                                                                {

                                                                    ?>
                                                                        <li class="list-group-item">
                                                                            No recent transactions recorded
                                                                        </li>
                                                                    <?php
                                                                }
                                                                else
                                                                {

                                                                    foreach( $purchases as $purchase )
                                                                    {

                                                                        if( $market->hasStockItem( $current_computer->computerid, $purchase['itemid'] ) == false )
                                                                        {

                                                                            ?>
                                                                                <li class="list-group-item">
                                                                                    Purchase of discontinued product <span class="badge" style="float: right"><?=date("F j, Y, g:i a", $purchase['timepurchased'])?></span>
                                                                                </li>
                                                                            <?php
                                                                        }
                                                                        else
                                                                        {

                                                                            $item = $market->getStockItem( $current_computer->computerid, $purchase['itemid'] );

                                                                            ?>
                                                                                <li class="list-group-item">
                                                                                Purchase of <?=$item['name']?> for <?=Settings::getSetting('syscrack_currency') . number_format($item['price'])?> <span class="badge" style="float: right"><?=date("F j, Y, g:i a", $purchase['timepurchased'])?></span>
                                                                                </li>
                                                                            <?php
                                                                        }
                                                                    }
                                                                }
                                                            ?>
                                                        </ul>
                                                        <div class="row">
                                                            <div class="col-sm-12">
                                                                <div class="panel panel-default">
                                                                    <div class="panel-body text-center">
                                                                        All actions on this page are logged locally, so remember to clear your transactions
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-12">
                                                                <p class="text-center">
                                                                    <a href="/game/internet/<?=$ipaddress?>/">Go Back</a>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <?php
                                                            foreach( $stocks as $itemid=>$stock )
                                                            {
                                                                ?>
                                                                <form action="/game/internet/<?=$ipaddress?>/buy" method="post">
                                                                    <div class="panel panel-info">
                                                                        <div class="panel-heading">
                                                                            <?=$stock['name']?> <span class="badge" style="float: right;"><?=$stock['type']?></span>
                                                                        </div>
                                                                        <div class="panel-body">
                                                                            <?php
                                                                                if( $market->hasPurchase( $current_computer->computerid, $computer->getCurrentUserComputer(), $itemid ) )
                                                                                {

                                                                                    ?>
                                                                                    <p style="margin: 0;" class="text-center">You have already purchased this item</>
                                                                                    <?php
                                                                                }
                                                                                else
                                                                                {

                                                                                    if( $hardware->hasHardwareType( $computer->getCurrentUserComputer(), $stock['hardware'] ) )
                                                                                    {

                                                                                        if( $hardware->getHardwareType( $computer->getCurrentUserComputer(), $stock['hardware'] )['value'] >= $stock['value'] )
                                                                                        {

                                                                                            ?>
                                                                                            <div class="panel panel-warning">
                                                                                                <div class="panel-body text-center">
                                                                                                    Your current hardware for this type is faster than or equal the one being sold
                                                                                                </div>
                                                                                            </div>
                                                                                            <?php
                                                                                        }
                                                                                    }

                                                                                    ?>
                                                                                    <ul class="list-group">
                                                                                        <li class="list-group-item">
                                                                                            Modifies <span class="badge right"><?=$stock['hardware']?></span>
                                                                                        </li>
                                                                                        <li class="list-group-item">
                                                                                            Power <span class="badge right"><?=$stock['value']?></span>
                                                                                        </li>
                                                                                        <li class="list-group-item">
                                                                                            Price <span class="badge right"><?=Settings::getSetting('syscrack_currency') . number_format($stock['price'])?></span>
                                                                                        </li>
                                                                                    </ul>
                                                                                    <select name="accountnumber" class="combobox input-sm form-control">
                                                                                        <option></option>

                                                                                        <?php
                                                                                            if( empty( $accounts ) == false )
                                                                                            {

                                                                                                foreach( $accounts as $account )
                                                                                                {

                                                                                                    ?>
                                                                                                    <option value="<?=$account->accountnumber?>">#<?=$account->accountnumber?> (<?=Settings::getSetting('syscrack_currency') . number_format( $account->cash )?>) @<?=$computer->getComputer( $account->computerid )->ipaddress?></option>
                                                                                                    <?php
                                                                                                }
                                                                                            }
                                                                                        ?>
                                                                                    </select>
                                                                                    <button style="width: 100%; margin-top: 2.5%;" class="btn btn-sm btn-info" type="submit">
                                                                                        <span class="glyphicon glyphicon-gbp" aria-hidden="true"></span> Purchase
                                                                                    </button>
                                                                                    <input type="hidden" name="itemid" value="<?=$itemid?>">
                                                                                    <?php
                                                                                }
                                                                            ?>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                                <?php
                                                            }
                                                        ?>
                                                    </div>
                                                <?php
                                            }
                                        }
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <p class="text-center">
                        <a onclick="$('html, body').animate({ scrollTop: 0 }, 'fast');">
                            Back to the top...
                        </a>
                    </p>
                </div>
            </div>
            <?php

                Flight::render('syscrack/templates/template.footer', array('breadcrumb' => true ) );
            ?>
        </div>
    </body>
</html>
