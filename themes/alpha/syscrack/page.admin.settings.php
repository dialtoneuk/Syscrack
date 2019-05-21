<?php
    use Framework\Application\Render;
?>
<html>
    <?php Render::view('syscrack/templates/template.header', array('pagetitle' => 'Syscrack | Admin'));?>
    <body>
        <div class="container">
	        <?php
		        Render::view('syscrack/templates/template.navigation');
		        Render::view('syscrack/templates/template.errors');
	        ?>
            <div class="row">
                <?php Render::view('syscrack/templates/template.admin.options'); ?>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel panel-info">
                                <div class="panel-body">
                                    Edit these settings with extreme caution and make sure to follow the guide on our
                                    github wiki. You can set a setting to be an array by using json. The setting will
                                    be updated if the json you have supplied is valid.
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                        if( empty( $admin_settings ) )
                        {

                            ?>
                                <div class="panel panel-warning">
                                    <div class="panel-body">
                                        No settings found.
                                    </div>
                                </div>
                            <?php
                        }
                        else
                        {

                            foreach( $admin_settings as $key=>$value )
                            {

                                ?>
                                    <div class="row">
                                        <form method="post">
                                            <input type="hidden" name="setting" value="<?=$key?>">
                                            <div class="col-lg-12">
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        <h3><?=$key?></h3>
                                                        <div class="input-group input-group-lg" style="margin-top: 1.5%;">
                                                            <span class="input-group-addon" id="sizing-addon1"><span class="glyphicon glyphicon-wrench"></span></span>

                                                            <?php
                                                                if( is_numeric( $value ) )
                                                                {
                                                                    ?>  <input type="number" name="value" class="form-control" value="<?=@$value?>" aria-describedby="sizing-addon1"> <?php
                                                                }
                                                                elseif( is_array( $value ) )
                                                                {
                                                                    ?> <input type="text" name="value" class="form-control" value="<?=@htmlspecialchars( json_encode( $value ) )?>" aria-describedby="sizing-addon1"> <?php
                                                                }
                                                                else
                                                                {
                                                                    ?>  <input type="text" name="value" class="form-control" value="<?=@$value?>" aria-describedby="sizing-addon1"> <?php
                                                                }
                                                            ?>

                                                        </div>
                                                        <button style="width: 100%; margin-top: 2.5%;" class="btn btn-default" name="action"
                                                                value="transfer" type="submit">
                                                            <span class="glyphicon glyphicon-save" aria-hidden="true"></span> Save
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                <?php
                            }
                        }
                    ?>
                </div>
            </div>
            <?php Render::view('syscrack/templates/template.footer', array('breadcrumb' => true));?>
        </div>
    </body>
</html>
