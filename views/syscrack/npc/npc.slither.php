<?php

    use Framework\Syscrack\Game\Internet;
    use Framework\Syscrack\Game\NPC;

    if( isset( $npc ) == false )
    {

        $npc = new NPC();
    }

    if( isset( $internet ) == false )
    {

        $internet = new Internet();
    }

    $current_computer = $internet->getComputer( $ipaddress );
?>

<div class="row">
    <div class="col-lg-12">
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

                    echo 'Slither';
                }
            ?>
        </h5>
        <div class="embed-responsive">
            <iframe class="embed-responsive-item" src="https://www.gameflare.com/embed/slitherio/" frameborder="0" scrolling="no"></iframe>
        </div>
    </div>
</div>