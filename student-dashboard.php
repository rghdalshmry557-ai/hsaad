<?php
/**
 * ملاحظات توثيقية للصفحة: student-dashboard.php
 * الغرض: لوحة الطالب (ملخص الأداء والمحفظة).
 *
 * الدوال المستدعاة في هذه الصفحة ولماذا:
 * - all_funds(): جلب قائمة الصناديق (الكل أو المنشور فقط). القيمة الراجعة: array.
 * - current_student(): جلب بيانات الطالب الحالي من الجلسة/القاعدة. القيمة الراجعة: array.
 * - current_wallet(): جلب بيانات محفظة الطالب. القيمة الراجعة: array.
 * - e(): تعقيم النص قبل الطباعة لمنع XSS. القيمة الراجعة: string.
 * - is_logged_in_as_student(): التحقق من أن الجلسة الحالية لطالب. القيمة الراجعة: bool.
 * - redirect_to(): إيقاف الصفحة الحالية وتحويل المستخدم لمسار مناسب. القيمة الراجعة: void.
 * - render_end(): إغلاق الصفحة وتحميل JS العام. القيمة الراجعة: void.
 * - render_footer(): إظهار تذييل الصفحة عند الحاجة. القيمة الراجعة: void.
 * - render_head(): بدء هيكل HTML وتحميل ملفات CSS. القيمة الراجعة: void.
 * - render_responsive_shell_end(): إغلاق الغلاف الموحد للواجهة. القيمة الراجعة: void.
 * - render_responsive_shell_start(): بدء الغلاف الموحد للواجهة (Header/Sidebar). القيمة الراجعة: void.
 * - route(): بناء رابط داخلي آمن للانتقال بين الصفحات. القيمة الراجعة: string.
 * - set_flash(): تخزين رسالة مؤقتة للواجهة (نجاح/خطأ). القيمة الراجعة: void.
 * - student_dashboard_cards(): بناء بطاقات ملخص الصناديق في لوحة الطالب. القيمة الراجعة: array.
 */
if (!is_logged_in_as_student()) {
    set_flash('error', 'Access denied. Student login required.');
    redirect_to('login', ['role' => 'student']);
}

$student = current_student();
$studentId = (int) $student['StudentID'];
$wallet = current_wallet($studentId);
$allFunds = all_funds(true);
$dashCards = student_dashboard_cards($studentId);
$subscribedCards = array_values(array_filter($dashCards, fn($c) => $c['subscribed'] === true));
$totalFunds = count($allFunds);
$totalSubscribed = count($subscribedCards);

$totalProfit = 0;
$totalInvested = 0;
foreach ($subscribedCards as $c) {
    $totalProfit += $c['current_value'] - $c['invested'];
    $totalInvested += $c['invested'];
}

$walletBalance = (float) ($wallet['InvestmentWalletTotalAmount'] ?? 0);
$moneyAdded = (float) ($wallet['InvestmentWalletCredit'] ?? 0);
$moneyWithdrawn = (float) ($wallet['InvestmentWalletReturn'] ?? 0);

render_head('Student Dashboard');
render_responsive_shell_start('');
?>

