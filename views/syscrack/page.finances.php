<?php

    use Framework\Application\Container;
    use Framework\Application\Settings;
    use Framework\Syscrack\Game\BankDatabase;
    use Framework\Syscrack\Game\Computer;
    use Framework\Syscrack\Game\Finance;
    use Framework\Syscrack\Game\NPC;
    use Framework\Syscrack\Game\Utilities\PageHelper;

    $session = Container::getObject('session');

    if( $session->isLoggedIn() )
    {

        $session->updateLastAction();
    }

    if( isset( $finance ) == false )
    {

        $finance = new Finance();
    }

    if( isset( $computer ) == false )
    {

        $computer = new Computer();
    }

    if( isset( $pagehelper ) == false )
    {

        $pagehelper = new PageHelper();
    }

    if( isset( $accountdatabase ) == false )
    {

        $bankdatabase = new BankDatabase();
    }
?>
<!DOCTYPE html>
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
                    <h5 style="color: #ababab" class="text-uppercase">
                        Finances
                    </h5>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            Total Cash
                        </div>
                        <div class="panel-body text-center">
                            <h1>
                                <?=Settings::getSetting('syscrack_currency') . $pagehelper->getCash()?>
                            </h1>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            Number of Accounts
                        </div>
                        <div class="panel-body text-center">
                            <h1>
                                <?=$finance->getUserBankAccounts( $session->getSessionUser() )->count()?>
                            </h1>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            Accounts Hacked <span class="badge" style="float: right;"><a href="/game/accountbook" style="color: rgb(136, 136, 136);">Go to Account Book</a></span>
                        </div>
                        <div class="panel-body text-center">
                            <h1>
                                <?=count( $bankdatabase->getDatabase( $session->getSessionUser() ) )?>
                            </h1>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <h5 style="color: #ababab" class="text-uppercase">
                        Options
                    </h5>
                    <div class="list-group">
                        <a href="/finances/" class="list-group-item active">
                            <h4 class="list-group-item-heading">Finances</h4>
                            <p class="list-group-item-text">Takes you back to the finance page.</p>
                        </a>
                    </div>
                    <div class="list-group">
                        <a href="/finances/transfer/" class="list-group-item">
                            <h4 class="list-group-item-heading">Transfer</h4>
                            <p class="list-group-item-text">Transfer cash to another account anonymously.</p>
                        </a>
                    </div>
                </div>
                <div class="col-md-8">
                    <h5 style="color: #ababab" class="text-uppercase">
                        Accounts
                    </h5>
                    <?php
                        $accounts = $finance->getUserBankAccounts( $session->getSessionUser() );

                        if( empty( $accounts ) )
                        {

                            ?>
                                <div class="panel panel-danger">
                                    <div class="panel-heading">
                                        No accounts found
                                    </div>
                                    <div class="panel-body text-center">
                                        You currently don't have any accounts, maybe you should go make a bank account?
                                    </div>
                                </div>
                            <?php
                        }
                        else
                        {

                            $npc = new NPC();

                            foreach( $accounts as $account )
                            {

                                if( $computer->computerExists( $account->computerid ) == false )
                                {

                                    continue;
                                }

                                ?>
                                    <div class="panel panel-info">
                                        <div class="panel-heading">
                                            #<?=$account->accountnumber?>

                                            <span class="badge" style="float: right;">
                                                <?php
                                                    if( $npc->hasNPCFile( $account->computerid ) )
                                                    {

                                                        $schema = $npc->getNPCFile( $account->computerid );

                                                        if( isset( $schema['name'] ) == false )
                                                        {

                                                            echo( $computer->getComputer( $account->computerid )->ipaddress );
                                                        }
                                                        else
                                                        {

                                                            echo( $schema['name'] );
                                                        }
                                                    }
                                                    else
                                                    {

                                                        echo( $computer->getComputer( $account->computerid )->ipaddress );
                                                    }
                                                ?>
                                            </span>
                                        </div>
                                        <div class="panel panel-body" style="margin-bottom: 0; padding-bottom: 0;">
                                            <div class="row">
                                                <div class="col-sm-3 text-center">
                                                    <h3 style="padding-top: 27.5%;">
                                                        <?=Settings::getSetting('syscrack_currency') . number_format( $account->cash )?>
                                                    </h3>
                                                </div>
                                                <div class="col-sm-9">
                                                    <ul class="list-group">
                                                        <li class="list-group-item">
                                                            Bank Address
                                                            <span class="badge right">
                                                                <a style="color: white;" href="/game/internet/<?=$computer->getComputer( $account->computerid )->ipaddress?>">
                                                                    <?=$computer->getComputer( $account->computerid )->ipaddress?>
                                                                </a>
                                                            </span>
                                                        </li>
                                                        <li class="list-group-item">
                                                            Account Number <span class="badge right"><?=$account->accountnumber?></span>
                                                        </li>
                                                        <li class="list-group-item">
                                                            Account ID <span class="badge right"><?=$account->accountid?></span>
                                                        </li>
                                                        <li class="list-group-item">
                                                            Time Created <span class="badge right"><?=date("F j, Y, g:i a", $account->timecreated )?></span>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                            }
                        }
                    ?>
                </div>
            </div>
            <?php

                Flight::render('syscrack/templates/template.footer', array('breadcrumb' => true ) );
            ?>
        </div>
    </body>
</html>
