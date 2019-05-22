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
            <h5 style="color: #ababab" class="text-uppercase">
                Admin
            </h5>
            <div class="row">
                <div class="col-sm-6">
                    <div class="panel panel-success">
                        <div class="panel-heading">
                            Users
                        </div>
                        <div class="panel-body text-center">
                            <h3><?=@$userscount?></h3><span class="small"> Users</span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="panel panel-success">
                        <div class="panel-heading">
                            Online
                        </div>
                        <div class="panel-body text-center">
                            <h3><?=@$activesessions?></h3><span class="small"> Users</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
        Render::view('syscrack/templates/template.footer', array('breadcrumb' => true));
    ?>
</div>
</body>
</html>
