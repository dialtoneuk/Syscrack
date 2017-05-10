<?php

    use Framework\Syscrack\Game\Internet;
    use Framework\Syscrack\Game\NPC;
    use Framework\Syscrack\Game\Utilities\PageHelper;

    $npc = new NPC();

    if( isset( $internet ) == false )
    {

        $internet = new Internet();
    }

    if( isset( $pagehelper ) == false )
    {

        $pagehelper = new PageHelper();
    }
?>
<div class="col-md-8">
    <form method="post" action="/game/internet/">
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

                    if( $npc->hasNPCFile( $computer->computerid ) && $npc->hasNPCPage( $computer->computerid ) )
                    {

                        Flight::render( $npc->getNPCPageLocation( $computer->computerid ), array( 'internet' => $internet, 'ipaddress' => $ipaddress, 'npc' => $npc ) );
                    }
                    else
                    {
                        ?>
                            <p>
                                Connection success
                            </p>
                        <?php
                    }
                ?>
            </div>
            <div class="panel-footer">
                <?php echo strtoupper( $pagehelper->getComputerType( $internet->getComputer( $ipaddress )->computerid ) ); echo ' <small>' . date('d-M-y H:m:s') . '</small>';?>
            </div>
        </div>
    </form>
</div>