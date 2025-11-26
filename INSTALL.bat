@echo off
echo ======================================
echo HP SNEAKERS - AUTO INSTALLATION
echo ======================================
echo.

REM Check if composer exists
where composer >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] Composer not found! Please install Composer first.
    echo Download: https://getcomposer.org/download/
    pause
    exit /b 1
)

REM Check if npm exists
where npm >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] NPM not found! Please install Node.js first.
    echo Download: https://nodejs.org/
    pause
    exit /b 1
)

echo [1/8] Installing PHP dependencies...
call composer install
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] Composer install failed!
    pause
    exit /b 1
)

echo.
echo [2/8] Installing Node dependencies...
call npm install
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] NPM install failed!
    pause
    exit /b 1
)

echo.
echo [3/8] Creating .env file...
if not exist .env (
    copy .env.example .env
    echo .env file created!
) else (
    echo .env already exists, skipping...
)

echo.
echo [4/8] Generating application key...
call php artisan key:generate

echo.
echo [5/8] Please configure your database in .env file
echo Default settings:
echo   DB_DATABASE=sneakershop
echo   DB_USERNAME=root
echo   DB_PASSWORD=
echo.
set /p continue="Press Enter after configuring .env file..."

echo.
echo [6/8] Creating database (if needed)...
mysql -u root -e "CREATE DATABASE IF NOT EXISTS sneakershop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
if %ERRORLEVEL% NEQ 0 (
    echo [WARNING] Could not create database automatically.
    echo Please create database 'sneakershop' manually in phpMyAdmin.
    set /p continue="Press Enter after creating database..."
)

echo.
echo [7/8] Running migrations and seeders...
call php artisan migrate:fresh --seed
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] Migration failed! Please check your database connection.
    pause
    exit /b 1
)

echo.
echo [8/8] Building assets...
call npm run build

echo.
echo ======================================
echo INSTALLATION COMPLETED!
echo ======================================
echo.
echo Next steps:
echo 1. Open TWO terminals/command prompts
echo 2. Terminal 1: npm run dev
echo 3. Terminal 2: php artisan serve
echo 4. Visit: http://127.0.0.1:8000
echo.
echo Press any key to start development servers...
pause >nul

REM Start both servers
start cmd /k "npm run dev"
timeout /t 3 >nul
start cmd /k "php artisan serve"

echo.
echo Servers are starting...
echo Check the new terminal windows!
echo.
pause
