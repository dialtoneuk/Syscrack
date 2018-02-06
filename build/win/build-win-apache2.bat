@echo off
echo "Copying meta files"
copy %cd%\meta\ %cd%\.\
echo "Creating config file for Apache2"
copy %cd%\config\htaccess.windows %cd%\.\.htaccess
echo "Building Composer Dependencies..."
composer install
echo "Deleting build directory"
del /s /q %cd%\.\build\
echo "Done!"
PAUSE >nul