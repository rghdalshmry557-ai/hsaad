# التوثيق التقني التفصيلي (عربي)

هذا الملف يشرح:
- وظيفة كل صفحة في النظام (نبذة واضحة).
- كل دالة مستدعاة داخل الصفحات: لماذا تم استدعاؤها؟ وما القيمة الراجعة/الأثر؟

ملاحظة:
- التغطية هنا مبنية على الاستدعاءات الفعلية داخل ملفات `pages/*.php`.
- توثيق جميع تعريفات الدوال موجود أيضًا في `FUNCTIONS_AR_DOC.md`.

---

## 1) قاموس الدوال المشتركة (السبب + القيمة الراجعة)

### دوال التوجيه والعرض
- `route(page, params): string`
  - لماذا: إنشاء رابط داخلي آمن للانتقال بين الصفحات.
  - القيمة الراجعة: نص URL كامل.
- `action_url(action): string`
  - لماذا: إنشاء رابط POST إلى `action.php`.
  - القيمة الراجعة: نص URL للإجراء.
- `redirect_to(page, params): void`
  - لماذا: منع الاستمرار في الصفحة عندما الشرط الأمني/المنطقي غير متحقق.
  - القيمة الراجعة: لا شيء (تأثير جانبي: Header redirect + إنهاء التنفيذ).
- `render_head(title): void`
  - لماذا: فتح وثيقة HTML وتحميل CSS العام وCSS الخاص بالصفحة.
  - القيمة الراجعة: لا شيء (تطبع HTML).
- `render_responsive_shell_start(title, showBack): void`
  - لماذا: بدء هيكل الواجهة الموحد (header/sidebar/content).
  - القيمة الراجعة: لا شيء.
- `render_responsive_shell_end(): void`
  - لماذا: إغلاق هيكل الواجهة الموحد.
  - القيمة الراجعة: لا شيء.
- `render_footer(): void`
  - لماذا: إظهار Footer في الصفحات التي تحتاجه.
  - القيمة الراجعة: لا شيء.
- `render_end(): void`
  - لماذا: إغلاق الصفحة وتحميل JavaScript العام.
  - القيمة الراجعة: لا شيء.

### دوال الأمان والمصادقة
- `is_logged_in_as_student(): bool`
  - لماذا: فحص أن المستخدم الحالي طالب.
  - القيمة الراجعة: `true/false`.
- `is_logged_in_as_manager(): bool`
  - لماذا: فحص أن المستخدم مدير صندوق.
  - القيمة الراجعة: `true/false`.
- `is_logged_in_as_admin(): bool`
  - لماذا: فحص أن المستخدم إداري.
  - القيمة الراجعة: `true/false`.
- `is_logged_in_as_jic(): bool`
  - لماذا: فحص أن المستخدم جهة راعية JIC.
  - القيمة الراجعة: `true/false`.
- `require_student_login(): void`
  - لماذا: فرض دخول الطالب قبل متابعة الصفحة.
  - القيمة الراجعة: لا شيء (يعيد التوجيه عند الفشل).
- `require_manager_login(): void`
  - لماذا: فرض دخول المدير قبل متابعة الصفحة.
  - القيمة الراجعة: لا شيء (يعيد التوجيه عند الفشل).
- `set_flash(type, message): void`
  - لماذا: تخزين رسالة نجاح/خطأ مؤقتة للمستخدم.
  - القيمة الراجعة: لا شيء.

### دوال تنسيق/عرض
- `e(value): string`
  - لماذا: تعقيم النص قبل طباعته لمنع XSS.
  - القيمة الراجعة: نص آمن للطباعة.
- `format_sar(amount): string`
  - لماذا: تنسيق مبالغ SAR بشكل ثابت.
  - القيمة الراجعة: نص منسق.
- `mask_phone(phone): string`
  - لماذا: إخفاء جزء من رقم الهاتف.
  - القيمة الراجعة: نص مقنّع.
- `mask_card(card): string`
  - لماذا: إخفاء جزء من رقم البطاقة.
  - القيمة الراجعة: نص مقنّع.
- `capitalize(text): string`
  - لماذا: تحسين عرض النص (أول حرف كبير).
  - القيمة الراجعة: نص بعد المعالجة.

