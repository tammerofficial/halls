# نظام المستأجرين - نظام إدارة قاعات الأفراح 🏛️

## نظرة عامة 📋
هذا النظام يدعم إنشاء حسابات للمستأجرين مع قواعد بيانات منفصلة لكل مستأجر. يتم إنشاء قاعدة بيانات جديدة تلقائياً عند تسجيل مستأجر جديد.

## نظام تسمية قواعد البيانات 🗄️
- **النمط الجديد**: `halls_` + اسم المشروع
- **مثال**: إذا كان البريد الإلكتروني `ahmed@company.com`، ستكون قاعدة البيانات `halls_ahmedcompanycom`

## المسارات (Routes) 🔄

### صفحات الويب
```php
// صفحة تسجيل الدخول
GET /login

// صفحة التسجيل الجديد
GET /register

// لوحة التحكم (تتطلب تسجيل دخول)
GET /dashboard
```

### عمليات المصادقة
```php
// تسجيل الدخول
POST /login

// تسجيل الخروج
POST /logout

// تسجيل مستأجر جديد
POST /register
```

## قاعدة البيانات 💾

### جدول المستخدمين (staff_users)
```sql
CREATE TABLE staff_users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL DEFAULT 'staff',
    is_active BOOLEAN NOT NULL DEFAULT true,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### جدول طلبات المستأجرين (tenant_requests)
```sql
CREATE TABLE tenant_requests (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    db_name VARCHAR(255) NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES staff_users (id) ON DELETE CASCADE
);
```

## آلية إنشاء قواعد البيانات للمستأجرين ⚙️

### 1. تسجيل المستأجر
عند تسجيل مستأجر جديد، يتم:
- إنشاء مستخدم جديد في جدول `staff_users`
- إنشاء سجل في جدول `tenant_requests` يحتوي على معلومات قاعدة البيانات المطلوب إنشاؤها

### 2. معالجة الطلبات (الكرون جوب)
- يتم تشغيل الأمر `tenants:process-requests` كل دقيقة
- يقوم الأمر بقراءة الطلبات المعلقة من جدول `tenant_requests`
- إنشاء قاعدة بيانات جديدة لكل طلب باستخدام النمط `halls_*`
- تحديث حالة الطلب إلى `completed`

## الأوامر (Commands) 🔧

### معالجة طلبات المستأجرين
```bash
php artisan tenants:process-requests
```

## الملفات الرئيسية 📁

### 1. تسجيل المستأجرين
- `app/Http/Controllers/Web/AuthController.php`: يحتوي على منطق تسجيل المستأجرين وإنشاء طلبات قواعد البيانات

### 2. معالجة الطلبات
- `app/Console/Commands/ProcessTenantRequests.php`: الأمر المسؤول عن إنشاء قواعد البيانات للمستأجرين

### 3. تسجيل الأوامر
- `app/Console/Kernel.php`: تسجيل الأمر للتشغيل التلقائي كل دقيقة

## ملاحظات هامة ⚠️
- يجب التأكد من وجود صلاحيات كافية لإنشاء قواعد بيانات جديدة
- يتم استخدام نظام التسمية `halls_` لتمييز قواعد بيانات المستأجرين
- يجب تشغيل الكرون جوب بشكل صحيح لضمان معالجة الطلبات 