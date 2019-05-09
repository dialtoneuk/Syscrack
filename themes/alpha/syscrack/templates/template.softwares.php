<div class="row">
    <div class="col-sm-12">
        <?php
            if( empty( $softwares ) )
            {
                ?>
                    <div class="panel panel-warning">
                        <div class="panel-body">
                            No software found, is this computer blank?
                        </div>
                    </div>
                <?php
            }
            else
            {
                ?>
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

                                if (isset($hideoptions)) {

                                    if ($hideoptions == false) {

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
                            foreach ( $softwares as $software ) {

                                ?>
                                <tr>
                                    <td data-toggle="tooltip" data-placement="auto" title="<?=$software->type ?>"
                                        style="padding-top: 2.5%;">
                                        <?php

                                        if (isset( $icons[ $software->type ] )) {

                                            ?>
                                            <span class="glyphicon <?=$icons[ $software->type ]?>"></span>
                                            <?php
                                        } else {

                                            ?>
                                            <span class="glyphicon glyphicon-question-sign"></span>
                                            <?php
                                        }
                                        ?>
                                    </td>
                                    <td style="padding-top: 2.25%;">
                                        <?php

                                        if ($software->installed) {

                                            if ($software->userid == $user->userid ) {

                                                ?>
                                                <strong><u><?= $software->softwarename . @$extensions[ $software->type ] ?></u></strong>
                                                <?php
                                            } else {

                                                ?>
                                                <strong><?= $software->softwarename . @$extensions[ $software->type ] ?></strong>
                                                <?php
                                            }
                                        } else {

                                            if ($software->userid == $user->userid ) {

                                                ?>
                                                <span style="color: grey"><u><?= $software->softwarename . @$extensions[ $software->type ] ?></u></span>
                                                <?php
                                            } else {

                                                ?>
                                                <span style="color: grey"><?= $software->softwarename . @$extensions[ $software->type ] ?></span>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td style="padding-top: 2.25%;">
                                        <?php

                                        if ($software->level >= $settings['syscrack_software_level_godlike']) {

                                            ?>
                                            <strong style="color: rebeccapurple;">
                                                <?= $software->level ?>
                                            </strong>
                                            <?php
                                        } elseif ($software->level >= $settings['syscrack_software_level_expert']) {

                                            ?>
                                            <strong style="color: limegreen;">
                                                <?= $software->level ?>
                                            </strong>
                                            <?php
                                        } elseif ($software->level >= $settings['syscrack_software_level_advanced']) {

                                            ?>
                                            <strong style="color: indianred;">
                                                <?= $software->level ?>
                                            </strong>
                                            <?php
                                        } else {

                                            ?>
                                            <p>
                                                <?= $software->level ?>
                                            </p>
                                            <?php
                                        }
                                        ?>
                                    </td>
                                    <td style="padding-top: 2.25%;">
                                        <?= $software->size ?>MB
                                    </td>
                                    <?php

                                    if (isset($hideoptions) == false || $hideoptions == false) {

                                        ?>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                                        aria-haspopup="true" aria-expanded="false">
                                                    <span class="glyphicon glyphicon-cog"></span>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <?php
                                                    foreach( $tools_software as $tool )
                                                        if( @$local )
                                                        {
                                                            ?>
                                                            <li>
                                                                <a href="/computer/actions/<?=$tool['action']?>/<?=$software->softwareid?>"><?=@$tool['description']?></a>
                                                            </li>
                                                            <?php
                                                        }
                                                        else
                                                        {
                                                            ?>
                                                            <li>
                                                                <a href="/game/internet/<?=$ipaddress?>/<?= $tool['action'] ?>/<?=$software->softwareid?>"><?=@$tool['description']?></a>
                                                            </li>
                                                            <?php
                                                        }
                                                    ?>
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
                <?php
            }
        ?>

    </div>
</div>