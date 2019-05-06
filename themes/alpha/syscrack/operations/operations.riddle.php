<?php
    use Framework\Application\Render;

    $riddles = new \Framework\Syscrack\Game\Riddles();

    if ( isset( $riddleid ) == false )
    {

        Render::redirect('/computer/');
    }
    else
    {

        if ( $riddles->hasRiddle( $riddleid ) == false )
        {

            Render::redirect('/computer/');
        }
    }

    $riddle = $riddles->getRiddle( $riddleid );
?>
<html>
    <?php
        Render::view('syscrack/templates/template.header', array('pagetitle' => 'Syscrack | Game'));
    ?>
    <body>
        <div class="container">
            <?php
                Render::view('syscrack/templates/template.navigation');
            ?>
            <div class="row">
                <div class="col-sm-12">
                    <?php

                    if (isset($_GET['error']))
                        Render::view('syscrack/templates/template.alert', array('message' => $_GET['error']));
                    elseif (isset($_GET['success']))
                        Render::view('syscrack/templates/template.alert', array('message' => $settings['alert_success_message'), 'alert_type' => 'alert-success'));
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="panel panel-primary">
                                <div class="panel-body">
                                    Solve this riddle in order to progress to the next computer!
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <form method="post">
                                        <?=$riddle['question']?>
                                        <div class="input-group" style="margin-top: 2.5%;">
                                            <span class="input-group-addon" id="basic-addon1">
                                                <span class="glyphicon glyphicon-question-sign" aria-hidden="true">
                                                </span>
                                            </span>
                                            <input type="text" class="form-control" name="answer" aria-describedby="basic-addon1">
                                        </div>
                                        <button style="width: 100%; margin-top: 2.5%;"
                                                class="btn btn-info" type="submit">
                                            Check Answer
                                        </button>
                                        <input type="hidden" name="riddleid" value="<?=$riddleid?>">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                Render::view('syscrack/templates/template.footer');
            ?>
        </div>
    </body>
</html>
