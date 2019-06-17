<div class="row">
    <div class="col-sm-12">
        <h5 style="color: #ababab" class="text-uppercase">
            TorrentBay
        </h5>

        <p>
            The most popular torrenting network
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

				    foreach ($downloads as $key=>$download)
				    {



					    ?>
                        <li class="list-group-item">
                            <a href="/game/internet/<?=$computer->ipaddress?>/anondownload/<?=$download->softwareid ?>"><?= $download->softwarename . ' ' . $download->size . 'mb (' . $download->level . ')' ?></a>
                        </li>
					    <?php
				    }
			    }
		    ?>
        </ul>
    </div>
</div>