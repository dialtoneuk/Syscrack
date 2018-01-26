<?php

use Framework\Application\Container;
use Framework\Application\Render;
use Framework\Application\Settings;
use Framework\Syscrack\Game\Riddles;
use Framework\Syscrack\Game\Utilities\PageHelper;
use Framework\Syscrack\User;

$session = Container::getObject('session');

if ($session->isLoggedIn()) {

    $session->updateLastAction();
}

if (isset($user) == false) {

    $user = new User();
}

if (isset($pagehelper) == false) {

    $pagehelper = new PageHelper();
}

if (isset($riddles) == false) {

    $riddles = new Riddles();
}
?>
<html>

<?php

Render::view('syscrack/templates/template.header', array('pagetitle' => 'Syscrack | Admin'));
?>
<body>
<div class="container">

    <?php

    Render::view('syscrack/templates/template.navigation');
    ?>
    <div class="row">
        <div class="col-lg-12">
            <?php

            if (isset($_GET['error']))
                Render::view('syscrack/templates/template.alert', array('message' => $_GET['error']));
            elseif (isset($_GET['success']))
                Render::view('syscrack/templates/template.alert', array('message' => Settings::getSetting('alert_success_message'), 'alert_type' => 'alert-success'));
            ?>
        </div>
    </div>
    <div class="row">

        <?php

        Render::view('syscrack/templates/template.admin.options');
        ?>
        <div class="col-lg-8">
            <h5 style="color: #ababab" class="text-uppercase">
                Riddles
            </h5>
            <div class="row">
                <?php

                $computerriddles = $riddles->getAllRiddles();

                if (empty($computerriddles)) {

                    ?>
                    <div class="col-sm-12">
                        <div class="panel panel-warning">
                            <div class="panel-heading">
                                No Riddles
                            </div>
                            <div class="panel-body">
                                There are currently no riddles, maybe you should create some?
                            </div>
                        </div>
                    </div>
                    <?php
                } else {

                    ?>
                    <div class="col-md-12">
                        <?php

                        foreach ($computerriddles as $key => $value) {

                            ?>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            Riddle <span class="badge" style="float: right;"><?= $key ?></span>
                                        </div>
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <h5>
                                                        <?= $value['question'] ?>
                                                    </h5>
                                                    <div class="well">
                                                        <?= $value['answer'] ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <form method="post">
                                                        <div class="btn-group btn-group-justified" role="group"
                                                             aria-label="...">
                                                            <div class="btn-group" role="group">
                                                                <button type="submit" name="action" value="delete"
                                                                        class="btn btn-danger">Delete
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </form>
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
                    <?php
                }
                ?>
            </div>
        </div>
    </div>

    <?php

    Render::view('syscrack/templates/template.footer', array('breadcrumb' => true));
    ?>
</div>
</body>
</html>
