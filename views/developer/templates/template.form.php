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
        <div class="input-group" style="margin-top: 4%">
            <span class="input-group-addon" id="sizing-addon2">@</span>
            <input type="<?= $element['type']?>" name="<?= $element['name']?>" class="form-control"
                   placeholder="<?php if( isset( $element['placeholder'] )){ echo $element['placeholder']; }?>"
                   aria-describedby="sizing-addon2">
        </div>
        <?php
    }
    ?>

    <button class="btn btn-lg btn-primary btn-block" style="margin-top: 2.5%" type="submit">
        <?php

            if( isset( $form_submit_label ) )
            {

                echo $form_submit_label;
            }
            else
            {
                ?>
                    Submit!
                <?php
            }
        ?>
    </button>
</form>
