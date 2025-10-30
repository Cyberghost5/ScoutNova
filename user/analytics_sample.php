<?php
// Example: Player Analytics Dashboard (static demo)
session_start();
$playerName = "John Doe"; // replace with session data
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Player Analytics - ScoutNova</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body { background-color: #f8f9fa; }
    .card { border: none; border-radius: 1rem; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
    .icon-box { font-size: 2rem; color: #0d6efd; }
    .chart-container { height: 250px; }
  </style>
</head>
<body class="sidebar-dark">
<div class="container my-4">
  <div class="text-center mb-4">
    <h2 class="fw-bold">⚽ Player Analytics Dashboard</h2>
    <p class="text-muted">Welcome back, <?= htmlspecialchars($playerName) ?> — track your performance, growth, and visibility.</p>
  </div>

  <div class="row g-4">
    <!-- Performance Overview -->
    <div class="col-md-4">
      <div class="card p-4 text-center">
        <div class="icon-box mb-2"><i class="bi bi-bar-chart-line"></i></div>
        <h5 class="fw-bold">Performance Overview</h5>
        <p class="text-muted mb-2">Average AI score & key stats</p>
        <h3 class="fw-bold text-primary">82/100</h3>
        <small>Last analyzed: 3 days ago</small>
      </div>
    </div>

    <!-- Video Analytics -->
    <div class="col-md-4">
      <div class="card p-4 text-center">
        <div class="icon-box mb-2"><i class="bi bi-camera-video"></i></div>
        <h5 class="fw-bold">Video Analytics</h5>
        <p class="text-muted mb-2">Uploaded videos & ratings</p>
        <h3 class="fw-bold text-primary">12 Videos</h3>
        <small>Avg Rating: 4.6⭐</small>
      </div>
    </div>

    <!-- Visibility -->
    <div class="col-md-4">
      <div class="card p-4 text-center">
        <div class="icon-box mb-2"><i class="bi bi-people"></i></div>
        <h5 class="fw-bold">Visibility Metrics</h5>
        <p class="text-muted mb-2">Scouts interactions & views</p>
        <h3 class="fw-bold text-primary">248 Views</h3>
        <small>14 scouts engaged</small>
      </div>
    </div>
  </div>

  <!-- Development & Insights -->
  <div class="row g-4 mt-4">
    <div class="col-md-6">
      <div class="card p-4">
        <h5 class="fw-bold mb-3"><i class="bi bi-graph-up text-primary me-2"></i>Player Overtime Development</h5>
        <div class="chart-container">
          <canvas id="performanceChart"></canvas>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card p-4">
        <h5 class="fw-bold mb-3"><i class="bi bi-lightbulb text-primary me-2"></i>AI Insights Summary</h5>
        <ul class="list-unstyled">
          <li>⚡ Ball control improved by <b>15%</b> this month.</li>
          <li>🎯 Passing accuracy dropped slightly vs last week.</li>
          <li>🔥 Top strength: <b>Dribbling</b> • Area to improve: <b>Finishing</b></li>
        </ul>
      </div>
    </div>
  </div>

  <!-- Achievements -->
  <div class="row g-4 mt-4">
    <div class="col-12">
      <div class="card p-4 text-center">
        <div class="icon-box mb-2"><i class="bi bi-trophy"></i></div>
        <h5 class="fw-bold mb-2">Achievements & Badges</h5>
        <div class="d-flex flex-wrap justify-content-center gap-3">
          <span class="badge bg-success p-3">🏅 Top 10% in Passing</span>
          <span class="badge bg-warning p-3">⭐ Scouted by 3 Agents</span>
          <span class="badge bg-primary p-3">📈 5 Consecutive Uploads</span>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
const ctx = document.getElementById('performanceChart');
new Chart(ctx, {
  type: 'line',
  data: {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
    datasets: [{
      label: 'AI Score',
      data: [65, 72, 75, 80, 82, 85],
      borderColor: '#0d6efd',
      tension: 0.3,
      fill: false
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },    
    scales: { y: { beginAtZero: true, max: 100 } }
  }
});
</script>
</body>
</html>
