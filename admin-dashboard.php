<?php
/**
 * ملاحظات توثيقية للصفحة: admin-dashboard.php
 * الغرض: لوحة الإدارة لمتابعة المستخدمين والصناديق والرسائل والإعدادات.
 *
 * الدوال المستدعاة في هذه الصفحة ولماذا:
 * - action_url(): بناء رابط إجراء POST عبر action.php. القيمة الراجعة: string.
 * - all_bans(): جلب سجلات الحظر. القيمة الراجعة: array.
 * - all_funds(): جلب قائمة الصناديق (الكل أو المنشور فقط). القيمة الراجعة: array.
 * - all_managers(): جلب قائمة المديرين. القيمة الراجعة: array.
 * - all_students(): جلب قائمة الطلاب. القيمة الراجعة: array.
 * - current_admin(): جلب بيانات الإداري الحالي. القيمة الراجعة: array.
 * - e(): تعقيم النص قبل الطباعة لمنع XSS. القيمة الراجعة: string.
 * - get_access_levels(): جلب مستويات الصلاحيات المتاحة. القيمة الراجعة: array.
 * - get_sms_logs(): جلب سجل رسائل SMS المرسلة. القيمة الراجعة: array.
 * - is_logged_in_as_admin(): التحقق من أن الجلسة الحالية لإداري. القيمة الراجعة: bool.
 * - mask_phone(): إخفاء جزء من رقم الهاتف عند العرض. القيمة الراجعة: string.
 * - redirect_to(): إيقاف الصفحة الحالية وتحويل المستخدم لمسار مناسب. القيمة الراجعة: void.
 * - render_end(): إغلاق الصفحة وتحميل JS العام. القيمة الراجعة: void.
 * - render_head(): بدء هيكل HTML وتحميل ملفات CSS. القيمة الراجعة: void.
 * - render_responsive_shell_end(): إغلاق الغلاف الموحد للواجهة. القيمة الراجعة: void.
 * - render_responsive_shell_start(): بدء الغلاف الموحد للواجهة (Header/Sidebar). القيمة الراجعة: void.
 * - repo_fetch_all(): تنفيذ استعلام DB وإرجاع عدة صفوف. القيمة الراجعة: array.
 * - report_summary_stats(): جلب مؤشرات التقارير الإجمالية. القيمة الراجعة: array.
 * - route(): بناء رابط داخلي آمن للانتقال بين الصفحات. القيمة الراجعة: string.
 * - set_flash(): تخزين رسالة مؤقتة للواجهة (نجاح/خطأ). القيمة الراجعة: void.
 */
// Check admin access
if (!is_logged_in_as_admin()) {
    set_flash('error', 'Access denied. Admin login required.');
    redirect_to('login', ['role' => 'admin']);
}

$admin = current_admin();
$stats = report_summary_stats();
$students = all_students();
$managers = all_managers();
$funds = all_funds(true);
$accessLevels = get_access_levels();
$bans = all_bans();
$smsLogs = get_sms_logs();

$activeBansByKey = [];
foreach ($bans as $banRow) {
    if ((int) ($banRow['IsActive'] ?? 0) !== 1) {
        continue;
    }
    $banType = strtolower((string) ($banRow['UserType'] ?? ''));
    $banUserId = trim((string) ($banRow['UserID'] ?? ''));
    if ($banType !== '' && $banUserId !== '') {
        $activeBansByKey[$banType . ':' . $banUserId] = $banRow;
    }
}

$dbNotifications = repo_fetch_all('SELECT * FROM Notification ORDER BY NotificationID DESC LIMIT 30');
$rawNotifications = $dbNotifications;

usort($rawNotifications, static function (array $a, array $b): int {
    return (int) ($b['NotificationID'] ?? 0) <=> (int) ($a['NotificationID'] ?? 0);
});

$fundCreationNotifications = array_values(array_filter(
    $rawNotifications,
    static fn(array $n): bool =>
        (int) ($n['FundID'] ?? 0) > 0
        && stripos((string) ($n['NotificationDescription'] ?? ''), 'new fund created') !== false
));

$newFundNotificationCount = count($fundCreationNotifications);
$recentFundNotifications = array_slice($fundCreationNotifications, 0, 4);

$pendingStudents = array_filter($students, fn($s) => ($s['StudentAccountStates'] ?? '') === 'Pending');
$pendingManagers = array_filter($managers, fn($m) => ($m['FundManagerAccountStatus'] ?? '') === 'Pending');

// Compute earnings data for overview
$totalEarnings = $stats['total_investments'] ?? 0;
$totalDistributed = $stats['total_returns'] ?? 0;
$totalPending = $totalEarnings - $totalDistributed;
$jicFees = round($totalEarnings * 0.035);
$availableToWithdraw = $totalPending > 0 ? $totalPending : 0;
$distributedPercent = $totalEarnings > 0 ? round(($totalDistributed / $totalEarnings) * 100, 1) : 0;

