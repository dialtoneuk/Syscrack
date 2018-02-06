@echo off
echo "Static meta files are omitted, please run a live build script when ready to launch"
echo "Replacing default settings..."
copy %cd%\config\developer.settings %cd%\.\conf\settings.json
echo "Creating config file for Apache2"
copy %cd%\config\htaccess.windows %cd%\.\.htaccess
echo "Building Composer Dependencies..."
composer install
echo "Done!"
PAUSE >nul