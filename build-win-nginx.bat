@echo off
echo "Copying meta files"
copy %cd%\build\meta\ %cd%\
echo "Opening config file for Nginx"
start notepad "%cd%\build\config\nginx.conf.windows"
echo "Building Composer Dependencies..."
composer install
echo "Done!"
PAUSE >nul