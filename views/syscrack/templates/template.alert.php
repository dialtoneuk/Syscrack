<?php

    use Framework\Application\Settings;

    if( isset( $_GET['success'] ) == false && isset( $_GET['error'] ) == true )
    {

        if( isset( $_SESSION['error'] ) == false && $message == "" )
        {

            return;
        }

        if( Settings::getSetting('error_use_session') == true )
        {

            if( isset( $_SESSION['error'] ) == false || $_SESSION['error'] == null || $_SESSION['error'] == "" )
            {

                return;
            }
        }
    }
    else
    {

        if( isset( $_GET['error'] ) == true )
        {

            if( isset( $_SESSION['error'] ) == false && $message == "" )
            {

                return;
            }

            if( Settings::getSetting('error_use_session') == true )
            {

                if( isset( $_SESSION['error'] ) == false || $_SESSION['error'] == null || $_SESSION['error'] == "" )
                {

                    return;
                }
            }
        }
    }
?>


<div class="alert alert-dismissible <?php if( isset( $alert_type ) ){ echo $alert_type; }else{?> alert-danger <?php }?>" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <?php

        if( isset( $_GET['success'] ) && isset( $_GET['error'] ) == false )
        {

            echo ( htmlspecialchars( $message, ENT_QUOTES, 'UTF-8') );
        }
        else
        {

            if( Settings::getSetting('error_use_session') )
            {
                if( isset( $_SESSION['error'] ) )
                {

                    echo ( htmlspecialchars( $_SESSION['error'], ENT_QUOTES, 'UTF-8') );
                }
            }
            else
            {

                echo ( htmlspecialchars( $message, ENT_QUOTES, 'UTF-8') );
            }
        }
    ?>
</div>



