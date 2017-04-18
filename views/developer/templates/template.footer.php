<footer style="padding-top: 2.5%">

    <?php
        if( isset( $breadcrumb ) )
        {

            $url = $_SERVER['REQUEST_URI'];

            $paths = explode('/', $url);

            $built = '';

            echo '<ol class="breadcrumb">';

            echo '<li><a href="/' . \Framework\Application\Settings::getSetting('developer_page') . '">Home</a></li>';

            foreach( $paths as $path )
            {

                if( empty( explode('?', $path ) ) == false )
                {

                    $path = explode('?', $path)[0];
                }

                if( empty( $path ) || $path == \Framework\Application\Settings::getSetting('developer_page') )
                {

                    continue;
                }

                ?>
                <li><a class="text-capitalize" href="<?= '/' . $built . $path?>"><?=$path?></a></li>
                <?php

                $built = $built . $path . '/';
            }

            echo '</ol>';
        }
    ?>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="/assets/js/bootstrap.min.js"></script>
    <script src="/assets/js/bootstrap-combobox.js"></script>
</footer>
