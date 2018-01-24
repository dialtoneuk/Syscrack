<?php

    use Framework\Application\Settings;
    use Framework\Syscrack\Game\Computers;
    use Framework\Syscrack\Game\Internet;
    use Framework\Syscrack\Game\Schema;

    if( isset( $internet ) == false )
    {

        $internet = new Internet();
    }

    if( isset( $computers) == false )
    {

        $computers = new Computers();
    }

    if( isset( $npc ) == false )
    {

        $npc = new Schema();
    }
?>
<div class="row">
    <div class="col-lg-6">
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
    <div class="col-lg-6">
        <h5 style="color: #ababab" class="text-uppercase">
            Links
        </h5>
        <ul class="list-group">
            <?php

                if( Settings::hasSetting('syscrack_whois_default_computers') == true )
                {

                    $whoiscomputers = Settings::getSetting('syscrack_whois_default_computers');

                    foreach( $whoiscomputers as $computerid )
                    {

                        if( $computers->computerExists( $computerid ) )
                        {

                            $current_computer = $computers->getComputer( $computerid );

                            ?>
                                <li class="list-group-item">
                                    <a href="/game/internet/<?=$current_computer->ipaddress?>/">
                                        <?php

                                            if( $npc->hasSchema( $computerid ) )
                                            {

                                                $schema = $npc->getSchema( $computerid );

                                                if( isset( $schema['name'] ))
                                                {

                                                    echo $schema['name'];
                                                }
                                            }
                                            else
                                            {

                                                echo ( $current_computer->ipaddress );
                                            }
                                        ?>
                                    </a>
                                </li>
                            <?php
                        }
                        else
                        {

                            ?>
                                <li class="list-group-item">
                                    Computer offline, try again later?
                                </li>
                            <?php
                        }
                    }
                }
                else
                {

                    ?>
                        <li class="list-group-item">
                            No links available today, sorry!
                        </li>
                    <?php
                }
            ?>
        </ul>
    </div>
</div>