<div class="sd-page">

    <!-- ==================== PROFILE CARD ==================== -->
    <section class="sd-profile-card">
        <div class="sd-avatar">
            <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="40" cy="30" r="14" stroke="#333" stroke-width="2.5" fill="none"/>
                <path d="M12 72c0-15.464 12.536-28 28-28s28 12.536 28 28" stroke="#333" stroke-width="2.5" fill="none" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="sd-profile-grid">
            <div class="sd-field">
                <span class="sd-field-label">Full Name:</span>
                <div class="sd-field-value"><?= e(strtoupper($student['StudentNameFirst'] . ' ' . $student['StudentNameLast'])) ?></div>
            </div>
            <div class="sd-field">
                <span class="sd-field-label">Major:</span>
                <div class="sd-field-value"><?= e(strtoupper($student['StudentMajor'] ?? 'N/A')) ?></div>
            </div>
            <div class="sd-field">
                <span class="sd-field-label">ID</span>
                <div class="sd-field-value"><?= e($student['StudentID']) ?></div>
            </div>
            <div class="sd-field">
                <span class="sd-field-label">Phone Number:</span>
                <div class="sd-field-value"><?= e($student['StudentPhoneNumber'] ?? 'N/A') ?></div>
            </div>
        </div>
    </section>

    <!-- ==================== STATS ROW ==================== -->
    <section class="sd-stats-row">
        <div class="sd-stat-card">
            <div class="sd-stat-icon-circle">
                <svg viewBox="0 0 24 24" fill="none" stroke="#45644A" stroke-width="1.8"><path d="M21 12V7H5a2 2 0 010-4h14v4"/><path d="M3 5v14a2 2 0 002 2h16v-5"/><path d="M18 12a2 2 0 100 4 2 2 0 000-4z"/></svg>
            </div>
            <span class="sd-stat-title">Wallet Balance</span>
            <span class="sd-stat-amount"><?= number_format($walletBalance, 2, ',', '.') ?> <small>SAR</small></span>
            <span class="sd-stat-change positive">Up 12.5% since last month</span>
        </div>
        <div class="sd-stat-card">
            <div class="sd-stat-icon-circle">
                <svg viewBox="0 0 24 24" fill="none" stroke="#45644A" stroke-width="1.8"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>
            </div>
            <span class="sd-stat-title">Total Invested</span>
            <span class="sd-stat-amount"><?= number_format($totalInvested, 0, ',', ',') ?> <small>SAR</small></span>
            <span class="sd-stat-change positive">Up 8.3% since last month</span>
        </div>
        <div class="sd-stat-card">
            <div class="sd-stat-icon-circle">
                <svg viewBox="0 0 24 24" fill="none" stroke="#45644A" stroke-width="1.8"><path d="M12 20V10"/><path d="M18 20V4"/><path d="M6 20v-4"/></svg>
            </div>
            <span class="sd-stat-title">Total Profit</span>
            <span class="sd-stat-amount"><?= number_format($totalProfit, 0, ',', ',') ?> <small>SAR</small></span>
            <span class="sd-stat-change positive">Up 15.2% since last month</span>
        </div>
        <div class="sd-stat-card">
            <div class="sd-stat-icon-circle">
                <svg viewBox="0 0 24 24" fill="none" stroke="#45644A" stroke-width="1.8"><path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/></svg>
            </div>
            <span class="sd-stat-title">Active Funds</span>
            <span class="sd-stat-amount"><?= $totalSubscribed ?></span>
            <span class="sd-stat-change neutral">of <?= $totalFunds ?> funds</span>
        </div>
    </section>

    <!-- ==================== MY FUNDS SECTION ==================== -->
    <section class="sd-funds-section">
        <div class="sd-section-bar">
            <div></div>
            <h2>My Funds</h2>
            <span class="sd-live-dot">Live updates</span>
        </div>

        <?php if (!empty($subscribedCards)): ?>
        <div class="sd-funds-carousel-wrap">
            <button type="button" class="sd-carousel-btn prev" onclick="sdScrollFunds(-1)">&lsaquo;</button>
            <div class="sd-funds-carousel" id="sdFundsCarousel">
                <?php foreach ($subscribedCards as $idx => $card):
                    $profit = $card['current_value'] - $card['invested'];
                    $pctReturn = $card['invested'] > 0 ? ($profit / $card['invested']) * 100 : 0;
                    $isPos = $profit >= 0;
                    $fundIcons = ['sukuk' => 'Fund', 'gold' => 'Fund', 'real-estate' => 'Fund'];
                ?>
                <div class="sd-fund-card">
                    <div class="sd-fund-icon-row">
                        <div class="sd-fund-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#45644A" stroke-width="1.5"><path d="M3 21h18"/><path d="M5 21V7l7-4 7 4v14"/><path d="M9 21v-6h6v6"/><path d="M9 9h1"/><path d="M14 9h1"/><path d="M9 13h1"/><path d="M14 13h1"/></svg>
                        </div>
                        <h3><?= e($card['fund']['FundTitle']) ?></h3>
                    </div>
                    <div class="sd-fund-details">
                        <div class="sd-fund-row">
                            <span>The invested amount</span>
                            <strong><?= number_format($card['invested'], 0, '.', ',') ?></strong>
                        </div>
                        <div class="sd-fund-row">
                            <span>Current value</span>
                            <strong><?= number_format($card['current_value'], 0, '.', ',') ?></strong>
                        </div>
                        <div class="sd-fund-row profit">
                            <span>Profit</span>
                            <div class="sd-profit-values">
                                <strong class="<?= $isPos ? 'positive' : 'negative' ?>"><?= $isPos ? '+' : '' ?><?= number_format($profit, 0, '.', ',') ?> SAR</strong>
                                <em class="<?= $isPos ? 'positive' : 'negative' ?>"><?= $isPos ? '+' : '' ?><?= number_format($pctReturn, 1) ?>%</em>
                            </div>
                        </div>
                    </div>
                    <div class="sd-mini-chart">
                        <svg viewBox="0 0 200 50" preserveAspectRatio="none">
                            <linearGradient id="cg<?= $idx ?>" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#45644A" stop-opacity="0.3"/>
                                <stop offset="100%" stop-color="#45644A" stop-opacity="0"/>
                            </linearGradient>
                            <polygon fill="url(#cg<?= $idx ?>)"
                                points="0,45 20,42 40,38 60,40 80,35 100,30 120,28 140,22 160,18 180,12 200,5 200,50 0,50" />
                            <polyline fill="none" stroke="#45644A" stroke-width="2"
                                points="0,45 20,42 40,38 60,40 80,35 100,30 120,28 140,22 160,18 180,12 200,5" />
                        </svg>
                    </div>
                    <a href="<?= e(route('fund', ['slug' => $card['fund']['slug']])) ?>" class="sd-enter-btn">Enter</a>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="sd-carousel-btn next" onclick="sdScrollFunds(1)">&rsaquo;</button>
        </div>
        <?php else: ?>
        <div class="sd-empty-funds">
            <p>You haven't subscribed to any funds yet.</p>
            <a href="<?= e(route('search-funds')) ?>" class="sd-enter-btn u-inline-81">Browse Available Funds</a>
        </div>
        <?php endif; ?>

        <div class="sd-view-perf">
            <a href="<?= e(route('reports')) ?>" class="sd-perf-btn">View Performance Details</a>
        </div>
    </section>

    <!-- ==================== MY WALLET SECTION ==================== -->
    <section class="sd-wallet-section">
        <div class="sd-section-bar">
            <div></div>
            <h2>My Wallet <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.8" class="u-inline-82"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg></h2>
            <span class="sd-live-dot">Updated just now</span>
        </div>
        <div class="sd-wallet-grid">
            <!-- Balance Card -->
            <div class="sd-wallet-balance-card">
                <div class="sd-wb-header">
                    <span class="sd-wb-label">Balance</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="#999" stroke-width="1.5" class="sd-wb-svg-icon"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                </div>
                <div class="sd-wb-amount"><?= number_format($walletBalance, 2, ',', '.') ?> <small>SAR</small></div>
                <span class="sd-stat-change positive">Up 12.5% since last month</span>
                <div class="sd-wb-actions">
                    <a href="<?= e(route('withdraw')) ?>" class="sd-btn-withdraw">Withdraw</a>
                </div>
            </div>

            <!-- Balance History Chart -->
            <div class="sd-wallet-chart-card">
                <h4>Balance History (SAR)</h4>
                <div class="sd-chart-area">
                    <svg viewBox="0 0 400 200" preserveAspectRatio="xMidYMid meet" class="sd-history-svg">
                        <!-- Y axis labels -->
                        <text x="30" y="180" text-anchor="end" fill="#888" font-size="11">0</text>
                        <text x="30" y="145" text-anchor="end" fill="#888" font-size="11">5</text>
                        <text x="30" y="110" text-anchor="end" fill="#888" font-size="11">10</text>
                        <text x="30" y="75" text-anchor="end" fill="#888" font-size="11">15</text>
                        <text x="30" y="40" text-anchor="end" fill="#888" font-size="11">20</text>
                        <!-- Grid lines -->
                        <line x1="40" y1="175" x2="380" y2="175" stroke="#e8e8e8" stroke-width="0.5"/>
                        <line x1="40" y1="140" x2="380" y2="140" stroke="#e8e8e8" stroke-width="0.5"/>
                        <line x1="40" y1="105" x2="380" y2="105" stroke="#e8e8e8" stroke-width="0.5"/>
                        <line x1="40" y1="70" x2="380" y2="70" stroke="#e8e8e8" stroke-width="0.5"/>
                        <line x1="40" y1="35" x2="380" y2="35" stroke="#e8e8e8" stroke-width="0.5"/>
                        <!-- X axis labels -->
                        <text x="70" y="198" text-anchor="middle" fill="#888" font-size="10">May 1</text>
                        <text x="140" y="198" text-anchor="middle" fill="#888" font-size="10">May 8</text>
                        <text x="210" y="198" text-anchor="middle" fill="#888" font-size="10">May 15</text>
                        <text x="280" y="198" text-anchor="middle" fill="#888" font-size="10">May 29</text>
                        <text x="350" y="198" text-anchor="middle" fill="#888" font-size="10">Jun 5</text>
                        <!-- Area fill -->
                        <linearGradient id="wg" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#45644A" stop-opacity="0.25"/>
                            <stop offset="100%" stop-color="#45644A" stop-opacity="0.02"/>
                        </linearGradient>
                        <polygon fill="url(#wg)" points="70,165 140,145 210,130 280,100 350,72 350,175 70,175" />
                        <polyline fill="none" stroke="#45644A" stroke-width="2.5" stroke-linejoin="round"
                            points="70,165 140,145 210,130 280,100 350,72" />
                        <!-- End dot -->
                        <circle cx="350" cy="72" r="4" fill="#45644A" stroke="#fff" stroke-width="2"/>
                        <!-- Tooltip box -->
                        <rect x="290" y="42" width="85" height="30" rx="6" fill="#fff" stroke="#e0e0e0" stroke-width="0.5"/>
                        <text x="298" y="54" fill="#555" font-size="8">Jun 5</text>
                        <text x="298" y="66" fill="#18442A" font-size="9" font-weight="bold">15,00 SAR</text>
                        <text x="352" y="66" fill="#2e7d32" font-size="8">Up 12.5%</text>
                    </svg>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="sd-wallet-quick-card">
                <div class="sd-wq-header">
                    <h4>Quick Stats</h4>
                    <span class="sd-wq-badge">This Month</span>
                </div>
                <div class="sd-wq-item">
                    <span class="sd-wq-label">Money Added</span>
                    <span class="sd-wq-value"><?= number_format($moneyAdded, 2, ',', '.') ?> <small>SAR</small></span>
                    <span class="sd-stat-change positive">Up 25.0%</span>
                </div>
                <div class="sd-wq-item">
                    <span class="sd-wq-label">Money Withdrawn</span>
                    <span class="sd-wq-value"><?= number_format($moneyWithdrawn, 2, ',', '.') ?> <small>SAR</small></span>
                    <span class="sd-stat-change negative">Down 10.0%</span>
                </div>
            </div>
        </div>
    </section>

</div>

<?php render_footer(); ?>

<script>
function sdScrollFunds(dir) {
    const c = document.getElementById('sdFundsCarousel');
    if (c) {
        const card = c.querySelector('.sd-fund-card');
        const w = card ? card.offsetWidth + 20 : 260;
        c.scrollBy({ left: dir * w, behavior: 'smooth' });
    }
}
</script>

<?php
render_responsive_shell_end();
render_end();
?>



