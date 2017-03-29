<?php

    use Framework\Syscrack\Game\NPC;
    use Framework\Application\Settings;
    use Framework\Syscrack\Game\Internet;
    use Framework\Syscrack\Game\Utilities\PageHelper;
    use Framework\Syscrack\Game\Computer;
    use Framework\Syscrack\Game\Softwares;
    use Framework\Syscrack\Game\Log;

    $npc = new NPC();

    if( isset( $internet ) == false )
    {

        $interent = new Internet();
    }

    if( isset( $pagehelper ) == false )
    {

        $pagehelper = new PageHelper();
    }

    $computer = new Computer();

    $softwares = new Softwares();

    $log = new Log();
?>
<div class="col-md-8">
    <?php

        if( isset( $_GET['error'] ) )
            Flight::render('syscrack/templates/template.alert', array( 'message' => $_GET['error'] ) );
        elseif( isset( $_GET['success'] ) )
            Flight::render('syscrack/templates/template.alert', array( 'message' => 'Success', 'alert_type' => 'alert-success' ) );

        if( $internet->getComputerAddress( $computer->getCurrentUserComputer() ) == $ipaddress )
        {

            ?>

                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Notice
                    </div>

                    <div class="panel-body">
                        You are currently connected to yourself
                    </div>
                </div>
            <?php
        }

    ?>
    <form method="post" action="/game/internet/">
        <div class="input-group">
            <input type="text" class="form-control" id="ipaddress" name="ipaddress" placeholder="<?php if( isset( $ipaddress ) ){ echo $ipaddress; } else { echo $internet->getComputerAddress( \Framework\Application\Settings::getSetting('syscrack_whois_computer')); }?>">
            <span class="input-group-btn">
                <button class="btn btn-default" onclick="window.location.href = '/game/internet/' . $('#ipaddress').value()">Connect</button>
            </span>
        </div><!-- /input-group -->
    </form>
    <div class="panel panel-default" style="margin-top: 2.5%">
        <div class="panel-body">
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#log">Log</a></li>
                <li><a data-toggle="tab" href="#software">Software</a></li>
            </ul>

            <div class="tab-content">
                <div id="log" class="tab-pane fade in active" style="padding-top: 2.5%;">
                    <div class="row">
                        <div class="col-lg-12">
                            <?php

                            $connectedcomputer = $internet->getComputer( $ipaddress );

                            if( $log->hasLog( $connectedcomputer->computerid ) == false  )
                            {

                                ?>

                                <p>
                                    No Log Available
                                </p>
                                <?php
                            }
                            else
                            {

                                ?>
                                    <form method='post' action="/game/internet/<?=$ipaddress?>/log">
                                        <div class="well">
                                            <textarea id="log" name="log" style="width: 100%; height: 40%; resize: none; font-size: 14px; padding: 2.5%;"><?php $log = array_reverse( $log->getCurrentLog( $connectedcomputer->computerid ) ); foreach( $log as $key=>$value ){ echo '[' , $value['ipaddress'] . '] ' . strftime("%d-%m-%Y %H:%M:%S", $value['time']) . ' : ' . $value['message'] . "\n";}?></textarea>
                                        </div>
                                        <button style="width: 100%; margin-top: 2.5%;" class="btn btn-danger" type="submit">
                                            <span class="glyphicon glyphicon-alert" aria-hidden="true"></span> Clear Log
                                        </button>
                                        <button style="width: 100%; margin-top: 2.5%;" class="btn btn-success" type="button" onclick="window.location.href = '/game/internet/<?=$ipaddress?>'">
                                            <span class="glyphicon glyphicon-circle-arrow-down" aria-hidden="true"></span> Refresh Log
                                        </button>
                                    </form>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div id="software" class="tab-pane fade" style="padding-top: 2.5%;">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    Softwares
                                </div>

                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th><span class="glyphicon glyphicon-question-sign"></span></th>
                                            <th>Name</th>
                                            <th>Level</th>
                                            <th>Size</th>
                                            <th>Options</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php

                                            $software = $computer->getComputerSoftware( $internet->getComputer( $ipaddress )->computerid );

                                            foreach( $software as $key=>$value )
                                            {

                                                $softwareclass = $softwares->getSoftwareClassFromID( $value['softwareid'] );

                                                $software = $softwares->getSoftware( $value['softwareid'] );

                                                if( $softwares->softwareExists( $value['softwareid'] ) == false )
                                                {

                                                    continue;
                                                }
                                            ?>
                                                <tr>
                                                    <td>
                                                        <?php

                                                            if( $value['type'] == Settings::getSetting('syscrack_virus_type') )
                                                            {

                                                                ?>
                                                                    <span class="glyphicon glyphicon-cog"></span>
                                                                <?php
                                                            }elseif( $value['type'] == Settings::getSetting('syscrack_cracker_type') )
                                                            {

                                                                ?>
                                                                    <span class="glyphicon glyphicon-lock"></span>
                                                                <?php
                                                            }elseif( $value['type'] == Settings::getSetting('syscrack_hasher_type') )
                                                            {

                                                                ?>
                                                                    <span class="glyphicon glyphicon-briefcase"></span>
                                                                <?php
                                                            }elseif( $value['type'] == Settings::getSetting('syscrack_text_type') )
                                                            {

                                                                ?>
                                                                    <span class="glyphicon glyphicon-paperclip"></span>
                                                                <?php
                                                            }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php

                                                            if( $software->installed )
                                                            {

                                                                ?>
                                                                    <strong><?=$software->softwarename . $softwareclass->configuration()['extension']?></strong>
                                                                <?php
                                                            }
                                                            else
                                                            {

                                                                ?>
                                                                    <p style="color: lightgray;">
                                                                        <?=$software->softwarename . $softwareclass->configuration()['extension']?>
                                                                    </p>
                                                                <?php
                                                            }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php

                                                            if( $software->level >= Settings::getSetting('syscrack_level_expert') )
                                                            {

                                                                ?>
                                                                    <strong style="color: palevioletred;">
                                                                        <?=$software->level?>
                                                                    </strong>
                                                                <?php
                                                            }elseif( $software->level >= Settings::getSetting('syscrack_level_advanced') && $software->level < Settings::getSetting('syscrack_level_expert') )
                                                            {

                                                                ?>
                                                                    <strong>
                                                                        <?=$software->level?>
                                                                    </strong>
                                                                <?php
                                                            }else
                                                            {

                                                                ?>
                                                                    <p>
                                                                        <?=$software->level?>
                                                                    </p>
                                                                <?php
                                                            }

                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?=$software->size?>MB
                                                    </td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                Operations <span class="caret"></span>
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <li><a href="/game/internet/<?=$ipaddress?>/download/<?=$value['softwareid']?>">Download</a></li>

                                                                <?php
                                                                    if( $software->installed )
                                                                    {

                                                                        ?>

                                                                            <li><a href="/game/internet/<?=$ipaddress?>/uninstall/<?=$value['softwareid']?>">Uninstall</a></li>
                                                                            <li><a href="/game/internet/<?=$ipaddress?>/execute/<?=$value['softwareid']?>">Execute</a></li>
                                                                        <?php
                                                                    }
                                                                    else
                                                                    {

                                                                        ?>

                                                                            <li><a href="/game/internet/<?=$ipaddress?>/install/<?=$value['softwareid']?>">Install</a></li>
                                                                        <?php
                                                                    }

                                                                    if( $softwares->hasData( $value['softwareid'] ) )
                                                                    {

                                                                        ?>

                                                                            <li><a href="/game/internet/<?=$ipaddress?>/view/<?=$value['softwareid']?>">View</a></li>
                                                                        <?php
                                                                    }
                                                                ?>
                                                                <li><a href="/game/internet/<?=$ipaddress?>/delete/<?=$value['softwareid']?>">Delete</a></li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php
                                            }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <?php echo strtoupper( $pagehelper->getComputerType( $internet->getComputer( $ipaddress )->computerid ) ); echo ' <small>' . date('d-M-y H:m:s') . '</small>';?>
        </div>
    </div>
</div>