<?php

    use Framework\Syscrack\Game\Operations;

    if( isset( $processclass ) == false )
{

    $processclass = new Operations();
}

if( isset( $processid ) )
{

    $process = $processclass->getProcess( $processid );

    $data = json_decode( $process->data, true );

    ?>
    <div class="col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-body">
                <p>
                    <span class="glyphicon glyphicon-cog"></span> <?=$process->process?> at <a href="/game/internet/<?=$data['ipaddress']?>/"><?=$data['ipaddress']?></a> <span class="badge" style="float: right;"><?=date("F j, Y, g:i a", $process->timecompleted)?></span>
                </p>
                <div class="progress">
                    <div class="progress-bar" role="progressbar" id="progressbar<?=$processid?>" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"
                         style="<?php if( $processclass->canComplete( $processid ) ){ echo 'width: 100%';}else{echo 'width: 0%';}?>">
                        <p id="progresspercentage<?=$processid?>">
                            0%
                        </p>
                    </div>
                </div>
                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="heading<?=$processid?>">
                            <h4 class="panel-title">
                                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse<?=$processid?>" aria-expanded="true" aria-controls="collapse<?=$processid?>">
                                    Actions
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

        <?php

        if( $processclass->canComplete( $processid ) === false )
        {

            ?>
            <script>

                function move() {
                    var elem = document.getElementById("progressbar<?=$processid?>");
                    var precentage = document.getElementById("progresspercentage<?=$processid?>");
                    var width = 1;
                    var id = setInterval(frame, 10 * <?=( $process->timecompleted - time() )?>);
                    function frame() {
                        if (width >= 100) {
                            clearInterval(id);
                            <?php
                                if( isset( $auto ) )
                                {

                                    if( $auto == true )
                                    {

                                        echo 'window.location.href = "/processes/' . $processid . '/complete";';
                                    }
                                }

                                if( isset( $refresh ) )
                                {

                                    if( $refresh == true )
                                    {

                                        echo 'location.reload();';
                                    }
                                }
                            ?>
                        } else {
                            width++;
                            precentage.innerHTML = width + '%';
                            elem.style.width = width + '%';
                        }
                    }
                }

                move();
            </script>
            <?php
        }
        else
        {

            ?>
                <script>

                    var elem = document.getElementById("progressbar<?=$processid?>");
                    var precentage = document.getElementById("progresspercentage<?=$processid?>");

                    precentage.innerHTML = 100 + '%';
                    elem.style.width = 100 + '%';
                </script>
            <?php
        }
        ?>
    </div>
    <?php
}
?>