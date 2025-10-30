<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ScoutNova - Scout Dashboard</title>
  <link href="../css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background-color: #f5f6fa;
      font-family: 'Poppins', sans-serif;
    }
    .sidebar {
      height: 100vh;
      background-color: #001233;
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
    .player-card:hover {
      transform: translateY(-3px);
      transition: 0.3s ease;
    }
  </style>
</head>

<body class="sidebar-dark">

  <!-- SIDEBAR -->
  <div class="sidebar">
    <h4 class="text-center mb-4">🌍 ScoutNova</h4>
    <a href="#" class="active"><i class="bi bi-house-door me-2"></i> Dashboard</a>
    <a href="#"><i class="bi bi-search me-2"></i> Discover Players</a>
    <a href="#"><i class="bi bi-bar-chart-line me-2"></i> Analytics</a>
    <a href="#"><i class="bi bi-people me-2"></i> My Shortlist</a>
    <a href="#"><i class="bi bi-chat-dots me-2"></i> Messages</a>
    <a href="#"><i class="bi bi-gear me-2"></i> Settings</a>
  </div>

  <!-- MAIN DASHBOARD -->
  <div class="main">
    <div class="container-fluid">
      
      <!-- HEADER -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Welcome, Coach David 👋</h3>
        <button class="btn btn-dark"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
      </div>

      <!-- TOP CARDS -->
      <div class="row mb-4">
        <div class="col-md-3">
          <div class="card p-3">
            <h6 class="text-muted">Players Viewed</h6>
            <h3 class="fw-bold">128</h3>
            <small>+22 this week</small>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card p-3">
            <h6 class="text-muted">Players Shortlisted</h6>
            <h3 class="fw-bold">18</h3>
            <small>5 new additions</small>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card p-3">
            <h6 class="text-muted">Messages</h6>
            <h3 class="fw-bold">9</h3>
            <small>2 unread</small>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card p-3">
            <h6 class="text-muted">Upcoming Trials</h6>
            <h3 class="fw-bold">3</h3>
            <small>Next: Oct 14</small>
          </div>
        </div>
      </div>

      <!-- SEARCH BAR -->
      <div class="card p-3 mb-4">
        <div class="d-flex align-items-center">
          <i class="bi bi-search me-2"></i>
          <input type="text" class="form-control" placeholder="Search players by name, position, or country...">
        </div>
      </div>

      <!-- DISCOVER PLAYERS -->
      <div class="card p-3 mb-4">
        <h5 class="fw-bold mb-3"><i class="bi bi-people me-2"></i>Recommended Players</h5>
        <div class="row">
          
          <!-- PLAYER CARD -->
          <div class="col-md-4 mb-3">
            <div class="card p-2 player-card">
              <img src="https://via.placeholder.com/400x200" class="img-fluid rounded mb-2" alt="">
              <h6 class="fw-bold mb-0">Emeka Johnson</h6>
              <small class="text-muted">Forward • Nigeria • Age 20</small>
              <hr>
              <p class="mb-1"><b>Overall Score:</b> 87/100</p>
              <p class="mb-1"><b>Recent Form:</b> +5%</p>
              <button class="btn btn-sm btn-outline-primary mt-2 w-100"><i class="bi bi-eye me-1"></i>View Profile</button>
            </div>
          </div>

          <!-- PLAYER CARD -->
          <div class="col-md-4 mb-3">
            <div class="card p-2 player-card">
              <img src="https://via.placeholder.com/400x200" class="img-fluid rounded mb-2" alt="">
              <h6 class="fw-bold mb-0">Carlos Mendes</h6>
              <small class="text-muted">Point Guard • Brazil • Age 22</small>
              <hr>
              <p class="mb-1"><b>Overall Score:</b> 91/100</p>
              <p class="mb-1"><b>Recent Form:</b> +3%</p>
              <button class="btn btn-sm btn-outline-primary mt-2 w-100"><i class="bi bi-eye me-1"></i>View Profile</button>
            </div>
          </div>

          <!-- PLAYER CARD -->
          <div class="col-md-4 mb-3">
            <div class="card p-2 player-card">
              <img src="https://via.placeholder.com/400x200" class="img-fluid rounded mb-2" alt="">
              <h6 class="fw-bold mb-0">Aisha Bello</h6>
              <small class="text-muted">Midfielder • Ghana • Age 19</small>
              <hr>
              <p class="mb-1"><b>Overall Score:</b> 84/100</p>
              <p class="mb-1"><b>Recent Form:</b> +6%</p>
              <button class="btn btn-sm btn-outline-primary mt-2 w-100"><i class="bi bi-eye me-1"></i>View Profile</button>
            </div>
          </div>

        </div>
      </div>

      <!-- SHORTLIST SECTION -->
      <div class="card p-3 mb-4">
        <h5 class="fw-bold mb-3"><i class="bi bi-bookmark-star me-2"></i>My Shortlisted Players</h5>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead class="table-light">
              <tr>
                <th>Player</th>
                <th>Country</th>
                <th>Position</th>
                <th>Overall Score</th>
                <th>Last Contacted</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>John Adeyemi</td>
                <td>Nigeria</td>
                <td>Goalkeeper</td>
                <td>82</td>
                <td>Oct 2, 2025</td>
                <td><button class="btn btn-sm btn-outline-success"><i class="bi bi-chat"></i> Message</button></td>
              </tr>
              <tr>
                <td>Pedro Santos</td>
                <td>Brazil</td>
                <td>Forward</td>
                <td>90</td>
                <td>Sep 28, 2025</td>
                <td><button class="btn btn-sm btn-outline-success"><i class="bi bi-chat"></i> Message</button></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>

</body>
</html>
