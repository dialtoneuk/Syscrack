<?php

    if( isset( $internet ) == false )
    {

        $internet = new \Framework\Syscrack\Game\Internet();
    }

    if( isset( $log ) == false )
    {

        $log = new \Framework\Syscrack\Game\Log();
    }
?>

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

                <?php

                    if( isset( $hideoptions ) )
                    {

                        if( $hideoptions == false )
                        {

                            ?>

                                <button style="width: 100%; margin-top: 2.5%;" class="btn btn-danger" type="submit">
                                    <span class="glyphicon glyphicon-alert" aria-hidden="true"></span> Clear Log
                                </button>
                                <button style="width: 100%; margin-top: 2.5%;" class="btn btn-success" type="button" onclick="window.location.href = '/game/internet/<?=$ipaddress?>'">
                                    <span class="glyphicon glyphicon-circle-arrow-down" aria-hidden="true"></span> Refresh Log
                                </button>
                            <?php
                        }
                    }
                    else
                    {

                        ?>

                            <button style="width: 100%; margin-top: 2.5%;" class="btn btn-danger" type="submit">
                                <span class="glyphicon glyphicon-alert" aria-hidden="true"></span> Clear Log
                            </button>
                            <button style="width: 100%; margin-top: 2.5%;" class="btn btn-success" type="button" onclick="window.location.href = '/game/internet/<?=$ipaddress?>'">
                                <span class="glyphicon glyphicon-circle-arrow-down" aria-hidden="true"></span> Refresh Log
                            </button>
                        <?php
                    }
                ?>
            </form>
            <?php
        }
        ?>
    </div>
</div>