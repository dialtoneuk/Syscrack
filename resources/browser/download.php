<div class="row">
    <div class="col-sm-12">
        <h5 style="color: #ababab" class="text-uppercase">
            <?=@$metadata->custom["name"]?>
        </h5>
        <p>
            Anything that you download here won't be logged!
        </p>

        <ul class="list-group">
            <?php
;

            if (empty($downloads))
            {

                ?>
                <div class="panel panel-warning">
                    <div class="panel-body">
                        No softwares are currently available to download.. sorry!
                    </div>
                </div>
                <?php
            } else {

                foreach ($downloads as $key=>$download) {



                    ?>
                    <li class="list-group-item">
                        <a href="/game/internet/<?= $ipaddress ?>/anondownload/<?= $download->softwareid ?>"><?= $download->softwarename . ' ' . $download->size . 'mb (' . $download->level . ')' ?></a>
                    </li>
                    <?php
                }
            }
            ?>
        </ul>
    </div>
</div>