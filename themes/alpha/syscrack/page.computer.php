<?php
    use Framework\Application\Render;
?>
<!DOCTYPE html>
<html>
    <?php
        Render::view('syscrack/templates/template.header', array('pagetitle' => 'Syscrack | Game'));
    ?>
<body>
<div class="container">
	<?php
		Render::view('syscrack/templates/template.navigation');
		Render::view('syscrack/templates/template.errors');
	?>
    <div class="row">
        <div class="col-sm-12">
            <h5 style="color: #ababab" class="text-uppercase">
                <span class="badge"><?= $computer->type ?></span> <?= $ipaddress ?>
            </h5>
        </div>
    </div>
    <div class="row" style="margin-top: 1.5%;">
        <?php
            Render::view('syscrack/templates/template.computer.actions');
        ?>

        <div class="col-md-8">

            <?php

            Render::view('syscrack/templates/template.softwares', array("local" => true));
            ?>
        </div>
    </div>

    <?php
        Render::view('syscrack/templates/template.footer', array('breadcrumb' => true));
    ?>
</div>
</body>
</html>