<select name="accountnumber" class="combobox input-sm form-control">
    <option></option>

    <?php
        if (empty($accounts) == false) {

            foreach ($accounts as $account) {

                ?>
                <option value="<?= $account->accountnumber ?>">#<?= $account->accountnumber ?>
                    (<?= $settings['syscrack_currency'] . number_format($account->cash) ?>
                    ) @<?=@$ipaddress?></option>
                <?php
            }
        }
        ?>
</select>