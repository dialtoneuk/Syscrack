<?php

/**
 * Lewis Lancaster 2017
 *
 * Class PageViewer
 */

    use Framework\Application\Container;
    use Framework\Application\Settings;
    use Framework\Application\Utilities\FileSystem;
    use Framework\Exceptions\ViewException;
    use Framework\Views\Structures\Page;

    class PageViewer
{

    /**
     * @var \Framework\Views\Controller
     */

    protected $controller;

    /**
     * PageViewer constructor.
     */

    public function __construct()
    {

        $this->controller = Container::getObject('application')->getController();
    }

    /**
     * Gets all the pages we currently have in the framework
     *
     * @return array
     */

    public function getPages()
    {

        $files = $this->removeExtensions( $this->getFiles() );

        $files = $this->getFileNameOnly( $files );

        $pages = [];

        foreach( $files as $page )
        {

            $pages[ $page ] = $this->getMapping( $page );
        }

        return $pages;
    }

    /**
     * Gets the file name only
     *
     * @param $files
     *
     * @return array
     */

    private function getFileNameOnly( $files )
    {

        $result = [];

        foreach( $files as $file )
        {

            $exploded = explode('/', $file );

            if( empty( $exploded ) )
            {

                continue;
            }

            $result[] = end( $exploded );
        }

        return $result;
    }

    /**
     * Removes the extension from the file
     *
     * @param array $files
     *
     * @return array
     */

    private function removeExtensions( array $files )
    {

        $result = [];

        foreach( $files as $file )
        {

            $result[] = explode('.', $file )[0];
        }

        return $result;
    }

    /**
     * Gets this page classes mapping
     *
     * @param $page
     *
     * @return mixed
     */

    private function getMapping( $page )
    {

        $reflection = new ReflectionClass( Settings::getSetting('controller_namespace') . ucfirst( $page ) );

        $class = $reflection->newInstanceWithoutConstructor();

        if( $class instanceof Page == false )
        {

            throw new ViewException( $page . ' is invalid');
        }

        return $class->mapping();
    }

    /**
     * Gets the files in the directory of our page location
     *
     * @return array|null
     */

    private function getFiles()
    {

        return FileSystem::getFilesInDirectory( $this->pageLocation() );
    }

    /**
     * Gets the folder location of our pages
     *
     * @return mixed
     */

    private function pageLocation()
    {

        return Settings::getSetting('controller_page_folder');
    }
}

$class = new PageViewer();
?>
<html lang="en">

    <?php

        Flight::render('developer/templates/template.header', array( 'pagetitle' => 'Page Viewer'));
    ?>
    <body>
        <div class="container">

            <?php

                Flight::render('developer/templates/template.navigation');
            ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header">
                        <h3>Page Viewer</h3>
                    </div>

                    <p class="lead">
                        Here you can see a visual display of how the pages are currently routed. You should use this
                        page as a visual aid when adding new pages as well as testing their functionality.
                    </p>

                    <p>
                        While the framework is unedited, there should be at least 4 default pages.
                        It is suggested that <strong>you do not delete these files</strong>. Try and learn from example
                        and try and understand how the developer area functions via looking at its mapping.
                    </p>
                </div>
            </div>
            <div class="row">

                <?php

                    $pages = $class->getPages();

                    if( empty( $pages ) )
                    {

                        echo 'No pages to display, this is probably an error...';
                    }

                    foreach( $pages as $page=>$mapping )
                    {

                        ?>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><?=$page?> <span class="small"><a href="/<?=strtolower($page)?>/">Visit</a></span></h3>
                            </div>

                                <div class="panel-body">
                                    <div class="well">
                                         <pre>
 <?=json_encode( $mapping , JSON_PRETTY_PRINT )?>
                                         </pre>
                                    </div>
                                </div>
                            </div>
                        <?php
                    }
                ?>
            </div>

            <?php

                Flight::render('developer/templates/template.footer', array( 'breadcrumb' => true ));
            ?>
        </div>
    </body>
</html>
