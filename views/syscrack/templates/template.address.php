<?php

    if( isset( $computer ) == false )
    {

        $computer = new \Framework\Syscrack\Game\Computer();
    }
?>
<div class="panel panel-success">
    <div class="panel-heading">
        <span class="badge">address #<?=$key?></span> <?=$value['ipaddress']?>
    </div>
    <div class="panel-body">
        <button class="btn btn-default" style="width: 100%" type="button" data-toggle="collapse" data-target="#collapse<?=$value['computerid']?>" aria-expanded="false" aria-controls="collapse<?=$value['computerid']?>">
            View
        </button>
        <div class="collapse" id="collapse<?=$value['computerid']?>">
            <div class="panel panel-default" style="margin-top: 1%;">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-8">
                            <p class="small">
                                Added <?=date('Y/m/d H:m:s', $value['timehacked'] )?>
                            </p>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    Hardware
                                </div>
                                <div class="panel-body">
                                    <div class="well">
                                        <pre><?=json_encode( $computer->getComputerHardware( $value['computerid']), true )?></pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <p class="small">
                                http:\\
                            </p>
                            <div class="btn-group btn-group-justified" role="group" aria-label="...">
                                <div class="btn-group" role="group" onclick="window.location.href = '/game/internet/<?=$value['ipaddress']?>/'">
                                    <button type="button" class="btn btn-default">Goto</button>
                                </div>
                                <div class="btn-group" role="group" onclick="window.location.href = '/game/internet/<?=$value['ipaddress']?>/login'">
                                    <button type="button" class="btn btn-default">Login</button>
                                </div>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-danger">Delete</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>