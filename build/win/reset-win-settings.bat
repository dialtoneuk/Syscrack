@echo off
echo "Reloading settings to default..."
copy %cd%\config\default.settings %cd%\.\conf\settings.json
echo "Done! Press any key to move on with your life..."
PAUSE >nul