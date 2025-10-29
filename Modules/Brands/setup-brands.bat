@echo off
echo ================================================
echo    Brands Module Setup Script
echo ================================================
echo.

echo Step 1: Running composer dump-autoload...
composer dump-autoload
echo.

echo Step 2: Running migrations...
php artisan migrate
echo.

echo Step 3: Clearing caches...
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
echo.

echo ================================================
echo    Setup Complete!
echo ================================================
echo.
echo Next steps:
echo 1. Copy view files from CategoryManagment module
echo 2. Update navigation menu
echo 3. Visit /admin/brands to test
echo.
pause
