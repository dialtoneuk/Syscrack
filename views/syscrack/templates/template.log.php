<?php

    if( isset( $internet ) == false )
    {

        $internet = new \Framework\Syscrack\Game\Internet();
    }

    if( isset( $log ) == false )
    {

        $log = new \Framework\Syscrack\Game\Log();
    }
?>

<div class="row">
    <div class="col-lg-12">
        <?php

        $connectedcomputer = $internet->getComputer( $ipaddress );

        try
        {

            $log->hasLog( $connectedcomputer->computerid );
        }
        catch( Exception $error )
        {

            ob_clean();

            ?>

                <html>
                    <head>
                        <meta charset="utf-8">
                        <meta http-equiv="X-UA-Compatible" content="IE=edge">
                        <meta name="viewport" content="width=device-width, initial-scale=1">

                        <title>Empty Log Error</title>

                        <!-- Stylesheets -->
                        <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
                        <link href="/assets/css/bootstrap-combobox.css" rel="stylesheet">

                        <!--[if lt IE 9]>
                        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
                        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
                        <![endif]-->
                    </head>
                    <body>
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="panel panel-danger">
                                    <div class="panel-heading">
                                        Empty Log
                                    </div>
                                    <div class="panel-body">
                                        Oh no! Something has happened internally and you currently don't have a log file associated to your computer, please report this
                                        to an administrator or developer for them to fix this for you!

                                        <p>
                                            <a href="<?=\Framework\Application\Settings::getSetting('controller_index_root')?><?=\Framework\Application\Settings::getSetting('controller_index_page')?>">Go Home</a>
                                        </p>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>
                    </body>
                </html>
            <?php

            exit;
        }

        if( $log->hasLog( $connectedcomputer->computerid ) == false  )
        {

            ?>

            <p>
                No Log Available
            </p>
            <?php
        }
        else
        {

            ?>
                <div class="well">
                    <textarea readonly id="log" name="log" style="width: 100%; height: 400px; resize: none; font-size: 14px; padding: 2.5%;"><?php $log = array_reverse( $log->getCurrentLog( $connectedcomputer->computerid ) ); foreach( $log as $key=>$value ){ echo '[' , $value['ipaddress'] . '] ' . strftime("%d-%m-%Y %H:%M:%S", $value['time']) . ' : ' . $value['message'] . "\n";}?></textarea>
                </div>

                <div class="btn-group-vertical" style="width: 100%">
                <?php

                    if( isset( $hideoptions ) )
                    {

                        if( $hideoptions == false )
                        {

                            ?>

                                <button class="btn btn-danger" type="submit" onclick="window.location.href = '/game/internet/<?=$ipaddress?>/clear'">
                                    <span class="glyphicon glyphicon-alert" aria-hidden="true"></span> Clear Log
                                </button>
                                <button class="btn btn-success" type="button" onclick="window.location.href = '/game/internet/<?=$ipaddress?>'">
                                    <span class="glyphicon glyphicon-circle-arrow-down" aria-hidden="true"></span> Refresh Log
                                </button>
                            <?php
                        }
                    }
                    else
                    {

                        ?>

                            <button class="btn btn-danger" type="submit">
                                <span class="glyphicon glyphicon-alert" aria-hidden="true"></span> Clear Log
                            </button>
                            <button class="btn btn-success" type="button" onclick="window.location.href = '/game/internet/<?=$ipaddress?>'">
                                <span class="glyphicon glyphicon-circle-arrow-down" aria-hidden="true"></span> Refresh Log
                            </button>
                        <?php
                    }
                    ?>
                </div>
            <?php
        }
        ?>
    </div>
</div>