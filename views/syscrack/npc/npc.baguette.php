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
    <div class="col-md-12">
        <div class="page-header" style="margin-top: 0;">
            <h1>
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

                        echo 'Baguette Salesman';
                    }
                ?>
            </h1>
        </div>
        <p class="text-center">
            <img style="width: 100%; height: auto;" src="https://i.imgur.com/w3mCvS2.jpg">
        </p>
    </div>
</div>