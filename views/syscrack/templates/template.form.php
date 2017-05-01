<?php
    use Framework\Exceptions\ViewException;

    if( isset( $form_elements ) == false )
    {

        throw new ViewException('Form Elements must be passed when using the form template');
    }

    if( is_array( $form_elements ) == false )
    {

        throw new ViewException('Must be array');
    }
?>

<form action="<?php if( isset( $form_action )){ echo $form_action; }?>" method="post">

    <?php
        foreach( $form_elements as $element )
        {

            if( isset( $element['type'] ) == false || isset( $element['name'] ) == false )
            {

                throw new ViewException();
            }

            ?>
            <label class="text-uppercase" style="color: lightgray;" for="form-<?= $element['name']?>">
                <?= $element['name']?>
            </label>
            <div class="input-group" style="padding-bottom: 1%;">
                <span class="input-group-addon" id="sizing-addon2"><span class="glyphicon <?php if(isset( $element['icon'])){ echo $element['icon']; }else{ echo 'glyphicon-arrow-right'; }?>"></span></span>
                <input type="<?= $element['type']?>" name="<?= $element['name']?>" id="form-<?= $element['name']?>" class="form-control"
                       placeholder="<?php if( isset( $element['placeholder'] )){ echo $element['placeholder']; }?>"
                >
            </div>
            <?php
        }
    ?>

    <div class="btn-group btn-group-justified" style="margin-top: 2.5%" role="group" aria-label="Submit">
        <div class="btn-group" role="group">
            <button type="submit" class="btn btn-default"><?php if( isset( $form_submit_label ) ){ echo $form_submit_label; }else{ echo 'Submit'; }?></button>
        </div>
    </div>
</form>
