<?php
    use Framework\Application\Render;
?>
<html>
<?php

Render::view('syscrack/templates/template.header', array('pagetitle' => 'Syscrack | Admin | BaseComputer Creator'));
?>
<body>
<div class="container">
	<?php
		Render::view('syscrack/templates/template.navigation');
		Render::view('syscrack/templates/template.errors');
	?>
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
                $schema = $settings['syscrack_example_schema'];
                ?>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="panel panel-info">
                            <div class="panel-body">
                                When you create your new machine you'll be able to edit it in more detail. Don't worry about
                                filling anything in as you can change it later. If you'd like to read up more about what
                                each type does, please <a href="">check out our github.</a>
                            </div>
                        </div>
                        <div class="panel panel-info">
                            <div class="panel-body">
                                <?php
                                Render::view('syscrack/templates/template.form', array('form_elements' => [
                                    [
                                        'type' => 'number',
                                        'name' => 'userid',
                                        'placeholder' => 1,
                                        'value' => $settings['syscrack_master_user'],
                                        'icon' => 'glyphicon-user'
                                    ],
                                    [
                                        'type' => 'text',
                                        'name' => 'ipaddress',
                                        'icon' => 'glyphicon-globe',
                                        'placeholder' => '1.2.3.4',
                                        'value' => $random_address
                                    ]
                                ], 'remove_submit' => true, 'remove_form' => true));
                                ?>
                                <label class="text-uppercase" style="color: lightgray;" for="type">Types</label>
                                <select name="type" class="combobox input-sm form-control">
                                    <?php

                                    if( empty( $types ) == false )
                                    {

                                        foreach( $types as $type )
                                        {
                                            ?>
                                            <option value="<?=$type?>"><?=$type?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <?php
                        Render::view('syscrack/templates/template.form', array('form_elements' => [
                            [
                                'type' => 'textarea',
                                'name' => 'hardware',
                                'value' => json_encode($schema['hardwares'], JSON_PRETTY_PRINT)
                            ]
                        ], 'remove_submit' => true, 'remove_form' => true));
                        ?>
                        <?php
                        Render::view('syscrack/templates/template.form', array('form_elements' => [
                            [
                                'type' => 'textarea',
                                'name' => 'software',
                                'value' => json_encode([], JSON_PRETTY_PRINT)
                            ]
                        ], 'remove_submit' => true, 'remove_form' => true));
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <?php
                                Render::view('syscrack/templates/template.form', array('form_elements' => [
                                    [
                                        'type' => 'string',
                                        'name' => 'name',
                                        'placeholder' => "",
                                        'icon' => 'glyphicon-user'
                                    ]
                                ], 'remove_submit' => true, 'remove_form' => true));
                                ?>
                                <label class="text-uppercase" style="color: lightgray;" for="browserpages">Page</label>
                                <select name="browserpages" class="combobox input-sm form-control">
                                    <option></option>
                                    <?php

                                    if( empty( $browserpages ) == false )
                                    {

                                        foreach( $browserpages as $page )
                                        {
                                            ?>
                                            <option value="<?=$page?>"><?=$page?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="btn-group btn-group-justified" role="group" aria-label="Submit">
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