### دوال المستخدم الحالي والبيانات الأساسية
- `current_student(): array`
  - لماذا: جلب بيانات الطالب الحالي من الجلسة/قاعدة البيانات.
  - القيمة الراجعة: مصفوفة بيانات طالب.
- `current_manager(): array`
  - لماذا: جلب بيانات المدير الحالي.
  - القيمة الراجعة: مصفوفة بيانات مدير.
- `current_admin(): array`
  - لماذا: جلب بيانات الإداري الحالي.
  - القيمة الراجعة: مصفوفة بيانات إداري.
- `current_jic(): array`
  - لماذا: جلب بيانات JIC الحالي.
  - القيمة الراجعة: مصفوفة بيانات جهة راعية.
- `current_wallet(studentId?): array`
  - لماذا: جلب بيانات المحفظة للطالب.
  - القيمة الراجعة: مصفوفة محفظة.
- `current_bank_account(studentId?): array`
  - لماذا: جلب البطاقة/الحساب البنكي للطالب.
  - القيمة الراجعة: مصفوفة بيانات بنكية.

### دوال الصناديق والاستثمار
- `all_funds(publishedOnly=false): array`
  - لماذا: جلب قائمة الصناديق.
  - القيمة الراجعة: مصفوفة صناديق.
- `fund_by_id(id): ?array`
  - لماذا: جلب صندوق محدد بالمعرف.
  - القيمة الراجعة: صندوق واحد أو `null`.
- `fund_by_slug(slug): ?array`
  - لماذا: جلب صندوق من رابط SEO slug.
  - القيمة الراجعة: صندوق واحد أو `null`.
- `find_fund_by_id(id): ?array`
  - لماذا: نسخة بحث متوافقة في بعض الصفحات القديمة.
  - القيمة الراجعة: صندوق واحد أو `null`.
- `manager_funds(license): array`
  - لماذا: جلب صناديق مدير معين فقط.
  - القيمة الراجعة: مصفوفة صناديق.
- `subscribers_for_fund(fundId): array`
  - لماذا: جلب المشتركين بصندوق معين.
  - القيمة الراجعة: مصفوفة مشتركين.
- `latest_investment_amount(studentId, fundId): float`
  - لماذا: آخر مبلغ استثماري للطالب في الصندوق.
  - القيمة الراجعة: رقم عشري.
- `student_has_contract(studentId, fundId): bool`
  - لماذا: هل الطالب مشترك في الصندوق؟
  - القيمة الراجعة: `true/false`.
- `student_dashboard_cards(studentId): array`
  - لماذا: بناء بطاقات ملخص استثمار الطالب.
  - القيمة الراجعة: مصفوفة بطاقات.

### دوال العقود/العمليات/السحب
- `student_contracts(studentId): array`
  - لماذا: جلب عقود الطالب.
  - القيمة الراجعة: مصفوفة عقود.
- `contracts_for_student(studentId): array`
  - لماذا: عقود الطالب بصيغة عرض.
  - القيمة الراجعة: مصفوفة.
- `contracts_for_manager(license): array`
  - لماذا: عقود تخص مديرًا بعينه.
  - القيمة الراجعة: مصفوفة.
- `student_transactions(studentId): array`
  - لماذا: عمليات الطالب المالية.
  - القيمة الراجعة: مصفوفة عمليات.
- `all_transactions(): array`
  - لماذا: عمليات النظام كاملة (للوحات الإدارة).
  - القيمة الراجعة: مصفوفة.
- `all_withdrawal_requests(managerLicense?): array`
  - لماذا: طلبات السحب (لكل النظام أو حسب مدير).
  - القيمة الراجعة: مصفوفة.
- `student_withdrawal_requests(studentId): array`
  - لماذا: طلبات السحب الخاصة بالطالب.
  - القيمة الراجعة: مصفوفة.

### دوال الإدارة والإعدادات
- `all_students(): array`
  - لماذا: جلب قائمة الطلاب.
  - القيمة الراجعة: مصفوفة.
- `all_managers(): array`
  - لماذا: جلب قائمة المديرين.
  - القيمة الراجعة: مصفوفة.
- `all_bans(): array`
  - لماذا: جلب سجلات الحظر.
  - القيمة الراجعة: مصفوفة.
- `is_user_banned(userId): bool`
  - لماذا: التحقق من الحظر النشط لمستخدم.
  - القيمة الراجعة: `true/false`.
