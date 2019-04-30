<?php
    /**
     *  Syscrack 2018
     */

    use Framework\Application\Render;
?>
<html>
    <head>
        <?php
            Render::view('syscrack/templates/template.head', $data, $model );
        ?>
    </head>
    <body>
        <div class="container-fluid" style="padding: 0;">
            <div class="row">
                <?php
                    Render::view('syscrack/templates/template.navbar', $data, $model );
                ?>
            </div>
        </div>
    </body>
    <footer>
        <?php
            Render::view('syscrack/templates/template.footer', $data, $model );
        ?>
    </footer>
</html>
