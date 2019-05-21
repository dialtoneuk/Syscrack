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
		        Render::view('syscrack/templates/template.errors');
	        ?>
            <div class="row">
                <?php
                    Render::view('syscrack/templates/template.admin.options');
                ?>
                <div class="col-sm-8">
                    <div class="row">
                        <div class="col-sm-12">
                            <h5 style="color: #ababab" class="text-uppercase">
                                Riddles
                            </h5>
                        </div>
                    </div>
                    <div class="row">
                        <?php
                        if (empty($riddles)) {

                            ?>
                            <div class="col-sm-12">
                                <div class="panel panel-warning">
                                    <div class="panel-heading">
                                        No Riddles
                                    </div>
                                    <div class="panel-body">
                                        There are currently no riddles, maybe you should create some?
                                    </div>
                                </div>
                            </div>
                            <?php
                        } else {

                            ?>
                            <div class="col-md-12">
                                <?php

                                foreach ($riddles as $key => $value) {

                                    ?>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="panel panel-default">
                                                <div class="panel-body">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <h2>
                                                                <?=$key?>
                                                            </h2>
                                                        </div>
                                                        <div class="col-sm-8">
                                                            <h4>
                                                                <i><?= $value['question'] ?></i>
                                                            </h4>
                                                            <?= $value['answer'] ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                            <?php
                        }
                    ?>
                    </div>
                </div>
            </div>

            <?php

            Render::view('syscrack/templates/template.footer', array('breadcrumb' => true));
            ?>
        </div>
    </body>
</html>
