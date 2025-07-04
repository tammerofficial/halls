# 🚀 تعليمات التثبيت والتشغيل

## المتطلبات الأساسية

### 1. متطلبات النظام
```bash
✅ PHP >= 8.2
✅ Composer
✅ Node.js >= 18
✅ NPM أو Yarn
✅ MySQL >= 8.0
✅ XAMPP أو LAMP/WAMP
```

### 2. فحص المتطلبات
```bash
# فحص إصدار PHP
php -v

# فحص Composer
composer --version

# فحص Node.js
node --version

# فحص NPM
npm --version
```

## خطوات التثبيت

### الخطوة 1: تحميل المشروع
```bash
# إذا كان لديك Git
git clone https://github.com/your-username/halls-management.git
cd halls-management

# أو تحميل مباشر وفك الضغط
# ثم الانتقال إلى مجلد المشروع
cd halls-management
```

### الخطوة 2: إعداد Laravel Backend
```bash
# تثبيت حزم PHP
composer install

# إنشاء ملف البيئة
cp .env.example .env

# توليد مفتاح التطبيق
php artisan key:generate

# إنشاء رابط التخزين
php artisan storage:link
```

### الخطوة 3: إعداد قاعدة البيانات
```bash
# في XAMPP - تشغيل MySQL
# إنشاء قاعدة بيانات جديدة باسم: halls_db

# تحديث ملف .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=halls_db
DB_USERNAME=root
DB_PASSWORD=

# تشغيل الهجرات
php artisan migrate

# تشغيل البذور (اختياري)
php artisan db:seed
```

### الخطوة 4: إعداد Frontend
```bash
# تثبيت حزم JavaScript
npm install

# بناء الأصول للإنتاج
npm run build

# أو للتطوير (مع المراقبة)
npm run dev
```

### الخطوة 5: تشغيل الخادم
```bash
# تشغيل خادم Laravel
php artisan serve

# الآن يمكنك الوصول للتطبيق على:
# http://localhost:8000
```

## إعداد متقدم

### تشغيل في بيئة التطوير
```bash
# في نافذة طرفية أولى
php artisan serve

# في نافذة طرفية ثانية
npm run dev

# في نافذة طرفية ثالثة (اختياري)
php artisan queue:work
```

### إعداد البريد الإلكتروني
```bash
# في ملف .env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
```

### إعداد التخزين السحابي (اختياري)
```bash
# في ملف .env
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your-access-key
AWS_SECRET_ACCESS_KEY=your-secret-key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket-name
```

## استكشاف الأخطاء

### مشاكل شائعة وحلولها

#### 1. خطأ "Class not found"
```bash
# إعادة تحميل Autoloader
composer dump-autoload

# مسح الكاش
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

#### 2. مشاكل قاعدة البيانات
```bash
# فحص الاتصال
php artisan tinker
>>> DB::connection()->getPdo();

# إعادة تشغيل الهجرات
php artisan migrate:fresh
```

#### 3. مشاكل الصلاحيات
```bash
# على Linux/Mac
sudo chown -R www-data:www-data storage/
sudo chown -R www-data:www-data bootstrap/cache/
sudo chmod -R 775 storage/
sudo chmod -R 775 bootstrap/cache/

# على Windows (في CMD كمسؤول)
icacls storage /grant Everyone:F /T
icacls bootstrap\cache /grant Everyone:F /T
```

#### 4. مشاكل Node.js
```bash
# حذف node_modules وإعادة التثبيت
rm -rf node_modules
rm package-lock.json
npm install

# أو استخدام yarn
yarn install
```

#### 5. مشاكل Vite
```bash
# مسح كاش Vite
rm -rf node_modules/.vite
npm run dev
```

## بيانات التجربة

### حساب المسؤول الافتراضي
```
البريد الإلكتروني: admin@halls.com
كلمة المرور: password123
```

### بيانات تجريبية
```bash
# إضافة بيانات تجريبية
php artisan db:seed --class=DemoDataSeeder
```

## الأوامر المفيدة

### أوامر Laravel
```bash
# عرض جميع المسارات
php artisan route:list

# عرض معلومات النظام
php artisan about

# تشغيل الاختبارات
php artisan test

# إنشاء مفتاح جديد
php artisan key:generate

# تحسين الأداء
php artisan optimize
```

### أوامر NPM
```bash
# تطوير مع المراقبة
npm run dev

# بناء للإنتاج
npm run build

# فحص الأخطاء
npm run lint

# إصلاح الأخطاء
npm run lint:fix
```

## نصائح للأداء

### 1. تحسين Laravel
```bash
# تحسين الكاش
php artisan config:cache
php artisan route:cache
php artisan view:cache

# تحسين Composer
composer install --optimize-autoloader --no-dev
```

### 2. تحسين قاعدة البيانات
```sql
-- إضافة فهارس للبحث السريع
CREATE INDEX idx_bookings_date ON bookings(event_date);
CREATE INDEX idx_customers_email ON customers(email);
CREATE INDEX idx_halls_status ON halls(is_available);
```

### 3. تحسين Frontend
```bash
# ضغط الصور
npm install imagemin-cli -g
imagemin public/images/* --out-dir=public/images/compressed

# تحليل حجم الحزم
npm run build -- --analyze
```

## النسخ الاحتياطي

### نسخ احتياطي لقاعدة البيانات
```bash
# تصدير قاعدة البيانات
mysqldump -u root -p halls_db > backup.sql

# استيراد قاعدة البيانات
mysql -u root -p halls_db < backup.sql
```

### نسخ احتياطي للملفات
```bash
# ضغط المشروع
tar -czf halls-backup-$(date +%Y%m%d).tar.gz halls-management/

# أو باستخدام zip
zip -r halls-backup-$(date +%Y%m%d).zip halls-management/
```

## التحديث

### تحديث Laravel
```bash
# تحديث الحزم
composer update

# تشغيل الهجرات الجديدة
php artisan migrate

# مسح الكاش
php artisan cache:clear
```

### تحديث Frontend
```bash
# تحديث حزم JavaScript
npm update

# إعادة بناء الأصول
npm run build
```

## الدعم

### روابط مفيدة
- [Laravel Documentation](https://laravel.com/docs)
- [Vue.js Documentation](https://vuejs.org/guide/)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)

### الحصول على المساعدة
- إنشاء Issue على GitHub
- مراجعة الـ Logs في `storage/logs/`
- فحص Browser Console للأخطاء

---

**🎉 تهانينا! النظام جاهز للاستخدام**

قم بزيارة `http://localhost:8000` للبدء في استخدام النظام. 