<?php

    if( isset( $computer ) == false )
    {

        $computer = new \Framework\Syscrack\Game\Computer();
    }

    $currentcomputer = $computer->getComputer( $computer->getCurrentUserComputer() );
?>
<div class="col-lg-4">
    <div class="panel panel-default" style="cursor: hand" onclick="window.location.href = '/game/computer/'">
        <div class="panel-body" style="padding-bottom: 0;">
            <p>
                <span class="glyphicon glyphicon-modal-window"></span> Desktop
            </p>
        </div>
    </div>
    <div class="panel panel-default" style="cursor: hand" onclick="window.location.href = '/game/computer/log'">
        <div class="panel-body" style="padding-bottom: 0;">
            <p>
                <span class="glyphicon glyphicon-paperclip"></span> View Log
            </p>
        </div>
    </div>
    <div class="panel panel-default" style="cursor: hand" onclick="window.location.href = '/game/computer/processes'">
        <div class="panel-body" style="padding-bottom: 0;">
            <p>
                <span class="glyphicon glyphicon-cog"></span> View Processes
            </p>
        </div>
    </div>
    <div class="panel panel-default" style="cursor: hand" onclick="window.location.href = '/game/computer/upgrade'">
        <div class="panel-body" style="padding-bottom: 0;">
            <p>
                <span class="glyphicon glyphicon-wrench"></span> Upgrade Computer
            </p>
        </div>
    </div>
</div>