<?php

    use Framework\Syscrack\Game\Operations;

    if( isset( $processclass ) == false )
    {

        $processclass = new Operations();
    }

    if( isset( $processid ) )
    {

        $process = $processclass->getProcess( $processid );

        if( $processclass->canComplete( $processid ) )
        {

            if( isset( $auto ) == true )
            {

                Flight::redirect('/processes/' . $processid . '/complete');
            }
        }

        $data = json_decode( $process->data, true );

        ?>
            <div class="col-lg-12">
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <p>
                            <span class="glyphicon glyphicon-cog"></span> <?=$process->process?> at <a href="/game/internet/<?=$data['ipaddress']?>/"><?=$data['ipaddress']?></a> <span class="badge" style="float: right;"><?=date("F j, Y, g:i a", $process->timecompleted)?></span>
                        </p>
                        <div style="height: 5%; margin-bottom: 1.5%;">
                            <div id="progressbar<?=$processid?>"></div>
                        </div>
                        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="heading<?=$processid?>">
                                    <h4 class="panel-title">
                                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse<?=$processid?>" aria-expanded="true" aria-controls="collapse<?=$processid?>">
                                            Actions <span class="badge" style="float: right">Click to expand</span>
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapse<?=$processid?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="process<?=$processid?>">
                                    <div class="panel-body" style="padding-top: 0%;">
                                        <button style="width: 100%; margin-top: 2.5%;" class="btn btn-danger" type="button" onclick="window.location.href = '/processes/<?=$processid?>/delete'">
                                            <span class="glyphicon glyphicon-alert" aria-hidden="true"></span> Delete
                                        </button>

                                        <?php

                                            if( $processclass->canComplete( $processid ) )
                                            {

                                                ?>
                                                    <button style="width: 100%; margin-top: 2.5%;" class="btn btn-success" type="button" onclick="window.location.href = '/processes/<?=$processid?>/complete'">
                                                        <span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span> Complete
                                                    </button>
                                                <?php
                                            }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <script>

                    <?php

                        if( $processclass->canComplete( $processid ) )
                        {

                            $duration = 1;
                        }
                        else
                        {

                            $duration = ( $process->timecompleted - time() );
                        }
                    ?>

                    var line = new ProgressBar.Line('#progressbar<?=$processid?>',{color: '#FCB03C',
                        duration: <?=1000 * $duration?>,
                        easing: 'easeIn'
                    });

                    line.animate(1);

                    function onComplete()
                    {

                        <?php

                            if( isset( $auto ) )
                            {

                                if( $auto == true )
                                {

                                ?>
                                    window.location.href = '/processes/<?=$processid?>/complete';
                                <?php
                                }
                            }
                            else
                        ?>

                        clearTimeout( interval );
                    }

                    var interval = setInterval( onComplete, <?=1000 * $duration?> );

                    <?php
                        if( $processclass->canComplete( $processid ) )
                        {

                            if( isset( $auto ) )
                            {

                                if( $auto == true )
                                {

                                    ?>
                                        window.location.href = '/processes/<?=$processid?>/complete';
                                    <?php
                                }
                            }
                        }
                    ?>
                </script>
            </div>
        <?php
    }
    ?>