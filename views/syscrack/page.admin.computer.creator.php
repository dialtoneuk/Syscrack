<?php

    use Framework\Application\Container;
    use Framework\Application\Settings;
    use Framework\Syscrack\Game\Internet;
    use Framework\Syscrack\Game\Utilities\PageHelper;
    use Framework\Syscrack\User;

    $session = Container::getObject('session');

    if( $session->isLoggedIn() )
    {

        $session->updateLastAction();
    }

    if( isset( $user ) == false )
    {

        $user = new User();
    }

    if( isset( $pagehelper ) == false )
    {

        $pagehelper = new PageHelper();
    }

    if( isset( $internet ) == false )
    {

        $internet = new Internet();
    }
?>
<html>
    <?php

        Flight::render('syscrack/templates/template.header', array('pagetitle' => 'Syscrack | Admin'));
    ?>
    <body>
        <div class="container">

            <?php

                Flight::render('syscrack/templates/template.navigation');
            ?>
            <div class="row">
                <div class="col-lg-12">
                    <?php

                        if( isset( $_GET['error'] ) )
                            Flight::render('syscrack/templates/template.alert', array( 'message' => $_GET['error'] ) );
                        elseif( isset( $_GET['success'] ) )
                            Flight::render('syscrack/templates/template.alert', array( 'message' => 'Success', 'alert_type' => 'alert-success' ) );
                    ?>
                </div>
            </div>
            <div class="row">

                <?php

                    Flight::render('syscrack/templates/template.admin.options');
                ?>

                <div class="col-sm-8">
                    <div class="panel panel-default">
                        <div class="panel-body" style="padding: 10px 15px;">
                           <h4>
                               Computer Creator
                           </h4>
                        </div>
                    </div>
                    <form class="form-group" method="post">

                        <?php
                            $schema = Settings::getSetting('syscrack_example_schema');
                        ?>
                        <div class="row">
                                <div class="col-lg-4">

                                    <?php
                                        Flight::render('syscrack/templates/template.form', array('form_elements' => [
                                            [
                                                'type'          => 'text',
                                                'name'          => 'userid',
                                                'placeholder'   => Settings::getSetting('syscrack_master_user'),
                                                'icon'          => 'glyphicon-user'
                                            ],
                                            [
                                                'type'          => 'text',
                                                'name'          => 'ipaddress',
                                                'icon'          => 'glyphicon-globe',
                                                'placeholder'   => '1.2.3.4',
                                                'value'         => $internet->getIP()
                                            ],
                                            [
                                                'type'          => 'text',
                                                'name'          => 'type',
                                                'icon'          => 'glyphicon-tag',
                                                'placeholder'   => 'npc'
                                            ]
                                        ],'remove_submit' => true, 'remove_form' => true ));

                                        Flight::render('syscrack/templates/template.form', array('form_elements' => [
                                            [
                                                'type'          => 'checkbox',
                                                'name'          => 'schema',
                                            ],
                                            [
                                                'type'          => 'text',
                                                'name'          => 'name',
                                                'placeholder'   => 'Whois',
                                                'icon'          => 'glyphicon-text-size',
                                                'disabled'      => true,
                                            ],
                                            [
                                                'type'          => 'text',
                                                'name'          => 'page',
                                                'icon'          => 'glyphicon-book',
                                                'disabled'      => true,
                                                'placeholder'   => 'npc.default'
                                            ],
                                            [
                                                'type'          => 'checkbox',
                                                'name'          => 'riddle',
                                                'disabled'      => true,
                                            ],
                                            [
                                                'type'          => 'text',
                                                'name'          => 'riddleaddress',
                                                'icon'          => 'glyphicon-question-sign',
                                                'placeholder'   => '1.2.3.5',
                                                'disabled'      => true,
                                            ]
                                        ],'remove_submit' => true, 'remove_form' => true ));
                                    ?>
                                </div>
                                <div class="col-lg-8">
                                    <?php
                                        Flight::render('syscrack/templates/template.form', array('form_elements' => [
                                            [
                                                'type'  => 'textarea',
                                                'name'  => 'softwares',
                                                'value' => json_encode( $schema['softwares'], JSON_PRETTY_PRINT ),
                                                'resizeable' => 'vertical'
                                            ],
                                            [
                                                'type'  => 'textarea',
                                                'name'  => 'hardwares',
                                                'value' => json_encode( $schema['hardwares'], JSON_PRETTY_PRINT ),
                                                'resizeable' => 'vertical'
                                            ]
                                        ],'remove_submit' => true, 'remove_form' => true ));
                                    ?>
                                </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="btn-group btn-group-justified" role="group" aria-label="Submit" style="margin-top: 2.5%;">
                                    <div class="btn-group" role="group">
                                        <button type="submit" class="btn btn-default">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <?php

                Flight::render('syscrack/templates/template.footer', array('breadcrumb' => true ) );
            ?>
        </div>
    </body>
    <footer>
        <script>
            $( "#form-schema" ).change(function() {
                var ckb = $("#form-schema").is(':checked');

                if( ckb == true )
                {

                    $("#form-name").prop('disabled', false );
                    $("#form-page").prop('disabled', false );
                    $("#form-riddle").prop('disabled', false );
                }
                else
                {

                    $("#form-name").prop('disabled', true );
                    $("#form-page").prop('disabled', true );
                    $("#form-riddle").prop('disabled', true );

                    if( $('#form-riddleaddress').is(':disabled') == false )
                    {

                        $("#form-riddleaddress").prop('disabled', true );
                    }

                    if( $("#form-riddle").is(':checked') )
                    {

                        $('#form-riddle').attr('checked', false); // Unchecks it
                    }
                }
            });

            $( "#form-riddle" ).change( function() {
                var ckb = $("#form-riddle").is(':checked');

                if( ckb == true )
                {

                    $("#form-riddleaddress").prop('disabled', false );
                }
                else
                {

                    $("#form-riddleaddress").prop('disabled', true );
                }
            })
        </script>
    </footer>
</html>
