<?php

/**
* Lewis Lancaster 2017
*
* Class DatabaseMigrator
*/

use Framework\Database\Manager as Database;
use Framework\Exceptions\ViewException;
use Illuminate\Database\Schema\Blueprint;
use Framework\Application\Utilities\PostHelper;

class DatabaseMigrator
{

    /**
     * @var Database
     */

    protected $database;

    /**
     * DatabaseMigrator constructor.
     */

    public function __construct()
    {

        $this->database = new Database();
    }

    /**
     * Parses the json
     *
     * @param $json
     *
     * @return mixed
     */

    public function parseJson( $json )
    {

        $array = json_decode( $json, true );

        if( json_last_error() != JSON_ERROR_NONE )
        {

            Flight::redirect('/developer/databasemigrator?error=Json invalid');

            exit;
        }

        return $array;
    }

    /**
     * Returns false if a table doesn't exist
     *
     * @param $table
     *
     * @return bool
     */

    public function tableExists( $table )
    {

        try
        {

            Database::$capsule->getConnection()->table( strtolower( $table ) )->get();
        }
        catch( Exception $error )
        {

            return false;
        }

        return true;
    }

    /**
     * Processes our payload
     *
     * @param $array
     */

    public function process( $array )
    {

        foreach( $array as $table=>$columns )
        {

            if( $this->tableExists( $table )  )
            {

                throw new ViewException('Table already exists');
            }

            Database::$capsule->getConnection()->getSchemaBuilder()->create( $table, function( Blueprint $table ) use ( $columns )
            {

                foreach( $columns as $column=>$type )
                {

                    $table->{ $type }( $column );
                }
            });
        }
    }
}

$class = new DatabaseMigrator();
?>
<html lang="en">

    <?php

        Flight::render('developer/templates/template.header', array( 'pagetitle' => 'Database Migrator'));
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
                        <h1>DB Migrator</h1>
                    </div>

                    <p class="lead">
                        This tool is used to migrate your database. This is used to create tables and data automatically in your
                        sql or equivilent database.
                    </p>

                    <p>
                        To use this, insert a valid parasable json to the right and press the blue button. You will need to make sure
                        that your JSON follows the schema of the migrator, you can view the schema <a href="/">at our github.</a>
                    </p>

                    <h5 style="color: #ababab" class="text-uppercase">
                        Example
                    </h5>

                    <div class="well">
                        <?= json_encode( array(
                            'example' => [
                                    'exampleid' => 'increments',
                                    'message' => 'text'
                            ]
                        ), JSON_PRETTY_PRINT ) ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="page-header">
                        <h1>Schema</h1>
                    </div>
                    <form method="post">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <textarea style='resize: none; width: 100%; height: 42.5%;' name="json"></textarea>
                            </div>
                        </div>
                        <button class="btn btn-primary btn-block btn-lg" data-toggle="modal" data-target="#disablemodal">
                            Migrate Database
                        </button>
                    </form>
                </div>
            </div>

            <?php

                Flight::render('developer/templates/template.footer', array( 'breadcrumb' => true ));
            ?>
        </div>
    </body>
</html>

<?php

if( $_POST )
    if( PostHelper::checkPostData(['json'] ) )
    {

        $json = PostHelper::getPostData('json');

        if( empty( $json ) )
        {

            Flight::redirect('/developer/databasemigrator?error=Json empty');

            exit;
        }

        $array = $class->parseJson( $json );

        try
        {

            $class->process( $array );
        }
        catch( Exception $error )
        {

            Flight::redirect('/developer/databasemigrator?error=' . $error->getMessage() );
        }


        Flight::redirect('/developer/databasemigrator?success');
    }