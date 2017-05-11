<?php

    use Framework\Application\Settings;

    if( isset( $routes ) == false )
    {

        Flight::redirect( Settings::getSetting('controller_index_root') . Settings::getSetting('developer_page') );
    }
?>
<html lang="en">

    <?php

        Flight::render('developer/templates/template.header', array( 'pagetitle' => 'Page Viewer'));
    ?>
    <body>
        <div class="container">

            <?php

                Flight::render('developer/templates/template.navigation');
            ?>
            <div class="row">
                <div class="col-md-6">
                    <h5 style="color: #ababab" class="text-uppercase">
                        Routes
                    </h5>
                    <p class="lead">
                        Here you can see a visual display of how the pages are currently routed. You should use this
                        page as a visual aid when adding new pages as well as testing their functionality.
                    </p>

                    <p>
                        While the framework is unedited, there should be at least 4 default pages.
                        It is suggested that <strong>you do not delete these files</strong>. Try and learn from example
                        and try and understand how the developer area functions via looking at its mapping.
                    </p>
                    <h5 style="color: #ababab" class="text-uppercase">
                        Information
                    </h5>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <h5 style="color: #ababab" class="text-uppercase">
                                Controller Namespace
                            </h5>
                            <p>
                                <?=Settings::getSetting('controller_namespace')?>
                            </p>
                            <h5 style="color: #ababab" class="text-uppercase">
                                Controller Page Folder
                            </h5>
                            <p>
                                <?=Settings::getSetting('controller_page_folder')?>
                            </p>
                            <h5 style="color: #ababab" class="text-uppercase">
                                Number Of Routes
                            </h5>
                            <p>
                                <?=count( $routes )?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <h5 style="color: #ababab" class="text-uppercase">
                        Mapping
                    </h5>
                    <?php

                        foreach( $routes as $route=>$mapping )
                        {

                            ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h3 class="panel-title"><?=$route?> <span class="badge" style="float: right;"><a style="color: white;" href="/<?=strtolower($route)?>/">Visit Page</a></span></h3>
                                            </div>

                                            <div class="panel-body">
                                                <div class="well well-sm">
                                                     <?=htmlspecialchars( json_encode( $mapping , JSON_PRETTY_PRINT ), ENT_QUOTES, 'UTF-8')?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php
                        }
                    ?>
                </div>
            </div>

            <?php

                Flight::render('developer/templates/template.footer', array( 'breadcrumb' => true ));
            ?>
        </div>
    </body>
</html>
