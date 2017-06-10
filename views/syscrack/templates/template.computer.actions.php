<?php

    use Framework\Application\Settings;
    use Framework\Views\Pages\Computer;

    if( isset( $computers) == false )
    {

        $computers= new Computer();
    }

    $currentcomputer = $computers->getComputer( $computers->getCurrentUserComputer() );
?>
<div class="col-md-4">
    <div class="list-group">
        <a href="/computer/" class="list-group-item">
            <h4 class="list-group-item-heading">Desktop</h4>
            <p class="list-group-item-text">View your current softwares.</p>
        </a>
        <a href="/computer/log/" class="list-group-item">
            <h4 class="list-group-item-heading">Log</h4>
            <p class="list-group-item-text">View your computers system log.</p>
        </a>
        <a href="/computer/processes/" class="list-group-item">
            <h4 class="list-group-item-heading">Processes</h4>
            <p class="list-group-item-text">View whats current hogging your processor.</p>
        </a>
        <a href="/computer/hardware/" class="list-group-item">
            <h4 class="list-group-item-heading">Hardware</h4>
            <p class="list-group-item-text">View your system hardware.</p>
        </a>
    </div>
    <?php

        if( $computers->hasType( $currentcomputer->computerid, Settings::getSetting('syscrack_software_collector_type'), true ) )
        {

            ?>
                <div class="list-group">
                    <a href="/computer/collect/" class="list-group-item list-group-item-info">
                        <h4 class="list-group-item-heading">Collect</h4>
                        <p class="list-group-item-text">Use this computer to collect your profits from all of your viruses.</p>
                    </a>
                </div>
            <?php
        }
    ?>
    <?php

        if( $computers->hasType( $currentcomputer->computerid, Settings::getSetting('syscrack_software_research_type'), true ) )
        {

            ?>
                <div class="list-group">
                    <a href="/computer/research/" class="list-group-item list-group-item-info">
                        <h4 class="list-group-item-heading">Research</h4>
                        <p class="list-group-item-text">Research new and exciting software.</p>
                    </a>
                </div>
            <?php
        }
    ?>
</div>