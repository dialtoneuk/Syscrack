<?php
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

<link href="/assets/bootstrap4/css/bootstrap.css" rel="stylesheet">

<!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->