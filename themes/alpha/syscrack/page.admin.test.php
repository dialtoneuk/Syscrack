<?php

	use Framework\Application\Render;
?>
<html>
	<?php
		Render::view('syscrack/templates/template.header', array('pagetitle' => 'Syscrack | Admin' ) )
	?>
	<div class="container">
		<?php
			Render::view('syscrack/templates/template.navigation');
		?>
        <div class="row">
            <div class="col-lg-12">
                <h1>
                    Session
                </h1>
                <div class="well">
	                <?php
		                echo print_r( $_SESSION );
	                ?>
                </div>

            </div>
        </div>
		<div class="row">
			<div class="col-lg-12">
                <h1>
                    Errors
                </h1>
				<?php
					Render::view('syscrack/templates/template.errors');
				?>
			</div>
		</div>

		<?php
			Render::view('syscrack/templates/template.footer', array('breadcrumb' => true));
		?>
	</div>
</html>
