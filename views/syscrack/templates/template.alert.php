<div style="margin-top: 2.5%;" class="alert alert-dismissible <?php if( isset( $alert_type ) ){ echo $alert_type; }else{?> alert-danger <?php }?>" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <?= $message ?>
</div>



