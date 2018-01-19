@echo off
echo "Static meta files are omitted, please run a live build script when ready to launch"
echo "Replacing default settings..."
copy %cd%\build\config\developer.settings %cd%\conf\settings.json
echo "Opening config file for Nginx"
start notepad "%cd%\build\config\nginx.conf.windows"
echo "Building Composer Dependencies..."
composer install
echo "Done!"
PAUSE >nul