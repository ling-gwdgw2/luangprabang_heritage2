@echo off
title Luang Prabang Heritage - Local Server
echo ============================================================
echo   Starting Luang Prabang Heritage Local Server...
echo ============================================================

:: 1. Search for PHP
set PHP_PATH=php
where php >nul 2>nul
if %errorlevel% neq 0 (
    set PHP_PATH=
    :: Try Laragon D:
    for /d %%i in (D:\laragon\bin\php\php-*) do (
        if exist "%%i\php.exe" set PHP_PATH="%%i\php.exe"
    )
    :: Try Laragon C:
    for /d %%i in (C:\laragon\bin\php\php-*) do (
        if exist "%%i\php.exe" set PHP_PATH="%%i\php.exe"
    )
    :: Try XAMPP C:
    if exist "C:\xampp\php\php.exe" set PHP_PATH="C:\xampp\php\php.exe"
)

if "%PHP_PATH%"=="" (
    echo [ERROR] PHP is not found on your system.
    echo Please install Laragon, XAMPP, or add PHP to your PATH.
    pause
    exit /b
)

:: 2. Search for MySQL (mysqld)
set MYSQL_PATH=mysqld
where mysqld >nul 2>nul
if %errorlevel% neq 0 (
    set MYSQL_PATH=
    :: Try Laragon D:
    for /d %%i in (D:\laragon\bin\mysql\mysql-*) do (
        if exist "%%i\bin\mysqld.exe" set MYSQL_PATH="%%i\bin\mysqld.exe"
    )
    :: Try Laragon C:
    for /d %%i in (C:\laragon\bin\mysql\mysql-*) do (
        if exist "%%i\bin\mysqld.exe" set MYSQL_PATH="%%i\bin\mysqld.exe"
    )
    :: Try XAMPP C:
    if exist "C:\xampp\mysql\bin\mysqld.exe" set MYSQL_PATH="C:\xampp\mysql\bin\mysqld.exe"
)

:: 3. Start MySQL if found and not running
tasklist /fi "imagename eq mysqld.exe" | find /i "mysqld.exe" >nul
if %errorlevel% neq 0 (
    if not "%MYSQL_PATH%"=="" (
        echo [INFO] Starting MySQL database server in background...
        start /b "" %MYSQL_PATH% --console
        ping 127.0.0.1 -n 4 >nul
    ) else (
        echo [WARNING] MySQL server not found. If your database is not running, the website will show connection errors.
    )
) else (
    echo [INFO] MySQL database server is already running.
)

:: 4. Start PHP Server and Open Browser
echo [INFO] Using PHP: %PHP_PATH%
echo [INFO] Starting Web Server at http://localhost:8000 ...
start "" "http://localhost:8000"
%PHP_PATH% -S localhost:8000
pause
