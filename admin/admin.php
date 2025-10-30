<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ScoutNova - Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background-color: #f5f6fa;
      font-family: 'Poppins', sans-serif;
    }
    .sidebar {
      height: 100vh;
      background-color: #001845;
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
    .table td, .table th {
      vertical-align: middle;
    }
  </style>
</head>

<body class="sidebar-dark">

  <!-- SIDEBAR -->
  <div class="sidebar">
    <h4 class="text-center mb-4">🧭 ScoutNova Admin</h4>
    <a href="#" class="active"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
    <a href="#"><i class="bi bi-people me-2"></i> Players</a>
    <a href="#"><i class="bi bi-person-check me-2"></i> Scouts & Agents</a>
    <a href="#"><i class="bi bi-bar-chart-line me-2"></i> Analytics</a>
    <a href="#"><i class="bi bi-camera-video me-2"></i> Video Reviews</a>
    <a href="#"><i class="bi bi-cpu me-2"></i> AI Processing</a>
    <a href="#"><i class="bi bi-gear me-2"></i> Settings</a>
  </div>

  <!-- MAIN DASHBOARD -->
  <div class="main">
    <div class="container-fluid">
      
      <!-- HEADER -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Admin Control Panel</h3>
        <button class="btn btn-dark"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
      </div>

      <!-- TOP STATS -->
      <div class="row mb-4">
        <div class="col-md-3">
          <div class="card p-3">
            <h6 class="text-muted">Total Players</h6>
            <h3 class="fw-bold">3,245</h3>
            <small>+120 this month</small>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card p-3">
            <h6 class="text-muted">Active Scouts</h6>
            <h3 class="fw-bold">540</h3>
            <small>+25 this week</small>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card p-3">
            <h6 class="text-muted">Videos Uploaded</h6>
            <h3 class="fw-bold">12,879</h3>
            <small>320 new videos</small>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card p-3">
            <h6 class="text-muted">AI Analyses Completed</h6>
            <h3 class="fw-bold">11,402</h3>
            <small>89% success rate</small>
          </div>
        </div>
      </div>

      <!-- SYSTEM OVERVIEW -->
      <div class="row mb-4">
        <div class="col-md-8">
          <div class="card p-3">
            <h5 class="fw-bold mb-3"><i class="bi bi-graph-up-arrow me-2"></i>Platform Activity Overview</h5>
            <div class="text-center text-muted py-5">
              <em>[Graph: Weekly activity of uploads, logins, and AI analysis trends]</em>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card p-3">
            <h5 class="fw-bold mb-3"><i class="bi bi-exclamation-circle me-2"></i>System Alerts</h5>
            <ul class="list-unstyled mb-0">
              <li>⚠️ 3 videos failed to process (AI timeout)</li>
              <li>🧾 2 scouts pending verification</li>
              <li>🔒 1 player account suspended</li>
              <li>📊 Analytics model update pending</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- PLAYER MANAGEMENT -->
      <div class="card p-3 mb-4">
        <h5 class="fw-bold mb-3"><i class="bi bi-people me-2"></i>Recent Player Signups</h5>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead class="table-light">
              <tr>
                <th>Name</th>
                <th>Country</th>
                <th>Sport</th>
                <th>Status</th>
                <th>Registered</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Emeka Johnson</td>
                <td>Nigeria</td>
                <td>Football</td>
                <td><span class="badge bg-success">Active</span></td>
                <td>Oct 3, 2025</td>
                <td><button class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> View</button></td>
              </tr>
              <tr>
                <td>Carlos Mendes</td>
                <td>Brazil</td>
                <td>Basketball</td>
                <td><span class="badge bg-warning text-dark">Pending</span></td>
                <td>Oct 1, 2025</td>
                <td><button class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> View</button></td>
              </tr>
              <tr>
                <td>Aisha Bello</td>
                <td>Ghana</td>
                <td>Football</td>
                <td><span class="badge bg-success">Active</span></td>
                <td>Sep 28, 2025</td>
                <td><button class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> View</button></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- AI PROCESSING STATUS -->
      <div class="card p-3 mb-4">
        <h5 class="fw-bold mb-3"><i class="bi bi-cpu me-2"></i>AI Processing Status</h5>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead class="table-light">
              <tr>
                <th>Video ID</th>
                <th>Player</th>
                <th>Status</th>
                <th>Started</th>
                <th>Duration</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>#VID-2032</td>
                <td>John Adeyemi</td>
                <td><span class="badge bg-success">Complete</span></td>
                <td>Oct 7, 2025</td>
                <td>45s</td>
                <td><button class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></button></td>
              </tr>
              <tr>
                <td>#VID-2033</td>
                <td>Pedro Santos</td>
                <td><span class="badge bg-danger">Failed</span></td>
                <td>Oct 7, 2025</td>
                <td>—</td>
                <td><button class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-repeat"></i></button></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>

</body>
</html>
