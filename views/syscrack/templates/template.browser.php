<?php

    use Framework\Syscrack\Game\NPC;
    use Framework\Application\Settings;
    use Framework\Syscrack\Game\Internet;
    use Framework\Syscrack\Game\Utilities\PageHelper;

    $npc = new NPC();

    if( isset( $internet ) == false )
    {

        $interent = new Internet();
    }

    if( isset( $pagehelper ) == false )
    {

        $pagehelper = new PageHelper();
    }
?>
<div class="col-md-8">
    <form method="post" action="/game/internet/">

        <?php

        if( isset( $_GET['error'] ) )
            Flight::render('syscrack/templates/template.alert', array( 'message' => $_GET['error'] ) );
        elseif( isset( $_GET['success'] ) )
            Flight::render('syscrack/templates/template.alert', array( 'message' => 'Success', 'alert_type' => 'alert-success' ) );
        ?>
        <div class="input-group">
            <input type="text" class="form-control" id="ipaddress" name="ipaddress" placeholder="<?php if( isset( $ipaddress ) ){ echo $ipaddress; } else { echo $internet->getComputerAddress( \Framework\Application\Settings::getSetting('syscrack_whois_computer')); }?>">
            <span class="input-group-btn">
                <button class="btn btn-default" onclick="window.location.href = '/game/internet/' . $('#ipaddress').value()">Connect</button>
            </span>
        </div><!-- /input-group -->
        <div class="panel panel-default" style="margin-top: 2.5%">
            <div class="panel-body">
                <?php

                    $computer = $internet->getComputer( $ipaddress );

                    if( $npc->isNPC( $computer->computerid ) )
                    {

                        if( $npc->hasPage( $computer->computerid ) )
                        {

                            $npc->renderNPCPage( $computer->computerid );
                        }
                        else
                        {

                            ?>
                                <p>
                                    NPC Server
                                </p>
                            <?php

                        }
                    }
                    else
                    {

                        if( $computer->type == Settings::getSetting('syscrack_vpc_type') )
                        {

                            ?>
                                <p>
                                    VPC Server
                                </p>
                            <?php

                        }
                        elseif( $computer->type == Settings::getSetting('syscrack_bank_type') )
                        {

                            ?>
                                <p>
                                    Bank Server
                                </p>
                            <?php
                        }
                        elseif( $computer->type == Settings::getSetting('syscrack_bitcoin_type') )
                        {

                            ?>
                                <p>
                                    Bitcoin Server
                                </p>
                            <?php
                        }
                        else
                        {

                            ?>
                                <p>
                                    Server
                                </p>
                            <?php
                        }
                    }
                ?>
            </div>
            <div class="panel-footer">
                <?php echo strtoupper( $pagehelper->getComputerType( $internet->getComputer( $ipaddress )->computerid ) ); echo ' <small>' . date('d-M-y H:m:s') . '</small>';?>
            </div>
        </div>
    </form>
</div>