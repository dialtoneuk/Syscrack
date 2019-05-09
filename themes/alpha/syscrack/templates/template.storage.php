<div class="row">
    <div class="col-lg-12">
        <?php
            if( isset( $hardwares["harddrive"] ) == false )
            {
                ?>
                    <div class="panel panel-danger">
                        <div class="panel-body">
                            This computer appears to lack a hard drive.
                        </div>
                    </div>
                <?php
            }
            else
            {
                $space = $hardwares["harddrive"]["value"];

                if( isset( $usedspace ) == false )
                    $usedspace = 0;

                $freespace = $space - $usedspace;
                ?>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            Drive information for <?=@$ipaddress?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="panel panel-danger">
                                <div class="panel-body text-center">
                                    <h2><small>Used Space</small> <?=$usedspace?>mbs</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="panel panel-success">
                                <div class="panel-body text-center">
                                    <h2><small>Free Space</small> <?=$freespace?>mbs</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="panel panel-default">
                                <div class="panel-body text-center">
                                    <h2><small>Total Space</small> <?=$space?>mbs</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
            }
        ?>
    </div>
</div>