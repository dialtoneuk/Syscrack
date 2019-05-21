<?php
    use Framework\Application\Render;
?>
<!DOCTYPE html>
<html>
<?php
Render::view('syscrack/templates/template.header', array('pagetitle' => 'Syscrack | Game'));
?>
<body>
<div class="container">
	<?php
        Render::view('syscrack/templates/template.navigation');
        Render::view('syscrack/templates/template.errors');
	?>
    <div class="row">
        <div class="col-md-4">
            <h5 style="color: #ababab" class="text-uppercase">
                Options
            </h5>
            <div class="list-group">
                <a href="/finances/" class="list-group-item">
                    <h4 class="list-group-item-heading">Finances</h4>
                    <p class="list-group-item-text">Takes you back to the finance page.</p>
                </a>
            </div>
            <div class="list-group">
                <a href="/finances/transfer/" class="list-group-item active">
                    <h4 class="list-group-item-heading">Transfer</h4>
                    <p class="list-group-item-text">Transfer cash to another account anonymously.</p>
                </a>
            </div>
        </div>
        <div class="col-md-8">
            <h5 style="color: #ababab" class="text-uppercase">
                Transfer to account
            </h5>
            <div class="panel panel-default">
                <div class="panel-body text-center">
                    Transferring from one account to another is completely anonymous, no logs are made on any server
                    connected
                    to this transfer.
                </div>
            </div>
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
            } else {

                ?>

                <div class="panel panel-info">
                    <div class="panel-body">
                        <form method="post" style="padding: 0; margin: 0;">
	                        <?php
		                        Render::view("syscrack/templates/template.account.search", array('values' => @$accounts ) );
	                        ?>
                            <div class="input-group input-group" style="margin-top: 2.5%;">
                                <span class="input-group-addon" id="basic-addon1">To</span>
                                <input type="text" class="form-control" name="targetaccount" placeholder="00000000"
                                       aria-describedby="basic-addon1">
                                <span class="input-group-addon" id="basic-addon1">@</span>
                                <input type="text" class="form-control" name="ipaddress" placeholder="1.2.3.4"
                                       aria-describedby="basic-addon1">
                                <span class="input-group-addon" id="basic-addon1"><span
                                            class="glyphicon glyphicon-gbp"></span></span>
                                <input type="number" class="form-control" name="amount" placeholder="25.0" value="0.0"
                                       aria-describedby="basic-addon1">
                            </div>
                            <button style="width: 100%; margin-top: 2.5%;" class="btn btn-info" name="action"
                                    value="transfer" type="submit">
                                <span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Transfer
                            </button>
                        </form>
                    </div>
                </div>
                <?php
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
