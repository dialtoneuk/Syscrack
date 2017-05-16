<?php

    use Framework\Application\Settings;
    use Framework\Views\Pages\Computer;

    if( isset( $computer ) == false )
    {

        $computer = new Computer();
    }

    $currentcomputer = $computer->getComputer( $computer->getCurrentUserComputer() );
?>
<div class="col-md-4">
    <div class="panel panel-default" style="cursor: pointer;" onclick="window.location.href = '/computer/'">
        <div class="panel-body" style="padding-bottom: 0;">
            <p>
                <span class="glyphicon glyphicon-modal-window"></span> Desktop
            </p>
        </div>
    </div>
    <div class="panel panel-default" style="cursor: pointer;" onclick="window.location.href = '/computer/log'">
        <div class="panel-body" style="padding-bottom: 0;">
            <p>
                <span class="glyphicon glyphicon-paperclip"></span> Log
            </p>
        </div>
    </div>
    <div class="panel panel-default" style="cursor: pointer;" onclick="window.location.href = '/computer/processes'">
        <div class="panel-body" style="padding-bottom: 0;">
            <p>
                <span class="glyphicon glyphicon-cog"></span> Processes
            </p>
        </div>
    </div>
    <div class="panel panel-default" style="cursor: pointer;" onclick="window.location.href = '/computer/hardware'">
        <div class="panel-body" style="padding-bottom: 0;">
            <p>
                <span class="glyphicon glyphicon-wrench"></span> Hardware
            </p>
        </div>
    </div>

    <?php

        if( $computer->hasType( $currentcomputer->computerid, Settings::getSetting('syscrack_software_collector_type'), true ) )
        {

            ?>
                <div class="panel panel-default" style="cursor: pointer;" onclick="window.location.href = '/computer/collect'">
                    <div class="panel-body" style="padding-bottom: 0;">
                        <p>
                            <span class="glyphicon glyphicon-gbp"></span> Collect
                        </p>
                    </div>
                </div>
            <?php
        }
    ?>
</div>