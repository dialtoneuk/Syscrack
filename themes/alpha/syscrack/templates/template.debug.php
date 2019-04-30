<?php

use Framework\Syscrack\Game\Utilities\PageHelper;

if (empty($pagehelper)) {

    $pagehelper = new PageHelper();
}
?>
<div class="panel panel-default">
    <div class="panel-heading">Debug Information</div>
    <div class="panel-body">
        <p>
            You currently have <strong><?= $pagehelper->getCash() ?></strong>.
        </p>

        <p>
            Current computer: <?= $_SESSION['current_computer'] ?>
        </p>

        <p>
            Current connected address: <?php if (isset($_SESSION['connected_ipaddress'])) {
                echo $_SESSION['connected_ipaddress'];
            } ?>
        </p>

        <div class="well">
            <?= json_encode($pagehelper->getComputerSoftware(), JSON_PRETTY_PRINT) ?>
        </div>
    </div>
</div>