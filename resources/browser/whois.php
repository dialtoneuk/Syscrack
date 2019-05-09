<div class="row">
    <div class="col-sm-6">
        <h5 style="color: #ababab" class="text-uppercase">
            Welcome
        </h5>
        <p>
            Welcome to the world wide web, this is my whois page. Here I have collected a bunch of links for you to
            go explore! Hopefully somebody invents something which can automatically do this in the future, finding
            new addresses is hard!
        </p>
        <p>
            I will update this website overtime, so please check back for updates!
        </p>
        <p>
            <strong>Webmaster Haskell</strong>
        </p>
    </div>
    <div class="col-sm-6">
        <h5 style="color: #ababab" class="text-uppercase">
            Links
        </h5>
        <ul class="list-group">
            <?php
                if( isset( $whois_computers ) )
                    foreach( $whois_computers as $computer )
                    {

                        $metadata = $metaset[ $computer->computerid ]
                        ?>
                        <li class="list-group-item">
                            <small><?php if( isset( $metadata->custom["name"] ) ) echo $metadata->custom["name"]; else echo $metadata->name; ?> </small>
                            <p><a href="/game/internet/<?= @$computer->ipaddress?>"><?= @$computer->ipaddress?></a></p>
                        </li>
                        <?php
                    }
            ?>
        </ul>
    </div>
</div>