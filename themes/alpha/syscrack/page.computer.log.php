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
                <div class="col-sm-12" onclick="window.location.href = '/computer/'">
                    <h5 style="color: #ababab" class="text-uppercase">
                        <span class="badge"><?= $computer->type ?></span> <?= $computer->ipaddress ?>
                    </h5>
                </div>
            </div>
            <div class="row" style="margin-top: 1.5%;">

                <?php
                    Render::view('syscrack/templates/template.computer.actions');
                ?>
                <div class="col-md-8">
                    <form action="/computer/actions/clear" method="post">
                    <?php
                        Render::view('syscrack/templates/template.log')
                    ?>
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <?php
                        Render::view('syscrack/templates/template.footer', array('breadcrumb' => true));
                    ?>
                </div>
            </div>
        </div>
    </body>
</html>