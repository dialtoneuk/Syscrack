<select name="softwareid" class="combobox input-sm form-control">
    <option></option>
    <?php

    if (empty($values) == false)
    {
        foreach (@$values as $key => $software)
            echo('<option value="' . @$software->softwareid . '">' . @$software->softwarename . @$extensions[ @$software->softwareid ] . ' ' . @$software->size . 'mb (' . @$software->level . ')</option>');
    }
    else
	    echo '<option value="">Error retrieving data</option>'
    ?>
</select>