<?php

use Framework\Syscrack\Game\Internet;
use Framework\Syscrack\Game\Schema;

if (isset($npc) == false) {

    $npc = new Schema();
}

if (isset($internet) == false) {

    $internet = new Internet();
}

$current_computer = $internet->getComputer($ipaddress);
?>

<div class="row">
    <div class="col-sm-12">
        <h5 style="color: #ababab" class="text-uppercase">
            <?php
            if ($npc->hasSchema($current_computer->computerid)) {

                $schema = $npc->getSchema($current_computer->computerid);

                if (isset($schema['name'])) {

                    echo $schema['name'];
                }
            } else {

                echo 'Nuclear Silo';
            }
            ?>
        </h5>

        <img class="img-responsive"
             src="http://scienceline.org/wp-content/uploads/2007/11/600px-radiation_warning_symbolsvg.jpg">
    </div>
</div>