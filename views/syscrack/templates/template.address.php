<?php

    use Framework\Application\Settings;

    if( isset( $computer ) == false )
    {

        $computer = new \Framework\Syscrack\Game\Computer();
    }
?>
<div class="panel <?php if( $computer->getComputerType( $value['computerid'] ) == Settings::getSetting('syscrack_npc_type') ){ echo 'panel-primary'; }
elseif( $computer->getComputerType( $value['computerid'] ) == Settings::getSetting('syscrack_vpc_type') ){ echo 'panel-success'; }else{ echo 'panel-default'; }?>">
    <div class="panel-heading">
        <span class="badge">address #<?=$key?></span> <?=$value['ipaddress']?> <span style="float: right;" class="badge"><?=$computer->getComputerType( $value['computerid'] )?></span>
    </div>
    <div class="panel-body">
        <button class="btn btn-default" style="width: 100%" type="button" data-toggle="collapse" data-target="#computer_<?=$value['computerid']?>" aria-expanded="false" aria-controls="computer_<?=$value['computerid']?>">
            View
        </button>
        <div class="collapse" id="computer_<?=$value['computerid']?>">
            <div class="panel panel-default" style="margin-top: 3.5%;">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="panel panel-primary">
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
                        <div class="col-lg-6">
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
                            <div style="margin-top: 16%" class="panel panel-default">
                                <div class="panel-heading">
                                    Hack Information
                                </div>
                                <div class="panel-body">
                                    Added <?=date('Y/m/d H:m:s', $value['timehacked'] )?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>