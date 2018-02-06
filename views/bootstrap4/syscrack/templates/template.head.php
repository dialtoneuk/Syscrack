<?php
<<<<<<< HEAD
    use Framework\Application\Render;

=======
>>>>>>> 1c0aca3e10809bad2ef4fc3d7789b9044fafa2bc
    if ( empty( $model ) )
    {

        throw new \Framework\Exceptions\ViewException('No model, have you got mvc output enabled in settings?');
    }

    if ( isset( $model->pagetitle ) )
    {
        ?>
            <title><?=$model->pagetitle?></title>
        <?php
    }
?>

<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

<<<<<<< HEAD
<link href="<?=Render::getAssetsLocation()?>css/bootstrap.css" rel="stylesheet">
=======
<link href="/assets/bootstrap4/css/bootstrap.css" rel="stylesheet">
>>>>>>> 1c0aca3e10809bad2ef4fc3d7789b9044fafa2bc

<!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->