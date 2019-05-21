<?php
	$render = function( &$contents, $page ) use( $settings )
	{

		foreach( $contents as $key=>$error )
		{

		    if( isset( $_SESSION["form"]["drawn"] ) == false )
		        $_SESSION["form"]["drawn"] = [];

		    $_SESSION["form"]["drawn"][ $page ] = ["key" => $key, "modified" => time ()];

			if( isset( $error["success"] ) == true && $error["success"] )
			{
				?>
                <div class="row">
                    <div class="col-sm-12">
                        <div id="error<?=$key?>" class="alert alert-success" role="alert">
                            <button type="button" style="padding-left: 5px;" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <small>
                                <span class="glyphicon glyphicon-star"></span> Message:
                            </small>
							<?php

								if( empty( @$error["message"] ) )
								{
									echo $settings["alert_success_message"];
								}
								else
									echo @$error["message"];?>
                                <span style="float: right;">
                                    <span class="glyphicon glyphicon-chevron-right"></span> <a href="/<?=$page?>"><?=$page?></a>
                                </span>
                        </div>
                    </div>
                </div>
				<?php
			}
			else
			{

				?>
                <div class="row">
                    <div class="col-sm-12">
                        <div id="error<?=$key?>" class="alert alert-danger" role="alert">
                            <button type="button" style="padding-left: 5px;" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <small>
                                <span class="glyphicon glyphicon-exclamation-sign"></span> Process response:
                            </small>
                            <b>
	                            <?php
		                            if( empty( @$error["message"] ) )
		                            {
			                            echo "Error";
		                            }
		                            else
			                            echo @$error["message"];?>
                            </b>
                            <span style="float: right;">
                                <span class="glyphicon glyphicon-chevron-right"></span> <a href="/<?=$page?>"><?=$page?></a>
                            </span>
                        </div>
                    </div>
                </div>
				<?php
			}

		}
	};

	if( isset( $_SESSION["form"] ) == false || empty( $_SESSION["form"] ) )
		return;

	if( isset( $page ) == false )
    {

	    foreach( $_SESSION["form"] as $page=>$contents )
	        if( $page !== "drawn")
	            $render( $_SESSION["form"][ $page ], $page );
    }
    else
        if( isset( $_SESSION["form"][ $page ] ) )
	        $render( $_SESSION["form"][ $page ],$page );

?>
