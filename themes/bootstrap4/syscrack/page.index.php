<?php
    /**
     *  Syscrack 2018
     */

    use Framework\Application\Render;
?>
<html>
    <head>
        <?php
            Render::view('syscrack/templates/template.header');
        ?>
    </head>
    <body>
        <div class="container-fluid" style="padding: 0;">
            <div class="row">
                <?php
                    Render::view('syscrack/templates/template.navigration');
                ?>
            </div>
        </div>
    </body>
    <footer>
        <?php
            Render::view('syscrack/templates/template.footer');
        ?>
    </footer>
</html>
