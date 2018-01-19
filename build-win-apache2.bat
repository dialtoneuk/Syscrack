@echo off
echo "Copying meta files"
copy %cd%\build\meta\ %cd%\
echo "Creating config file for Apache2"
copy %cd%\build\config\htaccess.windows %cd%\.htaccess
echo "Building Composer Dependencies..."
composer install
echo "Done!"
PAUSE >nul