- `get_access_levels(): array`
  - لماذا: جلب مستويات الصلاحيات.
  - القيمة الراجعة: مصفوفة.
- `get_system_setting(key, default): mixed`
  - لماذا: قراءة إعداد نظام محدد.
  - القيمة الراجعة: قيمة الإعداد أو الافتراضي.
- `report_summary_stats(): array`
  - لماذا: مؤشرات عامة للتقارير.
  - القيمة الراجعة: مصفوفة إحصاءات.
- `customers_with_details(managerLicense): array`
  - لماذا: تفاصيل عملاء مدير معيّن.
  - القيمة الراجعة: مصفوفة.
- `manager_transaction_details(license, filters): array`
  - لماذا: تفاصيل معاملات المدير مع فلاتر.
  - القيمة الراجعة: مصفوفة.

### دوال المراسلة والموافقات
- `get_sms_logs(filters=[]): array`
  - لماذا: جلب سجلات الرسائل SMS.
  - القيمة الراجعة: مصفوفة سجلات.
- `all_participation_approvals(status=''): array`
  - لماذا: طلبات موافقة المشاركة (Pending/Approved/Rejected).
  - القيمة الراجعة: مصفوفة.

### دوال قاعدة البيانات المباشرة
- `db_connection(): ?PDO`
  - لماذا: الحصول على اتصال PDO.
  - القيمة الراجعة: كائن PDO أو `null`.
- `repo_fetch_one(sql, params=[]): ?array`
  - لماذا: جلب صف واحد من DB.
  - القيمة الراجعة: صف أو `null`.
- `repo_fetch_all(sql, params=[]): array`
  - لماذا: جلب عدة صفوف من DB.
  - القيمة الراجعة: مصفوفة صفوف.

---

## 2) نبذة كل صفحة + الاستدعاءات الفعلية

## account-verified.php
- النبذة: صفحة نتيجة/تأكيد تحقق الحساب بعد التسجيل.
- الاستدعاءات:
  - `is_logged_in_as_student()` / `is_logged_in_as_manager()` للتحقق من صلاحية عرض الصفحة.
  - `route()` لبناء روابط التنقل.
  - `e()` لطباعة آمنة.
  - `render_head()` / `render_responsive_shell_start()` / `render_responsive_shell_end()` / `render_end()` لبناء الصفحة.

## admin-dashboard.php
- النبذة: لوحة الإداري لإدارة المستخدمين، الرسائل، الحظر، التقارير.
- الاستدعاءات:
  - `is_logged_in_as_admin()` حماية الوصول.
  - `current_admin()` جلب هوية الإداري.
  - `all_students()` / `all_managers()` / `all_funds()` / `all_bans()` بيانات اللوحة.
  - `get_access_levels()` لمستويات الوصول.
  - `get_sms_logs()` لسجل الرسائل.
  - `report_summary_stats()` لمؤشرات الأداء.
  - `repo_fetch_all()` للاستعلامات المخصصة.
  - `action_url()` لنماذج الإدارة.
  - `set_flash()` و`redirect_to()` لمعالجة الفشل.
  - `render_*`, `route()`, `e()`, `mask_phone()` للعرض.

## bank-cards.php
- النبذة: إدارة البطاقات البنكية المرتبطة بحساب الطالب.
- الاستدعاءات:
  - `is_logged_in_as_student()` و`current_student()` للتحقق والهوية.
  - `bank_cards_for_student()` جلب البطاقات.
  - `student_transactions()` ربط البطاقة بسجل عمليات الطالب.
  - `mask_card()`, `e()`, `route()`, `render_*`.

## contracts.php
- النبذة: عرض العقود (طالب/مدير) بصيغة تحليلية.
- الاستدعاءات:
  - `is_logged_in_as_student()` / `is_logged_in_as_manager()` صلاحيات.
  - `current_student()` / `current_manager()` هوية المستخدم.
  - `contracts_for_student()` أو `contracts_for_manager()` بيانات العقود.
  - `format_sar()` تنسيق المبالغ.
  - `set_flash()` + `redirect_to()` عند منع الوصول.
  - `render_*`, `e()`.

