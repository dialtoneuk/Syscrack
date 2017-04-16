<?php

    use Framework\Application\Settings;

    if( isset( $softwares ) == null )
    {

        $softwares = new \Framework\Syscrack\Game\Softwares();
    }

    if( isset( $computer ) == null )
    {

        $computer = new \Framework\Syscrack\Game\Computer();
    }

    if( isset( $internet ) == null )
    {

        $internet = new \Framework\Syscrack\Game\Internet();
    }
?>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                C:\\
            </div>

            <table class="table">
                <thead>
                <tr>
                    <th><span class="glyphicon glyphicon-question-sign"></span></th>
                    <th>Name</th>
                    <th>Level</th>
                    <th>Size</th>

                    <?php

                        if( isset( $hideoptions ) )
                        {

                            if( $hideoptions == false )
                            {

                                ?>

                                    <th>Options</th>
                                <?php
                            }
                        }
                    ?>
                </tr>
                </thead>

                <tbody>
                <?php

                $software = $computer->getComputerSoftware( $internet->getComputer( $ipaddress )->computerid );

                foreach( $software as $key=>$value )
                {

                    $softwareclass = $softwares->getSoftwareClassFromID( $value['softwareid'] );

                    $software = $softwares->getSoftware( $value['softwareid'] );

                    if( $softwares->softwareExists( $value['softwareid'] ) == false || $softwareclass == null )
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

                        <?php



                            if( isset( $hideoptions ) == false || $hideoptions == false )
                            {

                                ?>

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
                                <?php
                            }
                        ?>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>