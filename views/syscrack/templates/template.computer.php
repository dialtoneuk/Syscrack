<?php

    use Framework\Syscrack\Game\NPC;
    use Framework\Syscrack\Game\Internet;
    use Framework\Syscrack\Game\Utilities\PageHelper;
    use Framework\Syscrack\Game\Computer;
    use Framework\Syscrack\Game\Softwares;
    use Framework\Syscrack\Game\Log;

    $npc = new NPC();

    if( isset( $internet ) == false )
    {

        $interent = new Internet();
    }

    if( isset( $pagehelper ) == false )
    {

        $pagehelper = new PageHelper();
    }

    $computer = new Computer();

    $softwares = new Softwares();

    $log = new Log();
?>
<div class="col-md-8">
    <?php

        if( isset( $_GET['error'] ) )
            Flight::render('syscrack/templates/template.alert', array( 'message' => $_GET['error'] ) );
        elseif( isset( $_GET['success'] ) )
            Flight::render('syscrack/templates/template.alert', array( 'message' => 'Success', 'alert_type' => 'alert-success' ) );

        if( $internet->getComputerAddress( $computer->getCurrentUserComputer() ) == $ipaddress )
        {

            ?>

                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Notice
                    </div>

                    <div class="panel-body">
                        You are currently connected to yourself
                    </div>
                </div>
            <?php
        }

    ?>
    <form method="post" action="/game/internet/">
        <div class="input-group">
            <input type="text" class="form-control" id="ipaddress" name="ipaddress" placeholder="<?php if( isset( $ipaddress ) ){ echo $ipaddress; } else { echo $internet->getComputerAddress( \Framework\Application\Settings::getSetting('syscrack_whois_computer')); }?>">
            <span class="input-group-btn">
                <button class="btn btn-default" onclick="window.location.href = '/game/internet/' . $('#ipaddress').value()">Connect</button>
            </span>
        </div><!-- /input-group -->
    </form>
    <div class="panel panel-default" style="margin-top: 2.5%">
        <div class="panel-body">
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#log">Log</a></li>
                <li><a data-toggle="tab" href="#software">Software</a></li>
            </ul>

            <div class="tab-content">
                <div id="log" class="tab-pane fade in active" style="padding-top: 2.5%;">

                    <?php

                        Flight::render('syscrack/templates/template.log', array( 'ipaddress' => $ipaddress, 'internet' => $internet ));
                    ?>
                </div>
                <div id="software" class="tab-pane fade" style="padding-top: 2.5%;">

                    <?php

                        Flight::render('syscrack/templates/template.softwares', array( 'ipaddress' => $ipaddress, 'softwares' => $softwares, 'computer' => $computer, 'internet' => $internet ));
                    ?>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <?php echo strtoupper( $pagehelper->getComputerType( $internet->getComputer( $ipaddress )->computerid ) ); echo ' <small>' . date('d-M-y H:m:s') . '</small>';?>
        </div>
    </div>
</div>