## create-fund.php
- النبذة: نموذج إنشاء صندوق جديد كمسودة من المدير.
- الاستدعاءات:
  - `is_logged_in_as_manager()` + `current_manager()` صلاحية/هوية.
  - `action_url('create-fund')` لإرسال النموذج.
  - `set_flash()` + `redirect_to()` للحالات غير الصحيحة.
  - `route()`, `render_*`, `e()`.

## edit-fund.php
- النبذة: تعديل بيانات صندوق وإدارة حالته (Draft/Publish/Hide).
- الاستدعاءات:
  - `require_manager_login()` حماية صارمة.
  - `current_manager()` و`find_fund_by_id()` جلب الهوية والصندوق.
  - `action_url()` لإجراءات التحديث/الإخفاء/النشر.
  - `set_flash()`, `redirect_to()` لمعالجة الأخطاء.
  - `render_*`, `route()`, `e()`.

## edit-profile.php
- النبذة: تحديث بيانات الملف الشخصي (طالب/مدير) مع كلمة المرور والبيانات البنكية.
- الاستدعاءات:
  - `is_logged_in_as_student()` / `is_logged_in_as_manager()` تحقق الدور.
  - `current_student()` / `current_manager()` / `current_bank_account()` جلب بيانات النموذج.
  - `action_url('update-profile'|'update-bank'|'change-password')` إرسال النماذج.
  - `mask_card()` إخفاء البطاقة عند العرض.
  - `set_flash()`, `redirect_to()`, `route()`, `render_*`, `e()`.

## fund.php
- النبذة: صفحة تفاصيل الصندوق مع زر اشتراك/إلغاء حسب حالة المستخدم.
- الاستدعاءات:
  - `is_logged_in_as_student()` / `is_logged_in_as_manager()` صلاحيات.
  - `fund_by_slug()` و`all_funds()` جلب الصندوق.
  - `current_student()` + `student_has_contract()` تحديد حالة الاشتراك.
  - `format_sar()` عرض الأرقام المالية.
  - `action_url('cancel-subscription')` عند الإلغاء.
  - `set_flash()`, `redirect_to()`, `route()`, `render_*`, `e()`.

## home.php
- النبذة: الصفحة الرئيسية التسويقية للتنقل السريع.
- الاستدعاءات:
  - `route()` روابط الأقسام.
  - `render_head()`, `render_footer()`, `render_end()`, `e()`.

## investment-report.php
- النبذة: تقرير أداء استثماري لصندوق محدد.
- الاستدعاءات:
  - `is_logged_in_as_student()` / `is_logged_in_as_manager()` صلاحية.
  - `current_student()`, `fund_by_slug()`, `student_has_contract()` تحقق الوصول.
  - `latest_investment_amount()` حساب الأساس الاستثماري.
  - `repo_fetch_one()` / `repo_fetch_all()` بيانات تفصيلية من DB.
  - `format_sar()`, `set_flash()`, `redirect_to()`, `render_*`, `route()`, `e()`.

## jic-participants.php
- النبذة: إدارة طلبات مشاركة الطلاب من طرف JIC أو المدير.
- الاستدعاءات:
  - `is_logged_in_as_jic()` / `is_logged_in_as_manager()` صلاحيات.
  - `current_jic()` / `current_manager()` هوية المعتمد.
  - `all_participation_approvals()` قائمة الطلبات.
  - `action_url()` لنماذج الموافقة/الرفض.
  - `format_sar()`, `set_flash()`, `redirect_to()`, `render_*`, `route()`, `e()`.

## login.php
- النبذة: شاشة الدخول الموحد لجميع الأدوار.
- الاستدعاءات:
  - `action_url('login-*')` توجيه النماذج إلى الإجراء المناسب.
  - `route()` لروابط التسجيل/المساعدة.
  - `render_*`, `e()`.

## manager-dashboard.php
- النبذة: لوحة المدير (الصناديق، الطلبات، المشاركون، السحب).
- الاستدعاءات:
  - `is_logged_in_as_manager()` + `current_manager()`.
  - `manager_funds()` صناديق المدير فقط.
  - `subscribers_for_fund()`, `all_participation_approvals()`, `all_withdrawal_requests()`.
  - `all_students()`, `all_transactions()`, `repo_fetch_all()` لتجميع بيانات KPI.
  - `action_url()`, `set_flash()`, `redirect_to()`, `render_*`, `route()`, `e()`.

