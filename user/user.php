<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ScoutNova Player Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background-color: #f5f6fa;
      font-family: 'Poppins', sans-serif;
    }
    .sidebar {
      height: 100vh;
      background-color: #0d1b2a;
      color: white;
      position: fixed;
      width: 250px;
      padding-top: 1rem;
    }
    .sidebar a {
      color: #ccc;
      text-decoration: none;
      display: block;
      padding: 12px 20px;
      border-radius: 6px;
      margin: 5px 0;
    }
    .sidebar a:hover, .sidebar a.active {
      background-color: #1b263b;
      color: #fff;
    }
    .main {
      margin-left: 260px;
      padding: 2rem;
    }
    .card {
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
  </style>
</head>

<body class="sidebar-dark">

  <!-- SIDEBAR -->
  <div class="sidebar">
    <h4 class="text-center mb-4">🏆 ScoutNova</h4>
    <a href="#" class="active"><i class="bi bi-house-door me-2"></i> Dashboard</a>
    <a href="#"><i class="bi bi-person me-2"></i> Profile</a>
    <a href="#"><i class="bi bi-cloud-upload me-2"></i> My Videos</a>
    <a href="#"><i class="bi bi-bar-chart-line me-2"></i> Analytics</a>
    <a href="#"><i class="bi bi-graph-up-arrow me-2"></i> POD Tracker</a>
    <a href="#"><i class="bi bi-chat-dots me-2"></i> Messages</a>
    <a href="#"><i class="bi bi-gear me-2"></i> Settings</a>
  </div>

  <!-- MAIN DASHBOARD -->
  <div class="main">
    <div class="container-fluid">
      
      <!-- HEADER -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Welcome back, Emeka 👋</h3>
        <button class="btn btn-dark"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
      </div>

      <!-- TOP CARDS -->
      <div class="row mb-4">
        <div class="col-md-3">
          <div class="card p-3">
            <h6 class="text-muted">Overall Score</h6>
            <h3 class="fw-bold">87 / 100</h3>
            <small>+5% vs last week</small>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card p-3">
            <h6 class="text-muted">Videos Uploaded</h6>
            <h3 class="fw-bold">12</h3>
            <small>2 awaiting analysis</small>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card p-3">
            <h6 class="text-muted">Profile Views</h6>
            <h3 class="fw-bold">243</h3>
            <small>+18 this week</small>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card p-3">
            <h6 class="text-muted">Scout Messages</h6>
            <h3 class="fw-bold">4</h3>
            <small>1 new this week</small>
          </div>
        </div>
      </div>

      <!-- POD & PERFORMANCE -->
      <div class="row mb-4">
        <div class="col-md-8">
          <div class="card p-3">
            <h5 class="fw-bold mb-3"><i class="bi bi-graph-up-arrow me-2"></i>Player Overtime Development (POD)</h5>
            <div class="text-center text-muted py-5">
              <em>[Graph showing performance trends over time]</em>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card p-3">
            <h5 class="fw-bold mb-3"><i class="bi bi-activity me-2"></i>Latest Performance Insights</h5>
            <ul class="list-unstyled mb-0">
              <li>⚽ Shot Accuracy: <b>92%</b></li>
              <li>🎯 Pass Completion: <b>88%</b></li>
              <li>🏃 Speed Index: <b>7.4</b></li>
              <li>📈 Growth Rate: <b>+14%</b></li>
            </ul>
          </div>
        </div>
      </div>

      <!-- RECENT VIDEOS -->
      <div class="card p-3 mb-4">
        <h5 class="fw-bold mb-3"><i class="bi bi-camera-video me-2"></i>Recent Video Uploads</h5>
        <div class="row">
          <div class="col-md-4">
            <div class="card p-2 text-center">
              <div class="ratio ratio-16x9 bg-light mb-2">
                <em>Video Preview</em>
              </div>
              <small class="text-muted">Uploaded: Oct 2, 2025</small>
              <p class="mb-0"><b>Analysis Status:</b> Complete</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card p-2 text-center">
              <div class="ratio ratio-16x9 bg-light mb-2">
                <em>Video Preview</em>
              </div>
              <small class="text-muted">Uploaded: Sep 25, 2025</small>
              <p class="mb-0"><b>Analysis Status:</b> Pending</p>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

</body>
</html>
