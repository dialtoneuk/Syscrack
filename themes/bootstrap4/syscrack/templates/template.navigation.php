<div class="col-sm-12">

    <?php
        if ( $model->session['loggedin'])
        {

            ?>
                <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <?php
        }
        else
        {
            ?>
                <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <?php
        }
    ?>

        <a class="navbar-brand" href="#">Syscrack</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <?php
                    if ( $model->session['loggedin'] ) {
                        ?>
                            <li class="nav-item active">
                                <a class="nav-link" href="#">Hub</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">Internet</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">VPC</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">Processes</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">Finances</a>
                            </li>
                        <?php
                    }
                    else {
                        ?>
                            <li class="nav-item active">
                                <a class="nav-link" href="#">About Us</a>
                            </li>
                            <li class="nav-item active">
                                <a class="nav-link" href="#">Github</a>
                            </li>
                        <?php
                    }
                ?>
            </ul>
            <?php
            if ( $model->session['loggedin'] ) {
                ?>
                    <h5 class="navbar-text" style="margin-right: 1.5%;">
                        <?=@$computer->ipaddress?>
                    </h5>
                    <h5 class="navbar-text" style="margin-right: 1.5%; color: #28a745;">
                        £<?=@$cash?>
                    </h5>
                    <a class="form-inline my-2 my-lg-0" href="/account/settings">
                        <button class="btn btn-outline-success my-2 my-sm-0"><?=$model->user['username']?></button>
                    </a>
                <?php
            }
            else {
                ?>
                    <a class="form-inline my-2 my-lg-0" href="/account/settings">
                        <button class="btn btn-outline-success my-2 my-sm-0">Login</button>
                    </a>
                <?php
            }
            ?>
        </div>
    </nav>
</div>