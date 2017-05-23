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

<?php
    if( isset( $remove_form) )
    {

        if( $remove_form == false )
        {

            ?>
                <form class="form-group" action="<?php if( isset( $form_action )){ echo $form_action; }?>" method="post">
            <?php
        }
    }
    else
    {

        ?>
            <form class="form-group" action="<?php if( isset( $form_action )){ echo $form_action; }?>" method="post">
        <?php
    }

    foreach( $form_elements as $element )
    {

        if( isset( $element['type'] ) == false || isset( $element['name'] ) == false )
        {

            throw new ViewException();
        }

        if( $element['type'] == "textarea" )
        {

            ?>
                <label class="text-uppercase" style="color: lightgray;" for="form-<?= $element['name']?>">
                    <?= $element['name']?>
                </label>
                <div class="input-group" style="width: 100%; height: 26.25%; padding-bottom: 1.25%;">
                    <div class="well" style="height: 100%;">
                        <textarea style="resize: <?php if( isset( $element['resizeable' ] )){ echo $element['resizeable']; }else{ echo 'none'; }?>;  max-height: 600px; width: 100%; height: 100%;" name="<?= $element['name']?>" id="form-<?= $element['name']?>"><?php if( isset( $element['value'] ) ){ echo $element['value']; } ?></textarea>
                    </div>
                </div>
            <?php
        }
        elseif( $element['type'] == 'checkbox' || $element['type'] == 'radio' )
        {

            ?>
                <label class="text-uppercase" style="color: lightgray; margin-bottom: 1%;" for="form-<?= $element['name']?>">
                    <?= $element['name']?>
                </label>
                <div class="input-group" style="margin-bottom: 5%;">
                    <input type="<?= $element['type']?>" name="<?= $element['name']?>" id="form-<?= $element['name']?>"
                           placeholder="<?php if( isset( $element['placeholder'] )){ echo $element['placeholder']; }?>"
                    >
                </div>

            <?php
        }
        else
        {

            ?>

                <label class="text-uppercase" style="color: lightgray;" for="form-<?= $element['name']?>">
                    <?= $element['name']?>
                </label>
                <div class="input-group" style="padding-bottom: 1.5%;">
                    <span class="input-group-addon" id="sizing-addon2"><span class="glyphicon <?php if(isset( $element['icon'])){ echo $element['icon']; }else{ echo 'glyphicon-arrow-right'; }?>"></span></span>
                    <input type="<?= $element['type']?>" name="<?= $element['name']?>" id="form-<?= $element['name']?>" class="form-control"
                           placeholder="<?php if( isset( $element['placeholder'] )){ echo $element['placeholder']; }?>"
                           value="<?php if( isset( $element['value'] )){ echo $element['value']; }?>"
                           <?php if( isset( $element['disabled'] )){ if( $element['disabled'] == true ){ echo 'disabled'; } }?>
                    >
                </div>
            <?php
        }
    }

    if( isset( $remove_submit ) )
    {

        if( $remove_submit == false )
        {

            ?>
                <div class="btn-group btn-group-justified" style="margin-top: 2.5%" role="group" aria-label="Submit">
                    <div class="btn-group" role="group">
                        <button type="submit" class="btn btn-default"><?php if( isset( $form_submit_label ) ){ echo $form_submit_label; }else{ echo 'Submit'; }?></button>
                    </div>
                </div>
            <?php
        }
    }
    else
    {

        ?>
            <div class="btn-group btn-group-justified" style="margin-top: 2.5%" role="group" aria-label="Submit">
                <div class="btn-group" role="group">
                    <button type="submit" class="btn btn-default"><?php if( isset( $form_submit_label ) ){ echo $form_submit_label; }else{ echo 'Submit'; }?></button>
                </div>
            </div>
        <?php
    }

    if( isset( $remove_form ) )
    {

        if( $remove_form == false )
        {

            ?>
                </form>
            <?php
        }
    }
    else
    {

        ?>
            </form>
        <?php
    }
?>

