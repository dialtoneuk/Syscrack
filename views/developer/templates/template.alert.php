<div class="alert alert-dismissible <?php if( isset( $alert_type ) ){ echo $alert_type; }else{?> alert-danger <?php }?>" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <?= htmlspecialchars( $message, ENT_QUOTES, 'UTF-8') ?>
</div>



