<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-body text-center">
				Welcome to Syscrack. This is probably the first web-page you are seeing on the internet right now. A good
				idea is to start clicking around, maybe you'll stumble across something useful?
			</div>
		</div>
	</div>
</div>
<div class="row">

		<?php
			if( isset( $whois_computers ) )
				foreach( $whois_computers as $computer )
				{

					$metadata = $metaset[ $computer->computerid ]
					?>
						<div class="col-xs-6 col-md-3">
							<div class="thumbnail">
								<div class="caption text-center">
								<small><?php if( isset( $metadata->custom["name"] ) ) echo $metadata->custom["name"]; else echo $metadata->name; ?> </small>
								<p><a href="/game/internet/<?= @$computer->ipaddress?>"><?= @$computer->ipaddress?></a></p>
								</div>
							</div>
						</div>
					<?php
				}
		?>
</div>