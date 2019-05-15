<select name="accountnumber" class="combobox input-sm form-control" style="margin-top: 16px;">
    <option></option>

    <?php
        if (empty($accounts) == false) {

            foreach ($accounts as $account) {

                ?>
                <option value="<?= $account->accountnumber ?>">#<?= $account->accountnumber ?>
                    (<?= $settings['syscrack_currency'] . number_format($account->cash) ?>
                    ) @<?=@$ipaddresses[ $account->computerid ]?></option>
                <?php
            }
        }
        else
            echo '<option value="">Error retrieving data</option>'
        ?>
</select>