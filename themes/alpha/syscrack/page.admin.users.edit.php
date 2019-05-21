<?php
    use Framework\Application\Render;
    use Framework\Application\Settings;
    use Framework\Syscrack\User;

    if ( isset( $users ) == false )
    {

        $users = new User();
    }

    if ( isset( $computer_controller ) == false )
    {

        $computer_controller = new \Framework\Syscrack\Game\Computer();
    }

    if ( isset( $userid ) == false )
    {

        throw new \Framework\Exceptions\ViewException();
    }

    $user = $users->getUser( $userid );
    $computer = $computer_controller->getUserComputers( $userid );
?>
<html>

    <?php

        Render::view('syscrack/templates/template.header', array('pagetitle' => 'Syscrack | Admin'));
    ?>
    <div class="container">
	    <?php
		    Render::view('syscrack/templates/template.navigation');
		    Render::view('syscrack/templates/template.errors');
	    ?>
        <div class="row">
            <div class="col-sm-12">
                <h5 style="color: #ababab" class="text-uppercase">
                    <span class="badge"><?= $user->userid ?></span> <?= $user->username ?>
                </h5>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4">
                <div class="thumbnail">
                    <div class="caption">
                        <h5>Total Virtual Computer</h5>
                        <h3 style="font-size: 1.5em;">
                            <?=count( $computer )?>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="thumbnail">
                    <div class="caption">
                        <h5>Group</h5>
                        <h3 style="font-size: 1.5em;">
                            <?=$user->group?>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="thumbnail">
                    <div class="caption">
                        <h5>Play Time</h5>
                        <h3 style="font-size: 1.5em;">
                            10 Hours
                        </h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div>
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs" role="tablist">
                                <li role="presentation" class="active">
                                    <a href="#options" aria-controls="options" role="tab" data-toggle="tab">Options</a>
                                </li>
                                <li role="presentation">
                                    <a href="#computers" aria-controls="computers" role="tab" data-toggle="tab">Computer</a>
                                </li>
                                <li style="float: right;"><a href="/admin/users/">Home <span class="glyphicon glyphicon-arrow-right"></span> </a></li>
                            </ul>

                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active" id="options">
                                    <form method="post">
                                        <div class="row" style="margin-top: 2.5%;">
                                            <div class="col-sm-6">
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        <form method="post">
                                                            <div class="input-group">
                                                                <span class="input-group-addon" id="basic-addon1">
                                                                    <span class="glyphicon glyphicon-user" aria-hidden="true">
                                                                    </span>
                                                                </span>
                                                                <input type="text" class="form-control" name="group" placeholder="guest" aria-describedby="basic-addon1">
                                                            </div>
                                                            <button style="width: 100%; margin-top: 2.5%;"
                                                                    class="btn btn-info" type="submit">
                                                                Change Group
                                                            </button>
                                                            <input type="hidden" name="action" value="group">
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="computers" style="padding-top: 2.5%;">
                                    <?php

                                        foreach ( $computer as $key=>$value )
                                        {
                                            ?>
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="panel panel-info">
                                                            <div class="panel-body">
                                                                <div class="row">
                                                                    <div class="col-sm-3">
                                                                        <span class="badge" style="font-size: 2em;"><?=$value->computerid?></span><span class="badge" style="margin-left: 5%;"><?=$value->type?></span>
                                                                    </div>
                                                                    <div class="col-sm-5">
                                                                        <h5>
                                                                            <?=$value->ipaddress?>
                                                                        </h5>
                                                                    </div>
                                                                    <div class="col-sm-4">
                                                                        <div class="btn-group" style="float: right;" role="group" aria-label="...">
                                                                            <button type="button" onclick="window.location.href = '/game/internet/<?=$value->ipaddress?>/'" class="btn btn-warning">View</button>
                                                                            <button type="button" onclick="window.location.href = '/admin/computer/edit/<?=$value->computerid?>/'"class="btn btn-success">Edit</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php
                                        }
                                    ?>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="ban">

                                </div>
                                <div role="tabpanel" class="tab-pane" id="debug">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php

            Render::view('syscrack/templates/template.footer', array('breadcrumb' => true));
        ?>
    </div>
</html>