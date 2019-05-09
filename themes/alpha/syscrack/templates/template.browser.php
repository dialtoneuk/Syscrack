<?php
use Framework\Application\Render;
?>
<div class="col-md-8">
    <form method="post" action="/game/internet/">
        <div class="input-group">
            <input type="text" class="form-control" id="ipaddress" name="ipaddress"
                   placeholder="<?php if (isset($ipaddress)) {
                       echo $ipaddress;
                   }?>">
            <span class="input-group-btn">
                <button class="btn btn-default"
                        onclick="window.location.href = '/game/internet/' . $('#ipaddress').value()">Connect</button>
            </span>
        </div><!-- /input-group -->
        <div class="panel panel-default" style="margin-top: 2.5%">
            <?php
                if( isset( $metadata ) && empty( $metadata->custom ) == false || isset( $metadata->custom["name"] ) )
                {
                    ?>
                        <div class="panel-heading">
                            <?=@$metadata->custom["name"]?>
                        </div>
                    <?php
                }

            ?>
            <div class="panel-body">
                <?php
                    if( isset( $metadata ) == false && empty( $metadata->custom ) || isset( $metadata->custom["browserpage"] ) == false )
                    {

                        ?>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="panel panel-info">
                                    <div class="panel-body">
                                        Successfully connected to <?=$ipaddress?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    else
                        Render::view("../../" . $settings["browser_pages_root"] . $metadata->custom["browserpage"] );
                ?>
            </div>
            <div class="panel-footer">
                <?php echo $computer->type;
                echo ' <small>' . date('d-M-y H:m:s') . '</small>'; ?>
            </div>
        </div>
    </form>
</div>