## manager-reports.php
- النبذة: تقارير المدير التنفيذية والتحليلية.
- الاستدعاءات:
  - `is_logged_in_as_manager()` + `current_manager()`.
  - `manager_funds()`, `manager_transaction_details()`, `customers_with_details()`.
  - `report_summary_stats()`, `fund_by_id()`.
  - `repo_fetch_one()`, `repo_fetch_all()`, `db_connection()` لاستعلامات متقدمة.
  - `current_bank_account()`, `mask_card()`, `format_sar()`.
  - `set_flash()`, `redirect_to()`, `render_*`, `route()`, `e()`.

## operation-result.php
- النبذة: صفحة نتيجة عملية عامة بعد إجراء إداري/استثماري.
- الاستدعاءات:
  - `is_logged_in_as_student()` / `is_logged_in_as_manager()` / `is_logged_in_as_admin()`.
  - `set_flash()`, `redirect_to()` حماية السياق.
  - `route()`, `render_*`, `e()`.

## publish-fund.php
- النبذة: مراجعة صندوق قبل نشره وتأكيد العملية.
- الاستدعاءات:
  - `is_logged_in_as_manager()` + `current_manager()`.
  - `fund_by_id()` / `fund_by_slug()` جلب الصندوق.
  - `action_url('publish-fund'|'save-fund-draft'|'undo-fund')`.
  - `format_sar()`, `capitalize()`, `set_flash()`, `redirect_to()`, `route()`, `render_*`, `e()`.

## register-manager.php
- النبذة: نموذج تسجيل مدير صندوق (غالبًا عبر الإداري).
- الاستدعاءات:
  - `is_logged_in_as_admin()` منع الوصول غير المصرح.
  - `action_url('register-manager')`, `set_flash()`, `redirect_to()`.
  - `route()`, `render_*`, `e()`.

## register-student.php
- النبذة: نموذج تسجيل طالب جديد.
- الاستدعاءات:
  - `action_url('register-student')` إرسال التسجيل.
  - `route()`, `render_*`, `e()`.

## reports.php
- النبذة: بوابة تحويل للتقرير الصحيح حسب الدور.
- الاستدعاءات:
  - `is_logged_in_as_student()` / `is_logged_in_as_manager()`.
  - `set_flash()`, `redirect_to()`.

## search-funds.php
- النبذة: استعراض/بحث الصناديق المتاحة للاشتراك.
- الاستدعاءات:
  - `is_logged_in_as_student()` حماية الدور.
  - `all_funds()` جلب القائمة + فلاتر.
  - `format_sar()`, `action_url()` لأزرار الاشتراك.
  - `set_flash()`, `route()`, `render_*`, `e()`.

## self-ban.php
- النبذة: تفعيل/عرض حالة تجميد الحساب الذاتي للطالب.
- الاستدعاءات:
  - `require_student_login()` حماية.
  - `current_student()` + `is_user_banned()` حالة الحظر.
  - `action_url('self-ban')`, `route()`, `render_*`, `e()`.

## send-notification.php
- النبذة: إرسال إشعارات/رسائل من لوحة الإداري.
- الاستدعاءات:
  - `is_logged_in_as_admin()` + `all_students()` + `all_managers()`.
  - `get_sms_logs()` سجل الإرسال.
  - `action_url('send-sms')`, `mask_phone()`.
  - `set_flash()`, `redirect_to()`, `route()`, `render_*`, `e()`.

## student-dashboard.php
- النبذة: لوحة الطالب (ملف، محافظ، صناديق مشتركة، KPIs).
- الاستدعاءات:
  - `is_logged_in_as_student()` + `current_student()`.
  - `current_wallet()`, `student_dashboard_cards()`, `all_funds()`.
  - `set_flash()`, `redirect_to()`, `route()`, `render_*`, `render_footer()`, `e()`.

## student-settings.php
- النبذة: إعدادات الطالب (خيارات الحساب + العقود + Self-ban + حذف الحساب).
- الاستدعاءات:
  - `is_logged_in_as_student()` + `current_student()`.
  - `is_user_banned()` لحالة التجميد.
  - `student_contracts()`, `fund_by_id()`, `all_managers()` لبناء بطاقات العقود.
  - `format_sar()`, `action_url()`, `set_flash()`, `redirect_to()`, `route()`, `render_*`, `e()`.

