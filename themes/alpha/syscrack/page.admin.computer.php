<?php

use Framework\Application\Container;
use Framework\Application\Render;
use Framework\Application\Settings;
use Framework\Syscrack\Game\Computer;
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

if (isset($computer_controller) == false) {

    $computer_controller = new Computer();
}
?>
<html>

<?php

Render::view('syscrack/templates/template.header', array('pagetitle' => 'Syscrack | Admin'));

if ( isset( $_GET['page'] ) )
{

    if ( is_numeric( $_GET['page'] ) == false || strlen(  $_GET['page']  ) > 5 )
    {

        $_GET['page'] = null;
    }
}

?>
<body>
<div class="container">
	<?php
		Render::view('syscrack/templates/template.navigation');
		Render::view('syscrack/templates/template.errors');
	?>
    <div class="row">

        <?php

        Render::view('syscrack/templates/template.admin.options');

        $computer = $computer_controller->getAllComputers()->toArray();
        $pages = floor( count( $computer ) / $settings['syscrack_admin_computer_count'] );
        ?>
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-6">
                    <div class="thumbnail">
                        <div class="caption">
                            <h5>Total Virtual Computers</h5>
                            <h3 style="font-size: 1.5em;">
                                <?=count( $computer )?>
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <nav aria-label="Page navigation">
                                <ul class="pagination">
                                    <?php
                                    if( @$_GET['page'] > 0 )
                                    {
                                        ?><li><a href="?page=<?=htmlspecialchars($_GET["page"] - 1 )?>">&laquo;</a></li><?php
                                    }
                                    else
                                    {
                                        ?><li class="disabled"><a>&laquo;</a></li><?php
                                    }
                                    ?>
                                    <?php
                                        for ( $i=0; $i < $pages + 1; $i++ )
                                        {

                                            if( $i == @$_GET['page'] )
                                            {
                                                ?>
                                                <li class="active"><a href="?page=<?=$i?>"><?=$i?></a></li>
                                                <?php
                                            }
                                            else
                                            {
                                                ?>
                                                <li><a href="?page=<?=$i?>"><?=$i?></a></li>
                                                <?php
                                            }


                                        }
                                    ?>
                                    <?php
                                    if( @$_GET['page'] <= $pages - 1 && @$_GET['page'] >= 0 )
                                    {
                                        ?><li><a href="?page=<?=htmlspecialchars(@$_GET["page"] + 1 )?>">&raquo;</a></li><?php
                                    }
                                    elseif(  isset( $_GET['page'] ) == false  )
                                    {
                                        ?><li><a href="?page=<?=htmlspecialchars(@$_GET["page"] + 1 )?>">&raquo;</a></li><?php
                                    }
                                    else
                                    {
                                    ?><li class="disabled"><a>&raquo;</a></li><?php
                                    }
                                    ?>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top: 1.5%;">
                <?php

                $offset = 0;

                if (  isset( $_GET['page'] ) && $_GET['page'] != null )
                {

                    $offset = $_GET['page'] * $settings['syscrack_admin_computer_count'];
                }

                $computer = array_slice( array_reverse( $computer ), $offset, $settings['syscrack_admin_computer_count']);

                if (empty($computer)) {

                    ?>
                    <div class="col-sm-12">
                        <div class="panel panel-danger">
                            <div class="panel-heading">
                                No Computer Found
                            </div>
                            <div class="panel-body">
                                It appears that no computers were found on your system... this is strange?
                            </div>
                        </div>
                    </div>
                    <?php
                } else {

                    foreach ($computer as $key => $value) {

                        ?>
                        <div class="col-sm-12">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-sm-3">
                                           <h5>
                                               <?=$value->computerid?><small> computer id</small>
                                           </h5>
                                        </div>
                                        <div class="col-sm-5">
                                            <h5>
                                                <?=$value->ipaddress?><span class="badge" style="margin-left: 5%;"><?=$value->type?></span>
                                            </h5>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="btn-group" style="float: right;" role="group" aria-label="...">
                                                <button type="button" onclick="window.location.href = '/game/internet/<?=$value->ipaddress?>/'" class="btn btn-warning">View</button>
                                                <button type="button" onclick="window.location.href = '/admin/computer/edit/<?=$value->computerid?>/'"class="btn btn-success">Edit</button>
                                                <button type="button" onclick="window.location.href = '/admin/users/edit/<?=$value->userid?>/'" class="btn btn-info">Owner</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <p class="text-center">
                <a onclick="$('html, body').animate({ scrollTop: 0 }, 'fast');" style="cursor: pointer;">
                    Back to the top...
                </a>
            </p>
        </div>
    </div>

    <?php

    Render::view('syscrack/templates/template.footer', array('breadcrumb' => true));
    ?>
</div>
</body>
</html>
