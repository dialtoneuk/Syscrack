<div class="row">
    <div class="col-sm-12">
        <?php
        if ( empty( $log ))
        {
        ?>

            <div class="panel panel-danger">
                <div class="panel-body">
                    Empty
                </div>
            </div>
            <?php
        }
        else
        {
            ?>
            <div class="well">
                <pre>
<textarea readonly id="log" name="log" style="width: 100%; height: 400px; resize: none; font-size: 14px; padding-left: 12px;">
<?php
foreach ($log as $key => $value) {
echo '[', $value['ipaddress'] . '] ' . strftime("%d-%m-%Y %H:%M:%S", $value['time']) . ' : ' . $value['message'] . "\n";
}
?>
</textarea>
                </pre>
            </div>
            <div class="btn-group-vertical" style="width: 100%">
                <?php

                if (isset($hideoptions)) {

                    if ($hideoptions == false) {

                        ?>

                        <button class="btn btn-danger" type="submit"
                                onclick="window.location.href = '/game/internet/<?= $ipaddress ?>/clear'">
                            <span class="glyphicon glyphicon-alert" aria-hidden="true"></span> Clear Log
                        </button>
                        <button class="btn btn-success" type="button"
                                onclick="window.location.href = '/game/internet/<?= $ipaddress ?>'">
                            <span class="glyphicon glyphicon-circle-arrow-down" aria-hidden="true"></span> Refresh Log
                        </button>
                        <?php
                    }
                } else {

                    ?>

                    <button class="btn btn-danger" type="submit">
                        <span class="glyphicon glyphicon-alert" aria-hidden="true"></span> Clear Log
                    </button>
                    <button class="btn btn-success" type="button"
                            onclick="window.location.href = '/game/internet/<?= $ipaddress ?>'">
                        <span class="glyphicon glyphicon-circle-arrow-down" aria-hidden="true"></span> Refresh Log
                    </button>
                    <?php
                }
                ?>
            </div>
            <?php
        }
        ?>
    </div>
</div>