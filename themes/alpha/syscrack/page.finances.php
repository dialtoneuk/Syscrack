<?php
    use Framework\Application\Render;
?>
<!DOCTYPE html>
<html>
<?php

Render::view('syscrack/templates/template.header', array('pagetitle' => 'Syscrack | Game'));
?>
<style>
    .vertical-center {
        min-height: 100%; /* Fallback for browsers do NOT support vh unit */
        min-height: 100vh; /* These two lines are counted as one :-)       */

        display: flex;
        align-items: center;
    }
</style>
<body>
<div class="container">
    <?php

    Render::view('syscrack/templates/template.navigation');
    ?>
    <div class="row">
        <div class="col-sm-4">
            <div class="panel panel-success">
                <div class="panel-heading">
                    Total Cash
                </div>
                <div class="panel-body text-center">
                    <h1><?= @$settings['syscrack_currency'] . number_format( @$cash )?></h1>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Number of Accounts
                </div>
                <div class="panel-body text-center">
                    <h1>
                        <?= @count(@$accounts) ?>
                    </h1>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Accounts Hacked <a style="float: right;" href="/game/accountbook">Go to Account Book</a>
                </div>
                <div class="panel-body text-center">
                    <h1>
                        <?php
                        if (empty($bankdatabase) == false)
                            echo count(@$count);
                        else
                            echo 0;
                        ?>
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
            if (empty($accounts)) {

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
                foreach ($accounts as $account) 
                {
                    
                    ?>
                    <div class="panel panel-default">
                        <div class="panel panel-body" style="margin-bottom: 0; padding-bottom: 0;">
                            <?php
                                if( isset( $metaset[ $account->computerid ] ) )
                                {
                                    $metadata = $metaset[ $account->computerid ];

                                    ?>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="panel panel-default">
                                                <div class="panel-body">
                                                    <h5>Account #<?=$account->accountnumber?><small> at <?=@$metadata->custom["name"]?></small></h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                            ?>
                            <div class="row">
                                <div class="col-sm-12">
                                    <ul class="list-group">
                                        <li class="list-group-item">
                                            Bank Address
                                            <p class="right">
                                                <a style="color: white;" href="/game/internet/<?= @$addresses[ $account->computerid ]?>">
                                                    <?=  @$addresses[ $account->computerid ] ?>
                                                </a>
                                            </p>
                                        </li>
                                        <li class="list-group-item">
                                            <small>Account Number</small>
                                            <p><?= @$account->accountnumber ?></p>
                                        </li>
                                        <li class="list-group-item">
                                            <small>Account ID</small>
                                            <p><?= @$account->accountid ?></p>
                                        </li>
                                        <li class="list-group-item">
                                            <small>Time Created</small>
                                            <p><?= @date("F j, Y, g:i a", $account->timecreated) ?><p>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="panel panel-success">
                                        <div class="panel-body text-center">
                                            <h5><?= @$settings['syscrack_currency'] . number_format($account->cash) ?></h5>
                                        </div>
                                    </div>
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

    Render::view('syscrack/templates/template.footer', array('breadcrumb' => true));
    ?>
</div>
</body>
</html>
