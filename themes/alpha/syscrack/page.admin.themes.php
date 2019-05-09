<?php
use Framework\Application\Render;
?>
<html>

<?php
Render::view('syscrack/templates/template.header', array('pagetitle' => 'Syscrack | Admin'));
?>
<body>
<div class="container">

    <?php

    Render::view('syscrack/templates/template.navigation');
    ?>
    <div class="row">
        <div class="col-sm-12">
            <?php
                if (isset($_GET['error']))
                    Render::view('syscrack/templates/template.alert', array('message' => $_GET['error']));
                elseif (isset($_GET['success']))
                    Render::view('syscrack/templates/template.alert', array('message' => $settings['alert_success_message'], 'alert_type' => 'alert-success'));
            ?>
        </div>
    </div>
    <form method="post">
        <div class="row">

            <?php

            Render::view('syscrack/templates/template.admin.options');
            ?>
            <div class="col-sm-8">
                <div class="row">
                    <div class="col-sm-12">
                        <h5 style="color: #ababab" class="text-uppercase">
                            Themes
                        </h5>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="panel panel-info">
                            <div class="panel-body">
                                Please look at our github wiki for more information on installing themes and creating
                                your own.
                            </div>
                        </div>
                    </div>
                </div>
                <?php

                    if( empty( $themes ) )
                    {
                        ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="panel panel-default">
                                        <div class="panel-body">
                                            Sorry no themes available!
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php
                    }
                    else
                    {

                        foreach( $themes as $folder=>$theme )
                        {

                            ?>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="thumbnail">
                                            <div class="caption">
                                                <h1><?=$theme['name']?>  <small>version <?=$theme["version"]?></small></h1>
                                                <h3 class="small">Created by <a href="<?=$theme["website"]?>"><?=$theme["author"]?></a></h3>
                                                <p><?=$theme['description']?></p>
                                                <h3>Data</h3>
                                                <div class="well">
                                                    <?=print_r($theme["data"])?>
                                                </div>
                                                <form method="post">
                                                <?php
                                                    if( $folder == $settings["render_folder"] )
                                                    {
                                                        ?>
                                                            <div class="row">
                                                                <div class="col-lg-12">
                                                                    <div class="panel panel-warning">
                                                                        <div class="panel-body">
                                                                            You already have this theme selected.
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php
                                                    }
                                                    else
                                                    {
                                                        ?>

                                                                <button style="width: 100%; margin-top: 2.5%;"
                                                                        class="btn btn-default" type="submit">
                                                                    <span class="glyphicon glyphicon-check"
                                                                          aria-hidden="true"></span> Switch
                                                                </button>
                                                                <input type="hidden" name="theme" value="<?=$folder?>">
                                                        <?php
                                                    }
                                                ?>
                                                </form>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php
                        }
                    }
                ?>
            </div>
        </div>
    </form>

    <?php

    Render::view('syscrack/templates/template.footer', array('breadcrumb' => true));
    ?>
</div>
</body>
</html>
