<?php

use Framework\Application\Settings;
use Framework\Application\Render;

?>
<?php

/**
 * Since this template is usually the first loaded onto every single page, I can sneak this here
 */

if ( Settings::getSetting('render_mvc_output') == true )
{

    throw new \Framework\Exceptions\ViewException('Current render folder "' . Settings::getSetting('render_folder') . '" does not support mvc output and cannot function using it. Please disable render_mvc_output in your settings to use this render folder' );
}
?>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= $pagetitle ?></title>

    <!--Fav Icons-->
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/manifest.json">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="apple-mobile-web-app-title" content="Syscrack">
    <meta name="application-name" content="Syscrack">
    <meta name="theme-color" content="#ffffff">

    <!-- Stylesheets -->

    <?php
    if (Settings::getSetting('theme_dark') == true) {

        ?>
        <link href="<?=Render::getAssetsLocation()?>css/bootstrap.dark.css" rel="stylesheet">
        <?php
    } else {

        ?>

        <link href="<?=Render::getAssetsLocation()?>css/bootstrap.min.css" rel="stylesheet">
        <?php
    }
    ?>
    <link href="<?=Render::getAssetsLocation()?>css/bootstrap-combobox.css" rel="stylesheet">

    <?php
    if (isset($styles)) {

        if (is_array($styles)) {

            foreach ($styles as $style) {

                echo $style;
            }
        }
    }
    ?>

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <?php
    if (isset($scripts)) {

        if (is_array($scripts)) {

            foreach ($scripts as $script) {

                echo $script;
            }
        }
    }
    ?>

    <script src="<?=Render::getAssetsLocation()?>js/progressbar.js"></script>
    <style>

        pre, code {
            white-space: pre-line;
        <?php

            if( Settings::getSetting('theme_dark') )
            {

                ?> background-color: #19171c;
            color: #888888;
            border: 0;
        <?php
    }
?>
        }

        <?php

            if( Settings::getSetting('theme_dark') )
            {

                ?>

        textarea {

            background-color: #19171c;
            border: 0px;
            color: #888888;
        }

        ::-webkit-scrollbar {
            width: 12px; /* for vertical scrollbars */
            height: 12px; /* for horizontal scrollbars */
        }

        ::-webkit-scrollbar-track {
            background: #888888;
        }

        ::-webkit-scrollbar-thumb {
            background: #151515;
        }

        ::-webkit-resizer {
            background-color: #424242;
            border: 1px solid #888888;
            /*size does not work*/
            display: block;
            width: 150px !important;
            height: 150px !important;
        }

        <?php
    }
?>

        @media (max-width: 992px) {
            .navbar-fix {
                display: none;
            }
        }
    </style>
</head>