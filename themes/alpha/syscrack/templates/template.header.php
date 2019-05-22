<?php

use Framework\Application\Render;
?>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= @$pagetitle ?></title>

    <!--Fav Icons-->
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/manifest.json">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="apple-mobile-web-app-title" content="Syscrack">
    <meta name="application-name" content="Syscrack">
    <meta name="theme-color" content="#ffffff">


    <link href="<?=Render::getAssetsLocation()?>css/bootstrap.min.css" rel="stylesheet">
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
    <link href="<?=Render::getAssetsLocation()?>css/custom.css" rel="stylesheet">

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
</head>