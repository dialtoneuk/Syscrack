<?php

    use Framework\Syscrack\Game\Internet;
    use Framework\Syscrack\Game\NPC;
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
        <div class="input-group">
            <input type="text" class="form-control" id="ipaddress" name="ipaddress" placeholder="<?php if( isset( $ipaddress ) ){ echo $ipaddress; } else { echo $internet->getComputerAddress( \Framework\Application\Settings::getSetting('syscrack_whois_computer')); }?>">
            <span class="input-group-btn">
                <button class="btn btn-default" onclick="window.location.href = '/game/internet/' . $('#ipaddress').value()">Connect</button>
            </span>
        </div><!-- /input-group -->
        <div class="panel panel-default" style="margin-top: 2.5%">
            <div class="panel-body" style="background: black; color: limegreen; text-decoration: none;">
                <?php

                    $computer = $internet->getComputer( $ipaddress );

                ?>

                <p>
                    $ view <?=$ipaddress?>
                </p>
                <p>
                    ==[ Please Choose An Action ]==
                </p>
                <p>
                    $ <a style="color: limegreen;" href="/game/internet/<?=$ipaddress?>/login">login</a>
                </p>
            </div>
            <div class="panel-footer">
                <?php echo strtoupper( $pagehelper->getComputerType( $internet->getComputer( $ipaddress )->computerid ) ); echo ' <small>' . date('d-M-y H:m:s') . '</small>';?>
            </div>
        </div>
    </form>
</div>