<footer style="padding-top: 2.5%">

    <?php
        if( isset( $breadcrumb ) )
        {

            $url = $_SERVER['REQUEST_URI'];

            if( empty( explode('?', $url ) ) == false )
            {

                $url = explode('?', $url )[0];
            }

            $paths = explode('/', $url);

            $built = '';

            echo '<ol class="breadcrumb">';

            echo '<li><a href="/' . \Framework\Application\Settings::getSetting('controller_index_page') . '">Home</a></li>';

            foreach( $paths as $path )
            {

                if( empty( explode('?', $path ) ) == false )
                {

                    $path = explode('?', $path)[0];
                }

                if( empty( $path ) || $path == \Framework\Application\Settings::getSetting('controller_index_page') )
                {

                    continue;
                }

                ?>
                    <li><a class="text-capitalize" href="<?= '/' . htmlspecialchars( $built . $path, ENT_QUOTES, 'UTF-8' )?>"><?=htmlspecialchars( $path, ENT_QUOTES, 'UTF-8' )?></a></li>
                <?php

                $built = $built . $path . '/';
            }

            echo '</ol>';
        }
    ?>
    <div class="row">
        <div class="col-sm-12">
            <p class="small text-center" style="color: lightgray;">
                Syscrack 2017 was created by <a href="http://www.github.com/dialtoneuk/">Lewis Lancaster</a> and we loaded in <?=SYSCRACK_TIME_END - SYSCRACK_TIME_START;?> seconds
            </p>
        </div>
    </div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="/assets/js/bootstrap.min.js"></script>
    <script src="/assets/js/bootstrap-combobox.js"></script>
</footer>
