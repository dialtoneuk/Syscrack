<?php

use Framework\Application\Container;
use Framework\Application\Render;
use Framework\Application\Settings;
use Framework\Syscrack\Game\Internet;
use Framework\Syscrack\Game\Utilities\PageHelper;
use Framework\Syscrack\User;

$session = Container::getObject('session');

if ($session->isLoggedIn()) {

    $session->updateLastAction();
}

if (isset($user) == false) {

    $user = new User();
}

if (isset($pagehelper) == false) {

    $pagehelper = new PageHelper();
}

if (isset($internet) == false) {

    $internet = new Internet();
}
?>
<html>
<?php

Render::view('syscrack/templates/template.header', array('pagetitle' => 'Syscrack | Admin | Computer Creator'));
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
                Render::view('syscrack/templates/template.alert', array('message' => Settings::getSetting('alert_success_message'), 'alert_type' => 'alert-success'));
            ?>
        </div>
    </div>
    <div class="row">

        <?php

        Render::view('syscrack/templates/template.admin.options');
        ?>
        <div class="col-sm-8">
            <h5 style="color: #ababab" class="text-uppercase">
                Computer Creator
            </h5>
            <form class="form-group" method="post">

                <?php
                $schema = Settings::getSetting('syscrack_example_schema');
                ?>
                <div class="row">
                    <div class="col-sm-4">

                        <?php
                        Render::view('syscrack/templates/template.form', array('form_elements' => [
                            [
                                'type' => 'number',
                                'name' => 'userid',
                                'placeholder' => Settings::getSetting('syscrack_master_user'),
                                'icon' => 'glyphicon-user'
                            ],
                            [
                                'type' => 'text',
                                'name' => 'ipaddress',
                                'icon' => 'glyphicon-globe',
                                'placeholder' => '1.2.3.4',
                                'value' => $internet->getIP()
                            ],
                            [
                                'type' => 'text',
                                'name' => 'type',
                                'icon' => 'glyphicon-tag',
                                'placeholder' => 'npc'
                            ]
                        ], 'remove_submit' => true, 'remove_form' => true));

                        Render::view('syscrack/templates/template.form', array('form_elements' => [
                            [
                                'type' => 'checkbox',
                                'name' => 'schema',
                            ],
                            [
                                'type' => 'text',
                                'name' => 'name',
                                'placeholder' => 'Whois',
                                'icon' => 'glyphicon-text-size',
                                'disabled' => true,
                            ],
                            [
                                'type' => 'text',
                                'name' => 'page',
                                'icon' => 'glyphicon-book',
                                'disabled' => true,
                                'placeholder' => 'schema.default'
                            ],
                            [
                                'type' => 'checkbox',
                                'name' => 'riddle',
                                'disabled' => true,
                            ],
                            [
                                'type' => 'number',
                                'name' => 'riddleid',
                                'icon' => 'glyphicon-question-sign',
                                'placeholder' => '1',
                                'disabled' => true,
                            ],
                            [
                                'type' => 'text',
                                'name' => 'riddlecomputer',
                                'icon' => 'glyphicon-question-sign',
                                'placeholder' => '1',
                                'disabled' => true,
                            ]
                        ], 'remove_submit' => true, 'remove_form' => true));
                        ?>
                    </div>
                    <div class="col-sm-8">
                        <?php
                        Render::view('syscrack/templates/template.form', array('form_elements' => [
                            [
                                'type' => 'textarea',
                                'name' => 'software',
                                'value' => json_encode([], JSON_PRETTY_PRINT),
                                'resizeable' => 'vertical'
                            ],
                            [
                                'type' => 'textarea',
                                'name' => 'hardware',
                                'value' => json_encode($schema['hardwares'], JSON_PRETTY_PRINT),
                                'resizeable' => 'vertical'
                            ]
                        ], 'remove_submit' => true, 'remove_form' => true));
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="btn-group btn-group-justified" role="group" aria-label="Submit"
                             style="margin-top: 2.5%;">
                            <div class="btn-group" role="group">
                                <button type="submit" class="btn btn-default">Create</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php

    Render::view('syscrack/templates/template.footer', array('breadcrumb' => true));
    ?>
</div>
</body>
<footer>
    <script>
        $("#form-schema").change(function () {
            var ckb = $("#form-schema").is(':checked');

            if (ckb == true) {

                $("#form-name").prop('disabled', false);
                $("#form-page").prop('disabled', false);
                $("#form-riddle").prop('disabled', false);
            }
            else {

                $("#form-name").prop('disabled', true);
                $("#form-page").prop('disabled', true);
                $("#form-riddle").prop('disabled', true);

                if ($('#form-riddleid').is(':disabled') == false) {

                    $("#form-riddlecomputer").prop('disabled', true);
                    $("#form-riddleid").prop('disabled', true);
                }

                if ($("#form-riddle").is(':checked')) {

                    $('#form-riddle').attr('checked', false); // Unchecks it
                }
            }
        });

        $("#form-riddle").change(function () {
            var ckb = $("#form-riddle").is(':checked');

            if (ckb == true) {

                $("#form-riddleid").prop('disabled', false);
                $("#form-riddlecomputer").prop('disabled', false);
            }
            else {

                $("#form-riddlecomputer").prop('disabled', true);
                $("#form-riddleid").prop('disabled', true);
            }
        })
    </script>
</footer>
</html>
