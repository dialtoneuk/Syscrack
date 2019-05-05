<?php

use Framework\Application\Container;
use Framework\Syscrack\Game\Software;
use Framework\Syscrack\Game\Utilities\PageHelper;
use Framework\Syscrack\User;

$session = Container::getObject('session');

if ($session->isLoggedIn()) {

    $session->updateLastAction();
}

$pagehelper = new PageHelper();

if (isset($softwares) == false) {

    $softwares = new Software();
}

if (isset($parsedown) == false) {

    $parsedown = new Parsedown();
}

if (isset($user) == false) {

    $user = new User();
}

if ($softwares->softwareExists($softwareid) == false) {

    Flight::redirect('/game/');
}

$software = $softwares->getSoftware($softwareid);
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
        <div class="col-md-12">
            <h5>
                Viewing
                File <?= $softwares->getSoftware($softwareid)->softwarename ?><?= $softwares->getSoftwareClassFromID($softwareid)->configuration()['extension'] ?>
            </h5>

            <div class="row">
                <div class="col-md-8">
                    <h6>
                        Text Contents
                    </h6>
                    <?php
                    if (isset($data['text'])) {

                        if (empty($data['text'])) {

                            ?>
                            <div class="panel panel-danger">
                                <div class="panel-body">
                                    No text contents to display
                                </div>
                            </div>
                            <?php
                        }

                        ?>
                        <div class="well-lg">
                            <?= $parsedown->parse($data['text']) ?>
                        </div>
                        <?php
                    } else {

                        ?>
                        <div class="panel panel-danger">
                            <div class="panel-body">
                                No text contents to display
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <div class="col-md-4">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            Information
                        </div>
                        <div class="panel-body">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    Owner <span class="badge right"><?= $user->getUsername($software->userid) ?></span>
                                </li>
                                <li class="list-group-item">
                                    Last Modified <span
                                            class="badge right"><?= date("F j, Y, g:i a", $software->lastmodified) ?></span>
                                </li>
                                <li class="list-group-item">
                                    Type <span class="badge right"><?= $software->type ?></span>
                                </li>
                            </ul>
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
</body>
</html>