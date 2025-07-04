@echo off
chcp 65001 >nul
cls

echo 🏛️  بدء تشغيل نظام إدارة قاعات الأفراح...
echo.

REM التحقق من وجود PHP
php -v >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ PHP غير مثبت. يرجى تثبيت PHP 8.2 أو أحدث
    pause
    exit /b 1
)

REM التحقق من وجود Composer
composer --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Composer غير مثبت. يرجى تثبيت Composer
    pause
    exit /b 1
)

REM التحقق من وجود Node.js
node --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Node.js غير مثبت. يرجى تثبيت Node.js 18 أو أحدث
    pause
    exit /b 1
)

REM التحقق من وجود NPM
npm --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ NPM غير مثبت. يرجى تثبيت NPM
    pause
    exit /b 1
)

echo ✅ جميع المتطلبات متوفرة
echo.

REM تثبيت حزم PHP إذا لم تكن موجودة
if not exist "vendor" (
    echo 📦 تثبيت حزم PHP...
    composer install
    echo.
)

REM تثبيت حزم JavaScript إذا لم تكن موجودة
if not exist "node_modules" (
    echo 📦 تثبيت حزم JavaScript...
    npm install
    echo.
)

REM إنشاء ملف .env إذا لم يكن موجود
if not exist ".env" (
    echo ⚙️  إنشاء ملف البيئة...
    copy .env.example .env >nul
    php artisan key:generate
    echo.
)

REM التحقق من اتصال قاعدة البيانات
echo 🔍 فحص قاعدة البيانات...
php artisan migrate:status >nul 2>&1
if %errorlevel% neq 0 (
    echo 📊 تشغيل هجرات قاعدة البيانات...
    php artisan migrate --force
    echo.
)

REM بناء الأصول
echo 🔨 بناء الأصول...
npm run build
echo.

REM تشغيل الخادم
echo 🚀 تشغيل الخادم...
echo 📱 يمكنك الوصول للتطبيق على: http://localhost:8000
echo ⏹️  للإيقاف: اضغط Ctrl+C في كلا النافذتين
echo.

REM تشغيل Laravel و Vite في نوافذ منفصلة
start "Laravel Server" cmd /k "php artisan serve"
timeout /t 3 /nobreak >nul
start "Vite Dev Server" cmd /k "npm run dev"

echo ✅ تم تشغيل النظام بنجاح!
echo.
echo 🌐 افتح المتصفح وانتقل إلى: http://localhost:8000
echo.
pause 