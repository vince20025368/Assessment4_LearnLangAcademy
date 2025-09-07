<?php
// admin_dashboard.php — LearnLang Admin Dashboard
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
$active = 'admin_dashboard';

// Auth guard: only admins
if (empty($_SESSION['account_id']) || ($_SESSION['account_type'] ?? '') !== 'admin') {
  header('Location: login.php');
  exit;
}

// Include backend metrics
require __DIR__ . '/backend/dashboard.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Dashboard | LearnLang</title>
  <link rel="stylesheet" href="css/style.css" />
  <link rel="icon" href="images/favicon.ico" type="image/x-icon" />
</head>
<body class="site">

  <?php include __DIR__ . '/partials/header.php'; ?>

  <main class="dashboard-layout">
    <!-- Sidebar -->
    <aside class="side-panel">
      <?php include __DIR__ . '/partials/sidebar.php'; ?>
    </aside>

    <!-- Content -->
    <section class="dashboard-content" aria-label="Dashboard">

      <!-- KPI Grid -->
      <section class="dash3-card kpi-grid" aria-label="Key metrics">
        <article class="kpi">
          <p class="kpi-num"><?= number_format($total_accounts) ?></p>
          <h2>Accounts</h2>
          <p class="kpi-sub">All registered accounts</p>
        </article>

        <article class="kpi">
          <p class="kpi-num"><?= number_format($total_inquiries) ?></p>
          <h2>Inquiries</h2>
          <p class="kpi-sub">Total contact inquiries</p>
        </article>

        <article class="kpi">
          <p class="kpi-num"><?= number_format($enroll_total) ?></p>
          <h2>Enrollments</h2>
          <p class="kpi-sub">Top 8 courses • Peak <?= number_format($enroll_peak) ?> • Avg <?= number_format($enroll_avg) ?></p>
        </article>

        <article class="kpi">
          <p class="kpi-num"><?= (int)$verified_pct ?>%</p>
          <h2>Verified</h2>
          <p class="kpi-sub">of accounts</p>
        </article>
      </section>

      <!-- Enrollments by Course -->
      <section class="dash3-card" aria-labelledby="enroll-title">
        <header class="dash3-card__head">
          <h2 id="enroll-title">Enrollments by Course</h2>
        </header>
        <canvas id="enrollBar" height="260"></canvas>
        <footer class="dash3-card__foot">
          <span class="muted">Total <?= number_format($enroll_total) ?> • Peak <?= number_format($enroll_peak) ?> • Avg <?= number_format($enroll_avg) ?></span>
        </footer>
      </section>
    </section>
  </main>

  <?php include __DIR__ . '/partials/footer.php'; ?>

  <!-- Charts -->
  <script>
  (function(){
    const labels = <?= json_encode($labels_courses) ?>;
    const data   = <?= json_encode($data_courses) ?>;

    const canvas = document.getElementById('enrollBar');
    const ctx = canvas.getContext('2d');

    const W = canvas.width = canvas.clientWidth;
    const H = canvas.height;

    const pad = { top: 20, right: 20, bottom: 40, left: 40 };
    const max = Math.max(...data, 1);
    const barWidth = (W - pad.left - pad.right) / data.length * 0.7;
    const xStep = (W - pad.left - pad.right) / data.length;

    ctx.clearRect(0, 0, W, H);

    // Axes
    ctx.strokeStyle = '#e6ecf3';
    ctx.beginPath();
    ctx.moveTo(pad.left, H - pad.bottom);
    ctx.lineTo(W - pad.right, H - pad.bottom);
    ctx.stroke();

    // Bars
    ctx.fillStyle = '#0d4061';
    data.forEach((val, i) => {
      const x = pad.left + i * xStep + (xStep - barWidth) / 2;
      const y = H - pad.bottom - (val / max) * (H - pad.top - pad.bottom);
      const h = (val / max) * (H - pad.top - pad.bottom);
      ctx.fillRect(x, y, barWidth, h);
    });

    // Labels (X)
    ctx.fillStyle = '#122033';
    ctx.font = '12px Segoe UI, sans-serif';
    ctx.textAlign = 'center';
    labels.forEach((lab, i) => {
      const x = pad.left + i * xStep + xStep / 2;
      ctx.fillText(lab, x, H - pad.bottom + 14);
    });

    // Y axis labels
    ctx.textAlign = 'right';
    ctx.fillStyle = '#64748b';
    for (let j = 0; j <= 4; j++) {
      const val = Math.round((max * j) / 4);
      const y = H - pad.bottom - (val / max) * (H - pad.top - pad.bottom);
      ctx.fillText(val, pad.left - 6, y + 4);
    }
  })();
  </script>
</body>
</html>