## subscribe.php
- النبذة: صفحة تنفيذ الاشتراك والدفع في صندوق.
- الاستدعاءات:
  - `is_logged_in_as_student()` + `current_student()`.
  - `fund_by_slug()` + `all_funds()` لاسترجاع الصندوق.
  - `current_bank_account()` + `bank_cards_for_student()` + `mask_card()`.
  - `latest_investment_amount()` للملخص السابق.
  - `format_sar()`, `action_url('subscribe')`, `set_flash()`, `redirect_to()`, `route()`, `render_*`, `e()`.

## system-settings.php
- النبذة: إعدادات النظام العامة للإداري.
- الاستدعاءات:
  - `is_logged_in_as_admin()` + `current_admin()`.
  - `get_system_setting()` قراءة الإعدادات الحالية.
  - `action_url('update-system-settings')`, `set_flash()`, `redirect_to()`, `route()`, `render_*`, `e()`.

## terms.php
- النبذة: صفحة الشروط والأحكام.
- الاستدعاءات:
  - `is_logged_in_as_student()` / `is_logged_in_as_manager()` (حسب سياسة الوصول).
  - `route()`, `render_*`, `e()`.

## verify-fund-operation.php
- النبذة: تحقق إضافي (OTP/Code) لعمليات حساسة على الصندوق.
- الاستدعاءات:
  - `require_manager_login()` + `current_manager()`.
  - `fund_by_id()` و`mask_phone()`.
  - `action_url('verify-fund-operation'|'resend-verification-code')`.
  - `set_flash()`, `redirect_to()`, `route()`, `render_*`, `e()`.

## wallet.php
- النبذة: صفحة محفظة الطالب (الرصيد، العقود، البطاقات، الطلبات).
- الاستدعاءات:
  - `is_logged_in_as_student()` + `current_student()`.
  - `current_wallet()`, `current_bank_account()`.
  - `student_contracts()`, `student_transactions()`, `student_withdrawal_requests()`.
  - `fund_by_id()`, `latest_investment_amount()`, `format_sar()`, `mask_card()`.
  - `action_url()`, `set_flash()`, `redirect_to()`, `route()`, `render_*`, `e()`.

## withdraw.php
- النبذة: طلب سحب أرباح/مبالغ من المحفظة.
- الاستدعاءات:
  - `require_student_login()` + `current_student()`.
  - `current_wallet()`, `current_bank_account()`, `student_withdrawal_requests()`.
  - `format_sar()`, `action_url('withdraw')`, `route()`, `render_*`, `e()`.

---

## 3) نبذة الملفات المركزية (خارج pages)

## index.php
- النبذة: Router رئيسي يقرأ `?page=` ويحمّل الصفحة المناسبة من مجلد `pages`.
- لماذا مهم: نقطة الدخول الأولى لكل الشاشات.

## action.php
- النبذة: معالج جميع عمليات POST (تسجيل، دخول، اشتراك، تحديث، موافقات...).
- لماذا مهم: طبقة التحكم في منطق الأعمال قبل التخزين في DB.

## includes/functions.php
- النبذة: دوال العرض، التوجيه، الأمان، الرسائل، وبناء الهيكل العام للواجهة.

## includes/repository.php
- النبذة: دوال بيانات النظام (قراءة/كتابة DB + fallback) ومنطق الأعمال الأساسي.

## config/database.php
- النبذة: إعداد الاتصال بقاعدة البيانات عبر PDO.

---

## 4) ملاحظات توثيقية مهمة
- في أي صفحة، التسلسل القياسي غالبًا يكون:
  1) فحص صلاحية المستخدم (`is_logged_in_*` أو `require_*`).
  2) جلب بيانات السياق (`current_*`, `fund_*`, `*_contracts` ...).
  3) تجهيز روابط الإجراءات (`action_url`) وروابط التنقل (`route`).
  4) إخراج الصفحة (`render_head` ثم shell start/end ثم `render_end`).
- القيم الراجعة المهمة التي تُبنى عليها القرارات:
  - `bool` من دوال الصلاحيات والحالات.
  - `?array` من دوال الجلب المفرد (وجود/عدم وجود).
  - `array` من القوائم والتقارير.
  - `string` من دوال الروابط والتنسيق.
  - `void` من دوال العرض وإعادة التوجيه.