$activeTab = (string) ($_GET['tab'] ?? 'overview');
$allowedTabs = ['overview', 'users', 'create-user', 'funds', 'access-levels', 'bans', 'sms'];
if (!in_array($activeTab, $allowedTabs, true)) {
    $activeTab = 'overview';
}

render_head('Admin Dashboard');
render_responsive_shell_start('');
?>

<div class="jd-page u-inline-1">

    <!-- Welcome banner -->
    <div class="jd-welcome">
        <div class="jd-welcome-avatar">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 4-7 8-7s8 3 8 7"/></svg>
        </div>
        <div class="jd-welcome-copy">
            <h1>WELCOME BACK!</h1>
            <p>Here's an overview of your earnings<br>and fund distributions.</p>
        </div>
        <div class="jd-welcome-right">
            <div class="jd-bell-wrap">
                <svg class="jd-bell-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
                <?php if ($newFundNotificationCount > 0): ?>
                <span class="jd-bell-badge"><?= e($newFundNotificationCount) ?></span>
                <?php endif; ?>
            </div>
            <span class="jd-id-chip">Admin: <?= e($admin['UserName']) ?></span>
        </div>
    </div>

    <!-- Stats grid -->
    <div class="jd-stats-grid">
        <!-- Total Earnings -->
        <div class="jd-stat-card">
            <div class="jd-stat-icon-wrap jd-icon-teal">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v12M8 10h8M8 14h8"/></svg>
            </div>
            <span class="jd-stat-title">Total Earnings</span>
            <span class="jd-stat-value"><?= number_format($totalEarnings) ?> <small>SAR</small></span>
            <span class="jd-stat-trend up">Up 12.5% this month</span>
        </div>
        <!-- Distributed Earnings -->
        <div class="jd-stat-card">
            <div class="jd-stat-icon-wrap jd-icon-green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12H2M17 7l5 5-5 5"/></svg>
            </div>
            <span class="jd-stat-title">Distributed Earnings</span>
            <span class="jd-stat-value"><?= number_format($totalDistributed) ?> <small>SAR</small></span>
            <span class="jd-stat-trend up">Up 8.3% this month</span>
        </div>
        <!-- Pending Earnings -->
        <div class="jd-stat-card">
            <div class="jd-stat-icon-wrap jd-icon-olive">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
            </div>
            <span class="jd-stat-title">Pending Earnings</span>
            <span class="jd-stat-value"><?= number_format($totalPending > 0 ? $totalPending : 0) ?> <small>SAR</small></span>
            <span class="jd-stat-trend up">Up 15.2% this month</span>
        </div>
        <!-- JIC Fees Deducted -->
        <div class="jd-stat-card">
            <div class="jd-stat-icon-wrap jd-icon-dark">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
            </div>
            <span class="jd-stat-title">JIC Fees Deducted</span>
            <span class="jd-stat-value"><?= number_format($jicFees) ?> <small>SAR</small></span>
            <span class="jd-stat-trend down">Down 2.1% this month</span>
        </div>
        <!-- Available to Withdraw -->
        <div class="jd-stat-card">
            <div class="jd-stat-icon-wrap jd-icon-gold">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            </div>
            <span class="jd-stat-title">Available to Withdraw</span>
            <span class="jd-stat-value"><?= number_format($availableToWithdraw) ?> <small>SAR</small></span>
            <span class="jd-stat-trend u-inline-2">Handled by Fund Manager</span>
        </div>
    </div>

    <!-- Tabs -->
    <div class="admin-tabs">
        <a href="<?= e(route('admin-dashboard', ['tab' => 'overview'])) ?>" class="admin-tab <?= $activeTab === 'overview' ? 'active' : '' ?>">Overview</a>
        <a href="<?= e(route('admin-dashboard', ['tab' => 'users'])) ?>" class="admin-tab <?= $activeTab === 'users' ? 'active' : '' ?>">Users</a>
        <a href="<?= e(route('admin-dashboard', ['tab' => 'create-user'])) ?>" class="admin-tab <?= $activeTab === 'create-user' ? 'active' : '' ?>">Create User</a>
        <a href="<?= e(route('admin-dashboard', ['tab' => 'funds'])) ?>" class="admin-tab <?= $activeTab === 'funds' ? 'active' : '' ?>">Funds</a>
        <a href="<?= e(route('admin-dashboard', ['tab' => 'access-levels'])) ?>" class="admin-tab <?= $activeTab === 'access-levels' ? 'active' : '' ?>">Access Levels</a>
        <a href="<?= e(route('admin-dashboard', ['tab' => 'bans'])) ?>" class="admin-tab <?= $activeTab === 'bans' ? 'active' : '' ?>">Bans</a>
        <a href="<?= e(route('admin-dashboard', ['tab' => 'sms'])) ?>" class="admin-tab <?= $activeTab === 'sms' ? 'active' : '' ?>">SMS Logs</a>
    </div>

    <!-- Tab Content -->
    <div class="tab-content">
        <?php if ($activeTab === 'overview'): ?>

        <!-- Fund overview -->
        <div class="jd-panel jd-panel-dark u-inline-3">
            <div class="jd-panel-head">
                <h2>Fund Overview</h2>
                <div class="jd-panel-head-right">
                    <a href="<?= e(route('admin-dashboard', ['tab' => 'funds'])) ?>">View All Funds</a>
                    <button class="jd-nav-btn" onclick="document.getElementById('adFundCarousel').scrollBy({left:-260,behavior:'smooth'})">&lsaquo;</button>
                    <button class="jd-nav-btn" onclick="document.getElementById('adFundCarousel').scrollBy({left:260,behavior:'smooth'})">&rsaquo;</button>
                </div>
            </div>
            <div class="jd-fund-carousel" id="adFundCarousel">
                <?php foreach ($funds as $fund):
                    $statusClass = strtolower($fund['FundAccountStatus']) === 'published' ? 'background:#d4edda;color:#155724;' : (strtolower($fund['FundAccountStatus']) === 'pending' ? 'background:#fff3cd;color:#856404;' : 'background:#e2e3e5;color:#383d41;');
                ?>
                <div class="jd-fund-card">
                    <div class="jd-fund-top">
                        <div class="jd-fund-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="18" rx="2"/><path d="M2 9h20M10 3v18"/></svg></div>
                        <div class="jd-fund-head">
                            <h3><?= e($fund['FundTitle']) ?></h3>
                            <span class="jd-pill" style="<?= $statusClass ?>"><?= e($fund['FundAccountStatus']) ?></span>
                        </div>
                    </div>
                    <div class="u-inline-6">Duration: <?= e($fund['DurationMonths'] ?? 'N/A') ?> Months</div>
                    <div class="u-inline-7">
                        <span class="u-inline-8">Estimated Return</span>
                        <span class="u-inline-8">Last Distribution</span>
                    </div>
                    <div class="u-inline-9">
                        <span><?= e($fund['ExpectedReturnPercentage']) ?>%</span>
                        <span class="u-inline-10"><?= e(date('M d, Y', strtotime($fund['CreatedAt'] ?? date('Y-m-d')))) ?></span>
                    </div>
                    <!-- Progress bar -->
                    <div class="u-inline-11">
                        <div style="height:100%;width:<?= rand(40,100) ?>%;background:linear-gradient(90deg,#4caf50,#81c784);border-radius:4px;"></div>
                    </div>
                    <div class="u-inline-12">
                        <span>Distributed <?= rand(40,100) ?>%</span>
                        <span>Pending <?= rand(0,60) ?>%</span>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($funds)): ?>
                <div class="u-inline-13">No funds available</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Earnings, schedule, and notifications -->
        <div class="u-inline-14">

            <!-- Your Earnings -->
            <div class="jd-panel">
                <div class="jd-panel-head">
                    <h2>Your Earnings</h2>
                    <div class="jd-panel-head-right">
                        <a href="<?= e(route('admin-dashboard',['tab'=>'funds'])) ?>">View All</a>
                        <button class="jd-nav-btn-sm">&lsaquo;</button>
                        <button class="jd-nav-btn-sm">&rsaquo;</button>
                    </div>
                </div>
                <div class="table-container">
                    <table class="data-table u-inline-4">
                        <thead>
                            <tr>
                                <th>Fund Name</th>
                                <th>Invested Amount</th>
                                <th>Earned</th>
                                <th>Distributed</th>
                                <th>Pending</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($funds, 0, 4) as $f):
                                $invested = $f['MinimumSubscriptionAmount'] ?? 0;
                                $earned = round($invested * (($f['ExpectedReturnPercentage'] ?? 10) / 100));
                                $dist = round($earned * 0.6);
                                $pend = $earned - $dist;
                            ?>
                            <tr>
                                <td class="u-inline-15"><?= e($f['FundTitle']) ?></td>
                                <td><?= number_format($invested) ?> SAR</td>
                                <td class="u-inline-16"><?= number_format($earned) ?> SAR</td>
                                <td><?= number_format($dist) ?> SAR</td>
                                <td><?= number_format($pend) ?> SAR</td>
                                <td><?php if ($pend > 0): ?><span class="u-inline-17">Manager Action</span><?php else: ?>-<?php endif; ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($funds)): ?>
                            <tr><td colspan="6" class="text-center text-muted">No fund data</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Next Distribution -->
            <div class="jd-panel">
                <div class="jd-panel-head">
                    <h2>Next Distribution</h2>
                    <div class="jd-panel-head-right">
                        <a href="#">View Schedule</a>
                        <button class="jd-nav-btn-sm">Prev</button>
                        <button class="jd-nav-btn-sm">Next</button>
                    </div>
                </div>
                <div class="table-container">
                    <table class="data-table u-inline-4">
                        <thead>
                            <tr>
                                <th>Fund Name</th>
                                <th>Date</th>
                                <th>Est. Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($funds, 0, 3) as $f):
                                $estAmount = round(($f['MinimumSubscriptionAmount'] ?? 1000) * (($f['ExpectedReturnPercentage'] ?? 10) / 100));
                                $nextDate = date('M d, Y', strtotime('+' . rand(15,90) . ' days'));
                            ?>
                            <tr>
                                <td class="u-inline-15"><?= e($f['FundTitle']) ?></td>
                                <td><?= $nextDate ?></td>
                                <td class="u-inline-15"><?= number_format($estAmount) ?> SAR</td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($funds)): ?>
                            <tr><td colspan="3" class="text-center text-muted">No upcoming distributions</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Notifications -->
            <div class="jd-panel">
                <div class="jd-panel-head">
                    <h2>Notifications</h2>
                    <span class="u-inline-18">Live Updates</span>
                </div>
                <div class="u-inline-19">
                    <?php foreach ($recentFundNotifications as $idx => $notification): ?>
                    <?php
                        $sentRaw = (string) ($notification['SentDate'] ?? date('Y-m-d H:i:s'));
                        $sentTs = strtotime($sentRaw) ?: time();
                    ?>
                    <div style="display:flex;gap:10px;align-items:flex-start;<?= $idx < (count($recentFundNotifications) - 1) ? 'padding-bottom:12px;border-bottom:1px solid #eee;' : '' ?>">
                        <span class="u-inline-20">NEW</span>
                        <div>
                            <div class="u-inline-21">New Fund Created</div>
                            <div class="u-inline-22"><?= e($notification['NotificationDescription'] ?? 'New fund created') ?></div>
                            <div class="u-inline-23"><?= e(date('M d, Y', $sentTs)) ?> | <?= e(date('g:i A', $sentTs)) ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($recentFundNotifications)): ?>
                    <div class="u-inline-24">
                        <span class="u-inline-25">INFO</span>
                        <div>
                            <div class="u-inline-21">No New Fund Alerts</div>
                            <div class="u-inline-22">When a manager creates a fund, it appears here instantly.</div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- أ¢â€¢ع¯أ¢â€¢ع¯أ¢â€¢ع¯أ¢â€¢ع¯أ¢â€¢ع¯أ¢â€¢ع¯أ¢â€¢ع¯أ¢â€¢ع¯أ¢â€¢ع¯أ¢â€¢ع¯ Bottom Charts Row (4 panels) أ¢â€¢ع¯أ¢â€¢ع¯أ¢â€¢ع¯أ¢â€¢ع¯أ¢â€¢ع¯أ¢â€¢ع¯أ¢â€¢ع¯أ¢â€¢ع¯أ¢â€¢ع¯أ¢â€¢ع¯ -->
        <div class="u-inline-26">

            <!-- Total Earnings Summary -->
            <div class="jd-panel">
                <div class="u-inline-27">
                    <span class="u-inline-28">SAR</span>
                    <h3 class="u-inline-29">Total Earnings Summary</h3>
                </div>
                <div class="u-inline-30">
                    <div class="u-inline-31"><span class="u-inline-2">Total Earnings (All Funds)</span><strong><?= number_format($totalEarnings) ?> SAR</strong></div>
                    <div class="u-inline-31"><span class="u-inline-2">Total Distributed</span><strong><?= number_format($totalDistributed) ?> SAR</strong></div>
                    <div class="u-inline-31"><span class="u-inline-2">Total Pending</span><strong><?= number_format($totalPending > 0 ? $totalPending : 0) ?> SAR</strong></div>
                    <div class="u-inline-31"><span class="u-inline-2">JIC Fees Deducted</span><strong><?= number_format($jicFees) ?> SAR</strong></div>
                    <div class="u-inline-32">
                        <span class="u-inline-33">Available to Withdraw</span>
                        <strong class="u-inline-34"><?= number_format($availableToWithdraw) ?> SAR</strong>
                    </div>
                </div>
            </div>

            <!-- Distributed vs Total Earnings (donut) -->
            <div class="jd-panel">
                <div class="u-inline-27">
                    <span class="u-inline-28">PIE</span>
                    <h3 class="u-inline-29">Distributed vs Total Earnings</h3>
                </div>
                <div class="u-inline-35">
                    <span class="u-inline-36"><span class="u-inline-37"></span> Distributed</span>
                    <span class="u-inline-36"><span class="u-inline-38"></span> Pending</span>
                </div>
                <!-- SVG Donut Chart -->
                <div class="u-inline-39">
                    <svg width="140" height="140" viewBox="0 0 140 140">
                        <circle cx="70" cy="70" r="54" fill="none" stroke="#c8e6c9" stroke-width="18"/>
                        <circle cx="70" cy="70" r="54" fill="none" stroke="#2e7d32" stroke-width="18" stroke-dasharray="<?= round(339.3 * $distributedPercent / 100) ?> 339.3" stroke-dashoffset="0" transform="rotate(-90 70 70)"/>
                    </svg>
                    <div class="u-inline-40">
                        <div class="u-inline-41"><?= $distributedPercent ?>%</div>
                        <div class="u-inline-42">Distributed</div>
                    </div>
                </div>
                <div class="u-inline-43">
                    <div><strong class="u-inline-44">Distributed</strong> <?= number_format($totalDistributed) ?> SAR</div>
                    <div><strong class="u-inline-45">Pending</strong> <?= number_format($totalPending > 0 ? $totalPending : 0) ?> SAR</div>
                </div>
            </div>

            <!-- Earnings by Fund (bar chart) -->
            <div class="jd-panel">
                <div class="u-inline-27">
                    <span class="u-inline-28">BAR</span>
                    <h3 class="u-inline-29">Earnings by Fund (by Period)</h3>
                </div>
                <div class="u-inline-46">
                    <span class="u-inline-47">3 Months</span>
                    <span class="u-inline-48">6 Months</span>
                    <span class="u-inline-48">12 Months</span>
                </div>
                <!-- SVG Bar Chart -->
                <svg width="100%" height="140" viewBox="0 0 280 140">
                    <line x1="30" y1="0" x2="30" y2="120" stroke="#eee" stroke-width="1"/>
                    <line x1="30" y1="120" x2="280" y2="120" stroke="#eee" stroke-width="1"/>
                    <?php
                    $barFunds = array_slice($funds, 0, 5);
                    $maxVal = 8000;
                    $barW = 30; $gap = 50;
                    foreach ($barFunds as $i => $bf):
                        $val = ($bf['MinimumSubscriptionAmount'] ?? 1000) * (($bf['ExpectedReturnPercentage'] ?? 10) / 100);
                        $h = min(100, round($val / $maxVal * 100));
                        $x = 45 + $i * $gap;
                    ?>
                    <rect x="<?= $x ?>" y="<?= 120 - $h ?>" width="<?= $barW ?>" height="<?= $h ?>" rx="4" fill="#45644A" opacity="0.8"/>
                    <?php endforeach; ?>
                    <!-- Y Labels -->
                    <text x="2" y="20" font-size="8" fill="#999">8K</text>
                    <text x="2" y="60" font-size="8" fill="#999">4K</text>
                    <text x="2" y="100" font-size="8" fill="#999">2K</text>
                    <text x="2" y="118" font-size="8" fill="#999">0</text>
                </svg>
            </div>

            <!-- Earnings by Group (pie) -->
            <div class="jd-panel">
                <div class="u-inline-27">
                    <span class="u-inline-28">GRP</span>
                    <h3 class="u-inline-29">Earnings by Group</h3>
                </div>
                <div class="u-inline-49">
                    <svg width="130" height="130" viewBox="0 0 130 130">
                        <circle cx="65" cy="65" r="54" fill="none" stroke="#18442A" stroke-width="18" stroke-dasharray="135 339.3" stroke-dashoffset="0" transform="rotate(-90 65 65)"/>
                        <circle cx="65" cy="65" r="54" fill="none" stroke="#45644A" stroke-width="18" stroke-dasharray="102 339.3" stroke-dashoffset="-135" transform="rotate(-90 65 65)"/>
                        <circle cx="65" cy="65" r="54" fill="none" stroke="#81c784" stroke-width="18" stroke-dasharray="68 339.3" stroke-dashoffset="-237" transform="rotate(-90 65 65)"/>
                        <circle cx="65" cy="65" r="54" fill="none" stroke="#c8e6c9" stroke-width="18" stroke-dasharray="34 339.3" stroke-dashoffset="-305" transform="rotate(-90 65 65)"/>
                    </svg>
                    <div class="u-inline-50"><?= number_format($totalEarnings) ?><br><small class="u-inline-51">SAR</small></div>
                </div>
                <div class="u-inline-52">
                    <div class="u-inline-53"><span class="u-inline-54"></span> <span class="u-inline-55">Group A</span> <span class="u-inline-56">40%</span></div>
                    <div class="u-inline-53"><span class="u-inline-57"></span> <span class="u-inline-55">Group B</span> <span class="u-inline-56">30%</span></div>
                    <div class="u-inline-53"><span class="u-inline-58"></span> <span class="u-inline-55">Group C</span> <span class="u-inline-56">20%</span></div>
                    <div class="u-inline-53"><span class="u-inline-38"></span> <span class="u-inline-55">Group D</span> <span class="u-inline-56">10%</span></div>
                </div>
            </div>
        </div>

        <?php elseif ($activeTab === 'users'): ?>
        <!-- Users Tab -->
        <div class="section">
            <h3>Students (<?= count($students) ?>)</h3>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $s): ?>
                        <?php
                            $studentId = (int) ($s['StudentID'] ?? 0);
                            $studentBanKey = 'student:' . $studentId;
                            $studentIsBanned = isset($activeBansByKey[$studentBanKey]);
                        ?>
                        <tr>
                            <td><?= e($studentId) ?></td>
                            <td><?= e($s['StudentNameFirst'] . ' ' . $s['StudentNameLast']) ?></td>
                            <td><?= e($s['StudentPhoneNumber']) ?></td>
                            <td>
                                <span class="status-badge <?= $studentIsBanned ? 'status-banned' : 'status-' . strtolower(str_replace(' ', '-', (string) ($s['StudentAccountStates'] ?? ''))) ?>">
                                    <?= $studentIsBanned ? 'Banned' : e($s['StudentAccountStates']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($studentIsBanned): ?>
                                <form action="<?= e(action_url('unban-user')) ?>" method="POST" class="u-inline-59">
                                    <input type="hidden" name="target_user_type" value="Student">
                                    <input type="hidden" name="target_user_id" value="<?= e($studentId) ?>">
                                    <input type="hidden" name="return_tab" value="users">
                                    <button type="submit" class="btn-sm btn-success" title="Unban" onclick="return confirm('Lift ban for this user?')">Unban</button>
                                </form>
                                <?php else: ?>
                                <form action="<?= e(action_url('ban-user')) ?>" method="POST" class="u-inline-59">
                                    <input type="hidden" name="target_user_type" value="Student">
                                    <input type="hidden" name="target_user_id" value="<?= e($studentId) ?>">
                                    <input type="hidden" name="ban_reason" value="Admin action">
                                    <input type="hidden" name="return_tab" value="users">
                                    <button type="submit" class="btn-sm btn-danger" title="Ban" onclick="return confirm('Ban this user?')">Ban</button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="section">
            <h3>Fund Managers (<?= count($managers) ?>)</h3>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>License</th>
                            <th>Name</th>
                            <th>Funds</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($managers as $m): ?>
                        <?php
                            $managerLicense = (int) ($m['FundManagerNumberofLicense'] ?? 0);
                            $managerBanKey = 'fundmanager:' . $managerLicense;
                            $managerIsBanned = isset($activeBansByKey[$managerBanKey]);
                        ?>
                        <tr>
                            <td><?= e($managerLicense) ?></td>
                            <td><?= e($m['FundManagerNameFirst'] . ' ' . $m['FundManagerNameLast']) ?></td>
                            <td><?= e($m['FundManagerNumberOfFund']) ?></td>
                            <td>
                                <span class="status-badge <?= $managerIsBanned ? 'status-banned' : 'status-' . strtolower((string) ($m['FundManagerAccountStatus'] ?? '')) ?>">
                                    <?= $managerIsBanned ? 'Banned' : e($m['FundManagerAccountStatus']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($managerIsBanned): ?>
                                <form action="<?= e(action_url('unban-user')) ?>" method="POST" class="u-inline-59">
                                    <input type="hidden" name="target_user_type" value="FundManager">
                                    <input type="hidden" name="target_user_id" value="<?= e($managerLicense) ?>">
                                    <input type="hidden" name="return_tab" value="users">
                                    <button type="submit" class="btn-sm btn-success" title="Unban" onclick="return confirm('Lift ban for this manager?')">Unban</button>
                                </form>
                                <?php else: ?>
                                <form action="<?= e(action_url('ban-user')) ?>" method="POST" class="u-inline-59">
                                    <input type="hidden" name="target_user_type" value="FundManager">
                                    <input type="hidden" name="target_user_id" value="<?= e($managerLicense) ?>">
                                    <input type="hidden" name="ban_reason" value="Admin action">
                                    <input type="hidden" name="return_tab" value="users">
                                    <button type="submit" class="btn-sm btn-danger" title="Ban" onclick="return confirm('Ban this manager?')">Ban</button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php elseif ($activeTab === 'create-user'): ?>
        <div class="section">
            <h3>Create New User</h3>
            <div class="card card-form create-user-card">
                <form action="<?= e(action_url('admin-create-user')) ?>" method="POST" id="adminCreateUserForm">

                    <!-- Role selector -->
                    <div class="cu-section">
                        <p class="cu-section-title">Role</p>
                        <div class="cu-role-tabs">
                            <label class="cu-role-tab">
                                <input type="radio" name="role" value="Student" checked>
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    Student
                                </span>
                            </label>
                            <label class="cu-role-tab">
                                <input type="radio" name="role" value="FundManager">
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-4 0v2"/><path d="M8 7V5a2 2 0 1 0-4 0v2"/></svg>
                                    Fund Manager
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Basic Info -->
                    <div class="cu-section">
                        <p class="cu-section-title">Basic Information</p>
                        <div class="cu-grid">
                            <div class="cu-field">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-input form-input-full" placeholder="e.g. Ahmed" required>
                            </div>
                            <div class="cu-field">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-input form-input-full" placeholder="e.g. Al-Harbi">
                            </div>
                            <div class="cu-field">
                                <label class="form-label">Phone</label>
                                <input type="tel" name="phone" class="form-input form-input-full" placeholder="+966 5x xxx xxxx">
                            </div>
                            <div class="cu-field">
                                <label class="form-label">Access Level</label>
                                <select name="access_id" class="form-select">
                                    <?php foreach ($accessLevels as $level): ?>
                                    <option value="<?= e($level['AccessID']) ?>"><?= e($level['Level']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Role-specific fields -->
                    <div class="cu-section cu-student-fields">
                        <p class="cu-section-title">Student Details</p>
                        <div class="cu-grid">
                            <div class="cu-field">
                                <label class="form-label">Student ID</label>
                                <input type="number" name="user_id" class="form-input form-input-full" placeholder="University student number">
                            </div>
                            <div class="cu-field">
                                <label class="form-label">Campus</label>
                                <input type="text" name="campus" class="form-input form-input-full" placeholder="e.g. Main Campus">
                            </div>
                            <div class="cu-field">
                                <label class="form-label">Degree</label>
                                <input type="text" name="degree" class="form-input form-input-full" placeholder="e.g. Bachelor">
                            </div>
                            <div class="cu-field">
                                <label class="form-label">Major</label>
                                <input type="text" name="major" class="form-input form-input-full" placeholder="e.g. Finance">
                            </div>
                            <div class="cu-field cu-field-full">
                                <label class="form-label">IBAN</label>
                                <input type="text" name="iban" class="form-input form-input-full" placeholder="SA00 0000 0000 0000 0000 0000">
                            </div>
                        </div>
                    </div>

                    <div class="cu-section cu-manager-fields u-inline-5">
                        <p class="cu-section-title">Manager Details</p>
                        <div class="cu-grid">
                            <div class="cu-field">
                                <label class="form-label">License Number</label>
                                <input type="number" name="user_id" class="form-input form-input-full" placeholder="Manager license number">
                            </div>
                            <div class="cu-field">
                                <label class="form-label">Contract Name</label>
                                <input type="text" name="contract_name" class="form-input form-input-full" placeholder="e.g. Standard Management Contract">
                            </div>
                        </div>
                    </div>

                    <!-- Security -->
                    <div class="cu-section">
                        <p class="cu-section-title">Security</p>
                        <div class="cu-grid">
                            <div class="cu-field">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-input form-input-full" required>
                            </div>
                            <div class="cu-field">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" name="confirm_password" class="form-input form-input-full" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions-row">
                        <button type="submit" class="btn-gold">Create User</button>
                    </div>
                </form>
            </div>
        </div>
        <script>
        (function(){
            var form = document.getElementById('adminCreateUserForm');
            if (!form) return;
            var roleInputs = form.querySelectorAll('input[name="role"]');
            var studentFields = form.querySelector('.cu-student-fields');
            var managerFields = form.querySelector('.cu-manager-fields');
            var studentUserId = studentFields.querySelector('input[name="user_id"]');
            var managerUserId = managerFields.querySelector('input[name="user_id"]');

            function setSectionEnabled(section, enabled) {
                section.querySelectorAll('input, select, textarea').forEach(function(el) {
                    el.disabled = !enabled;
                });
            }

            function updateFields() {
                var val = form.querySelector('input[name="role"]:checked').value;
                if (val === 'Student') {
                    studentFields.style.display = '';
                    managerFields.style.display = 'none';
                    setSectionEnabled(studentFields, true);
                    setSectionEnabled(managerFields, false);
                    studentUserId.required = true;
                    managerUserId.required = false;
                } else {
                    studentFields.style.display = 'none';
                    managerFields.style.display = '';
                    setSectionEnabled(studentFields, false);
                    setSectionEnabled(managerFields, true);
                    studentUserId.required = false;
                    managerUserId.required = true;
                }
            }
            roleInputs.forEach(function(r){ r.addEventListener('change', updateFields); });
            updateFields();
        })();
        </script>

        <?php elseif ($activeTab === 'funds'): ?>
        <!-- Funds Tab -->
        <div class="section">
            <h3>All Funds (<?= count($funds) ?>)</h3>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Risk</th>
                            <th>Return</th>
                            <th>Status</th>
                            <th>Manager</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($funds as $f): ?>
                        <tr>
                            <td><?= e($f['FundID']) ?></td>
                            <td><a href="<?= e(route('fund', ['slug' => $f['slug']])) ?>"><?= e($f['FundTitle']) ?></a></td>
                            <td><span class="risk-badge risk-<?= strtolower($f['RiskLevel']) ?>"><?= e(ucfirst($f['RiskLevel'])) ?></span></td>
                            <td><?= e($f['ExpectedReturnPercentage']) ?>%</td>
                            <td><span class="status-badge status-<?= strtolower($f['FundAccountStatus']) ?>"><?= e($f['FundAccountStatus']) ?></span></td>
                            <td><?= e($f['FundManagerNumberofLicense']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php elseif ($activeTab === 'access-levels'): ?>
        <!-- Access Levels Tab -->
        <div class="section">
            <h3>Access Levels</h3>
            <div class="permissions-grid">
                <?php foreach ($accessLevels as $level): ?>
                <div class="permission-card">
                    <h4><?= e($level['Level']) ?></h4>
                    <p><?= e($level['Description']) ?></p>
                    <span class="access-id">ID: <?= e($level['AccessID']) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="section">
            <h3>Update User Access Level</h3>
            <form action="<?= e(action_url('update-access-level')) ?>" method="POST" class="inline-form">
                <select name="user_type" class="form-select">
                    <option value="Student">Student</option>
                    <option value="FundManager">Fund Manager</option>
                </select>
                <input type="number" name="user_id" placeholder="User ID" class="form-input" required>
                <select name="access_id" class="form-select">
                    <?php foreach ($accessLevels as $level): ?>
                    <option value="<?= e($level['AccessID']) ?>"><?= e($level['Level']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn-gold">Update</button>
            </form>
        </div>

        <?php elseif ($activeTab === 'bans'): ?>
        <!-- Bans Tab -->
        <div class="section">
            <h3>User Bans</h3>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>User Type</th>
                            <th>User ID</th>
                            <th>Reason</th>
                            <th>Banned By</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bans as $ban): ?>
                        <tr>
                            <td><?= e($ban['UserType']) ?></td>
                            <td><?= e($ban['UserID']) ?></td>
                            <td><?= e(substr($ban['BanReason'] ?? '', 0, 30)) ?></td>
                            <td><?= e($ban['BannedByType']) ?></td>
                            <td><?= e(date('M d, Y', strtotime($ban['BanDate']))) ?></td>
                            <td>
                                <span class="status-badge <?= $ban['IsActive'] ? 'status-banned' : 'status-unbanned' ?>">
                                    <?= $ban['IsActive'] ? 'Active' : 'Lifted' ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($ban['IsActive']): ?>
                                <form action="<?= e(action_url('unban-user')) ?>" method="POST" class="u-inline-59">
                                    <input type="hidden" name="target_user_type" value="<?= e($ban['UserType']) ?>">
                                    <input type="hidden" name="target_user_id" value="<?= e($ban['UserID']) ?>">
                                    <button type="submit" class="btn-sm btn-success">Unban</button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($bans)): ?>
                        <tr><td colspan="7" class="text-center">No ban records</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php elseif ($activeTab === 'sms'): ?>
        <!-- SMS Logs Tab -->
        <div class="section">
            <div class="section-header">
                <h3>SMS Logs</h3>
                <a href="<?= e(route('send-notification')) ?>" class="btn-gold">Send New SMS</a>
            </div>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Recipient</th>
                            <th>Type</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Sent At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($smsLogs as $sms): ?>
                        <tr>
                            <td><?= e($sms['SmsID']) ?></td>
                            <td><?= e(mask_phone($sms['RecipientPhone'])) ?></td>
                            <td><?= e($sms['MessageType']) ?></td>
                            <td><?= e(substr($sms['MessageContent'], 0, 40)) ?>...</td>
                            <td><span class="status-badge status-<?= strtolower($sms['Status']) ?>"><?= e($sms['Status']) ?></span></td>
                            <td><?= e(date('M d, H:i', strtotime($sms['SentAt']))) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($smsLogs)): ?>
                        <tr><td colspan="6" class="text-center">No SMS logs</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
render_responsive_shell_end();
render_end();
?>



