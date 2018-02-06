@echo off
echo "Copying meta files"
copy %cd%\meta\ %cd%\.\
echo "Opening config file for Nginx"
start notepad "%cd%\config\nginx.conf.windows"
echo "Building Composer Dependencies..."
composer install
echo "Deleting build directory"
del /s /q %cd%\.\build\
echo "Done!"
PAUSE >nul