<?php

/**
 * Lewis Lancaster 2017
 *
 * Class CreateDatabase
 */

    use Framework\Application\Settings;
    use Framework\Application\Utilities\Cyphers;
    use Framework\Application\Utilities\FileSystem;
    use Framework\Application\Utilities\PostHelper;
    use Framework\Exceptions\ViewException;

    class CreateDatabase
{

    public function createDatabaseSettings( $username, $password, $host, $database)
    {

        $array = array(
            'username'  => $username,
            'password'  => $password,
            'host'      => $host,
            'database'  => $database
        );

        $array = array_merge( $array, $this->driverSettings() );

        FileSystem::write( Settings::getSetting('database_connection_file'), Cyphers::encryptToJson( $array ) );

        return true;
    }

    private function driverSettings()
    {

        return array(
            'driver'    =>  Settings::getSetting('database_driver'),
            'charset'   =>  Settings::getSetting('database_charset'),
            'collation' =>  Settings::getSetting('database_collation'),
            'prefix'    =>  Settings::getSetting('database_prefix')
        );
    }
}

$class = new CreateDatabase();
?>

<html lang="en">

    <?php

        Flight::render('developer/templates/template.header', array( 'pagetitle' => 'Connection Creator'));
    ?>
    <body>
        <div class="container">

            <?php

                Flight::render('developer/templates/template.navigation');

                if( isset( $_GET['error'] ) )
                    Flight::render('developer/templates/template.alert', array( 'message' => $_GET['error'] ) );
                elseif( isset( $_GET['success'] ) )
                    Flight::render('developer/templates/template.alert', array( 'message' => 'Success', 'alert_type' => 'alert-success' ) );
            ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="page-header">
                        <h1>DB Connection Creator</h1>
                    </div>

                    <p class="lead">
                        This tool is used to create a new database connection file which is read by the framework
                        when accessing your associated database. This file is encrypted by default for added
                        security.
                    </p>

                    <p>
                        To use this, enter your associated database <b>username, password, host and database</b> into the
                        associated boxed, and then press submit. An error will appear if the tool has been unsuccessful.
                    </p>

                    <p>
                        If you would like to test your connection, <a href="/developer/connectiontester/">head to the <b>Connection Tester</b> tool.</a>
                    </p>
                </div>
                <div class="col-md-6">
                    <div class="page-header">
                        <h1>Enter your settings</h1>
                    </div>

                    <?php

                        Flight::render('developer/templates/template.form', array('form_elements' => [
                            [
                                'type'          => 'text',
                                'name'          => 'username',
                                'placeholder'   => 'DB Username'
                            ],
                            [
                                'type'          => 'password',
                                'name'          => 'password',
                                'placeholder'   => 'DB Password'
                            ],
                            [
                                'type'          => 'text',
                                'name'          => 'host',
                                'placeholder'   => 'DB Host'
                            ],
                            [
                                'type'          => 'text',
                                'name'          => 'database',
                                'placeholder'   => 'DB Database'
                            ]
                        ]));
                    ?>
                </div>
            </div>

            <?php

                Flight::render('developer/templates/template.footer', array( 'breadcrumb' => true ));
            ?>
        </div>
    </body>
</html>

<?php
if( PostHelper::hasPostData() == true )
{

    if( PostHelper::checkForRequirements( ['username','password','host','database'] ) == false )
    {

        Flight::redirect( '/developer/connectioncreator?error=Missing information, did you make sure to fill out all the boxes?');

        exit;
    }

    $data = PostHelper::returnRequirements( ['username','password','host','database'] );

    if( empty( $data ) )
    {

        throw new ViewException('oh');
    }

    try
    {

        $class->createDatabaseSettings( $data['username'], $data['password'], $data['host'], $data['database'] );
    }
    catch( RuntimeException $error )
    {

        Flight::redirect( '/developer/connectioncreator?error=' . $error->getMessage() );

        exit;
    }

    Flight::redirect( '/developer/connectioncreator?success' );
}
