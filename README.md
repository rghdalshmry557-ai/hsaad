# Hassad PHP Project

## نظرة عامة - Overview
**Hassad** هو نظام إدارة صناديق استثمارية متكامل مصمم للجهات التعليمية. يتيح النظام للطلاب الاستثمار في صناديق متنوعة تحت إشراف مديري الصناديق والجهة الراعية (JIC).

Built with:
- HTML5 & CSS3
- JavaScript (ES6+)
- PHP 8+ (Strict Types)
- MySQL via PDO
- Bootstrap-inspired responsive design

## أدوار المستخدمين - User Roles

### 1. Student (الطالب)
- التسجيل وإنشاء الحساب
- البحث عن الصناديق الاستثمارية
- الاشتراك في الصناديق
- إدارة المحفظة الاستثمارية
- سحب الأرباح
- الحظر الذاتي للحماية
- ربط حساب تيليجرام للإشعارات

### 2. Fund Manager (مدير الصندوق)
- إنشاء ونشر صناديق استثمارية
- تعديل وإخفاء الصناديق
- توزيع العوائد على المستثمرين
- التحقق الثنائي للعمليات الحساسة
- عرض التقارير والإحصائيات

### 3. Admin (المدير الإداري)
- إدارة حسابات المستخدمين
- حظر وإلغاء حظر المستخدمين
- إرسال إشعارات SMS
- تحديث إعدادات النظام
- إنشاء وعرض التقارير
- مراقبة النشاط والتدقيق

### 4. JIC Sponsor (الجهة الراعية)
- مراجعة طلبات مشاركة الطلاب
- الموافقة أو رفض طلبات الاستثمار
- مراقبة خصومات المكافآت
- تفويض الحسابات

## الصفحات المنفذة - Implemented Pages

### صفحات عامة - Public Pages
- `home.php` - الصفحة الرئيسية
- `register-student.php` - تسجيل طالب
- `register-manager.php` - تسجيل مدير صندوق
- `account-verified.php` - تأكيد التحقق
- `login.php` - تسجيل الدخول
- `terms.php` - الشروط والأحكام

### صفحات الطالب - Student Pages
- `student-dashboard.php` - لوحة تحكم الطالب
- `fund.php` - تفاصيل الصندوق
- `subscribe.php` - الاشتراك في صندوق
- `wallet.php` - المحفظة الاستثمارية
- `withdraw.php` - طلب سحب الأرباح
- `search-funds.php` - البحث عن الصناديق
- `self-ban.php` - الحظر الذاتي
- `bank-cards.php` - إدارة البطاقات البنكية
- `edit-profile.php` - تعديل الملف الشخصي

### صفحات مدير الصندوق - Manager Pages
- `manager-dashboard.php` - لوحة تحكم المدير
- `create-fund.php` - إنشاء صندوق جديد
- `publish-fund.php` - نشر الصندوق
- `edit-fund.php` - تعديل الصندوق
- `verify-fund-operation.php` - التحقق الثنائي

### صفحات Admin - Admin Pages
- `admin-dashboard.php` - لوحة تحكم المدير
- `system-settings.php` - إعدادات النظام
- `send-notification.php` - إرسال الإشعارات

### صفحات JIC - JIC Pages
- `jic-participants.php` - موافقات المشاركة

### صفحات التقارير - Report Pages
- `reports.php` - التقارير
- `manager-reports.php` - تقارير المدير
- `investment-report.php` - تقرير الاستثمارات

## ربط قاعدة البيانات - Database Setup
الربط موجود داخل:
- `config/app.php`
- `config/database.php`

### إعداد MySQL
1. أنشئ قاعدة بيانات باسم `hassad`
2. نفذ الملف `database/schema.sql`
3. نفذ الملف `database/seed.sql`
4. عدل بيانات الاتصال داخل `config/app.php` إذا لزم

### جداول قاعدة البيانات
- `User` - المستخدمين
- `Student` - الطلاب
- `FundManager` - مديري الصناديق
- `InvestmentFund` - الصناديق الاستثمارية
- `FundParticipation` - مشاركات الصناديق
- `BankAccount` - الحسابات البنكية
- `Notification` - الإشعارات
- `UserBan` - حظر المستخدمين
- `WithdrawalRequest` - طلبات السحب
- `ReturnDistribution` - توزيع العوائد
- `ParticipationApproval` - موافقات المشاركة
- `StudentDeduction` - خصومات الطلاب
- `FundVerification` - التحقق الثنائي
- `SystemSettings` - إعدادات النظام
- `SmsLog` - سجل الرسائل
- `AuditLog` - سجل التدقيق
- `TermsAcceptance` - قبول الشروط


ضع المشروع داخل مجلد السيرفر المحلي مثل:
- `htdocs` في XAMPP
- `www` في WAMP

ثم افتح:
- `http://localhost/hassad/`

## الهيكل البرمجي - Project Structure

```
hassad/
├── index.php              # الراوتر الرئيسي
├── action.php             # معالج العمليات (POST)
├── logout.php             # تسجيل الخروج
├── config/
│   ├── app.php            # إعدادات التطبيق
│   └── database.php       # إعدادات قاعدة البيانات
├── includes/
│   ├── bootstrap.php      # تهيئة التطبيق
│   ├── functions.php      # الدوال المساعدة
│   └── repository.php     # طبقة البيانات
├── pages/                 # ملفات الصفحات
├── database/
│   ├── schema.sql         # هيكل قاعدة البيانات
│   └── seed.sql           # بيانات تجريبية
└── assets/
    ├── css/
    │   ├── styles.css     # التنسيق الرئيسي
    │   └── theme-colors.css
    ├── images/
    └── js/
        └── app.js         # JavaScript
```

## الدوال الرئيسية - Core Functions

### functions.php
- `e()` - تأمين المخرجات من XSS
- `route()` - بناء روابط الصفحات
- `action_url()` - بناء روابط الإجراءات
- `redirect_to()` - إعادة التوجيه
- `format_sar()` - تنسيق العملة
- `set_flash()` / `flash_message()` - رسائل Flash
- `require_student_login()` - التحقق من دخول الطالب
- `require_manager_login()` - التحقق من دخول المدير
- `require_admin_login()` - التحقق من دخول Admin
- `require_jic_login()` - التحقق من دخول JIC

### repository.php
- `current_student()` / `current_manager()` - المستخدم الحالي
- `all_funds()` / `fund_by_id()` - الصناديق
- `search_funds()` - البحث عن صناديق
- `subscribe_to_fund()` - الاشتراك في صندوق
- `cancel_fund_subscription()` - إلغاء الاشتراك
- `create_withdrawal_request()` - طلب سحب
- `distribute_returns()` - توزيع العوائد
- `ban_user()` / `unban_user()` - حظر المستخدمين
- `approve_participation()` / `reject_participation()` - موافقات JIC
- `send_sms_notification()` - إرسال SMS
- `log_audit()` - تسجيل التدقيق

## الأمان - Security Features
- CSRF Token Protection
- XSS Prevention via `e()` function
- Password Hashing (configurable)
- Two-Step Verification for sensitive operations
- Audit Logging
- User Ban System
- Session-based Authentication

## المميزات الإضافية - Additional Features
- 📱 SMS Notifications
- 🛡️ Self-Ban Protection
- 📊 Comprehensive Reporting
- 💰 Profit Distribution System
- 🔐 Two-Factor Verification
- 📋 Audit Trail

## الترخيص - License
هذا المشروع مخصص للأغراض التعليمية والتدريبية.
