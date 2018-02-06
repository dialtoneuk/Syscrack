@echo off
echo "Static meta files are omitted, please run a live build script when ready to launch"
echo "Replacing default settings..."
copy %cd%\config\developer.settings %cd%\.\conf\settings.json
echo "Opening config file for Nginx"
start notepad "%cd%\config\nginx.conf.windows"
echo "Building Composer Dependencies..."
composer install
echo "Done!"
PAUSE >nul