<?php
    use Framework\Application\Render;
?>
<html>
    <?php Render::view('syscrack/templates/template.header', array('pagetitle' => 'Syscrack | Game')); ?>
    <body>
        <div class="container">
            <?php Render::view('syscrack/templates/template.navigation'); ?>
            <div class="row">
                <div class="col-sm-12">
                    <?php
                        if (isset($_GET['error']))
                            Render::view('syscrack/templates/template.alert', array('message' => $_GET['error']));
                        elseif (isset($_GET['success']))
                            Render::view('syscrack/templates/template.alert', array('message' => $settings['alert_success_message'], 'alert_type' => 'alert-success'));
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <h5 style="color: #ababab" class="text-uppercase">
                        <?php
                            if( isset( $metadata ) )
                                echo @$metadata->custom["name"]
                        ?>
                    </h5>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="row">
                                <?php
                                if ( isset( $items ) == false || isset( $purchases ) == false )
                                {
                                    ?>
                                    <div class="col-sm-12">
                                        <div class="panel panel-danger">
                                            <div class="panel-heading">
                                                Market Error
                                            </div>
                                            <div class="panel-body">
                                                This market has encounted an error, please tell a developer the following
                                                information.
                                                <ul class="list-group">
                                                    <li class="list-group-item">
                                                        Computerid <span
                                                                class="badge right"><?= @$computer->computerid ?></span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                } else {
                                    if (empty($items))
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
                                        if (empty($accounts)) {

                                            ?>
                                            <div class="col-sm-12" style="height: 120px;">
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        You currently don't have a bank account, please get one before you
                                                        attempt to purchase from a market
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
                                                    Welcome to the marketplace, here you can buy all sorts of hardwares and
                                                    softwares which will make
                                                    you stronger.
                                                </p>
                                                <h5 style="color: #ababab" class="text-uppercase">
                                                    Your Recent Transactions
                                                </h5>
                                                <ul class="list-group">
                                                    <?php
                                                    if (empty($purchases)) 
                                                    {

                                                        ?>
                                                        <li class="list-group-item">
                                                            No recent transactions recorded
                                                        </li>
                                                        <?php
                                                    } 
                                                    else 
                                                    {

                                                        foreach ($purchases as $purchase) 
                                                        {
                                                            ?>
                                                                <li class="list-group-item">
                                                                    <?= @$items[ $purchase["itemid"] ]['name'] ?>
                                                                    for <?= @$settings['syscrack_currency'] . number_format(@$items[ $purchase["itemid"] ]['price']) ?>
                                                                    <span class="badge"
                                                                          style="float: right"><?= @date("F j, Y, g:i a", $purchase['timepurchased']) ?></span>
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
                                                                All actions on this page are logged locally, so remember to
                                                                clear your transactions
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <p class="text-center">
                                                            <a href="/game/internet/<?= @$ipaddress ?>/">Go Back</a>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <?php
                                                foreach ($items as $key=>$item ) {
                                                    ?>
                                                    <form action="/game/internet/<?= @$ipaddress ?>/buy" method="post">
                                                        <div class="panel panel-info">
                                                            <div class="panel-heading">
                                                                <?= @$item['name'] ?> <span class="badge"
                                                                                             style="float: right;"><?= @$item['type'] ?></span>
                                                            </div>
                                                            <div class="panel-body">
                                                                <?php
                                                                if (@$item["computerid"] == $computer->computerid) {

                                                                    ?>
                                                                    <p style="margin: 0;" class="text-center">You have
                                                                        already
                                                                        purchased this item</>
                                                                    <?php
                                                                }
                                                                ?>
                                                                <ul class="list-group">
                                                                    <li class="list-group-item">
                                                                        Modifies <span
                                                                                class="badge right"><?= @$item['hardware'] ?></span>
                                                                    </li>
                                                                    <li class="list-group-item">
                                                                        Power <span
                                                                                class="badge right"><?= @$item['value'] ?></span>
                                                                    </li>
                                                                    <li class="list-group-item">
                                                                        Price <span
                                                                                class="badge right"><?= @$settings['syscrack_currency'] . number_format($item['price']) ?></span>
                                                                    </li>
                                                                </ul>
                                                                <select name="accountnumber"
                                                                        class="combobox input-sm form-control">
                                                                    <option></option>
                                                                    <?php
                                                                        Render::view("syscrack/templates/template.account.search", array('values' => @$accounts ) );
                                                                    ?>
                                                                </select>
                                                                <button style="width: 100%; margin-top: 2.5%;"
                                                                        class="btn btn-sm btn-info" type="submit">
                                                                        <span class="glyphicon glyphicon-gbp"
                                                                              aria-hidden="true"></span> Purchase
                                                                </button>
                                                                <input type="hidden" name="itemid"
                                                                       value="<?= @$key ?>">
                                                            </div>
                                                        </div>
                                                    </form>

                                                    <?php
                                                    }
                                                }
                                                ?>
                                            </div>
                                            <?php

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

            Render::view('syscrack/templates/template.footer', array('breadcrumb' => true));
            ?>
        </div>
    </body>
</html>
