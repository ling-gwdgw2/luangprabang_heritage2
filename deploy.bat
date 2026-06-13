@echo off
title Deploy to GitHub and Railway
echo ============================================================
echo   Syncing changes to GitHub and Deploying to Railway...
echo ============================================================

:: Check Git
where git >nul 2>nul
if %errorlevel% neq 0 (
    echo [ERROR] Git is not installed or not in PATH.
    pause
    exit /b
)

:: Ask for Commit Message
set "commit_msg="
set /p commit_msg="Enter commit message (Press Enter for default): "
if "%commit_msg%"=="" (
    set commit_msg=Update system configuration and features
)

echo.
echo --> 1. Pushing to GitHub...
git add .
git commit -m "%commit_msg%"
git push origin main
if %errorlevel% neq 0 (
    echo [ERROR] Failed to push to GitHub.
    pause
    exit /b
)
echo [SUCCESS] Pushed to GitHub successfully!

:: Check Railway CLI
where railway >nul 2>nul
if %errorlevel% neq 0 (
    echo [WARNING] Railway CLI not found in PATH. Skipping Railway deployment.
    echo Make sure you configure GitHub auto-deployment on Railway instead.
    pause
    exit /b
)

echo.
echo --> 2. Deploying to Railway...
railway up
if %errorlevel% neq 0 (
    echo [ERROR] Failed to deploy to Railway.
    pause
    exit /b
)
echo [SUCCESS] Deployed to Railway successfully!
pause
