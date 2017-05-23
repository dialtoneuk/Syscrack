<?php

    use Framework\Syscrack\Game\Internet;
    use Framework\Syscrack\Game\Schema;

    if( isset( $npc ) == false )
    {

        $npc = new Schema();
    }

    if( isset( $internet ) == false )
    {

        $internet = new Internet();
    }

    $current_computer = $internet->getComputer( $ipaddress );
?>
<div class="row">
    <div class="col-md-12">
        <h5 style="color: #ababab" class="text-uppercase">
            <?php
                if( $npc->hasSchema( $current_computer->computerid ) )
                {

                    $schema = $npc->getSchema( $current_computer->computerid );

                    if( isset( $schema['name'] ) )
                    {

                        echo $schema['name'];
                    }
                }
                else
                {

                    echo 'Under Construction';
                }
            ?>
        </h5>
        <p class="text-center">
            <img src="http://cdn.business2community.com/wp-content/uploads/2016/04/image.gif.gif">
        </p>
    </div>
</div>