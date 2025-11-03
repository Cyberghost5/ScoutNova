<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="list active text-white">
      <div class="profile"><img src="<?php echo $settings['site_url']; ?>user/<?php echo (!empty($user['photo'])) ? 'images/'.$user['photo'] : 'images/profile.jpg'; ?>" class="img-circle elevation-2" height="60" width="60" alt="image" style="border-radius:50%;"><span class="online"></span></div>
      <div class="info mb-3 mt-3">
        <h5><?php echo $user['username']; ?>
        <?php if ($user['subscription_status'] == 'active'): ?>
        <sup><i class="mdi mdi-checkbox-marked-circle-outline text-success" style="font-size:10px;"></i></sup> 
        <?php endif; ?>
        </h5>
        <p>Type: 
            <?php if($user['role'] == 'agent'): 
              $role = "Agent/Scout";
            ?>
            <span class="badge badge-primary">Agent</span>
            <?php elseif($user['role'] == 'user'): 
              $role = "Player";
            ?>
            <span class="badge badge-info">Player</span>
            <?php endif; ?>
        </p>
      </div>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="<?php echo $settings['site_url']; ?>user/home">
        <i class="mdi mdi-home menu-icon"></i>
        <span class="menu-title">Dashboard</span>
      </a>
    </li>
    <?php if($user['role'] == 'user'): ?>
      <?php if($user['verified'] == 0 || $user['verified'] == 2 || $user['verified'] == 3): ?>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo $settings['site_url']; ?>user/player-verification">
          <i class="mdi mdi-account-check menu-icon"></i>
          <span class="menu-title">Verification</span>
          <span class="badge right text-danger"><i class="mdi mdi-check-decagram"></i></span>
        </a>
      </li>
      <?php endif; ?>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo $settings['site_url']; ?>user/settings">
          <i class="mdi mdi-account-outline menu-icon"></i>
          <span class="menu-title">Profile</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo $settings['site_url']; ?>user/discover">
          <i class="mdi mdi-account-search menu-icon"></i>
          <span class="menu-title">Discover Players</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo $settings['site_url']; ?>user/videos">
          <i class="mdi mdi-cloud-upload menu-icon"></i>
          <span class="menu-title">My Videos</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo $settings['site_url']; ?>user/subscriptions">
          <i class="mdi mdi-credit-card-multiple menu-icon"></i>
          <span class="menu-title">Subscription</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo $settings['site_url']; ?>user/analytics">
          <i class="mdi mdi-chart-bar menu-icon"></i>
          <span class="menu-title">Analytics</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo $settings['site_url']; ?>user/pod-tracker">
          <i class="mdi mdi-chart-line menu-icon"></i>
          <span class="menu-title">POD Tracker</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo $settings['site_url']; ?>user/messages">
          <i class="mdi mdi-message-text-outline menu-icon"></i>
          <span class="menu-title">Messages</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo $settings['site_url']; ?>user/#profile" data-toggle="modal">
          <i class="mdi mdi-settings menu-icon"></i>
          <span class="menu-title">Settings</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo $settings['site_url']; ?>user/logout">
          <i class="mdi mdi-logout menu-icon"></i>
          <span class="menu-title">Logout</span>
        </a>
      </li>
    <?php elseif($user['role'] == 'agent'): ?>
      <?php if($user['verified'] == 0 || $user['verified'] == 2 || $user['verified'] == 3): ?>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo $settings['site_url']; ?>user/scout-verification">
          <i class="mdi mdi-account-check menu-icon"></i>
          <span class="menu-title">Verification</span>
          <span class="badge right text-danger"><i class="mdi mdi-check-decagram"></i></span>
        </a>
      </li>
      <?php endif; ?>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo $settings['site_url']; ?>user/subscriptions">
          <i class="mdi mdi-credit-card-multiple menu-icon"></i>
          <span class="menu-title">Subscription</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo $settings['site_url']; ?>user/discover">
          <i class="mdi mdi-account-search menu-icon"></i>
          <span class="menu-title">Discover Players</span>
        </a>
      </li>
      <!-- <li class="nav-item">
        <a class="nav-link" href="<?php echo $settings['site_url']; ?>user/analytics">
          <i class="mdi mdi-chart-bar menu-icon"></i>
          <span class="menu-title">Analytics</span>
        </a>
      </li> -->
      <li class="nav-item">
        <a class="nav-link" href="<?php echo $settings['site_url']; ?>user/watchlist">
          <i class="mdi mdi-account-multiple-outline menu-icon"></i>
          <span class="menu-title">My Watchlist</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo $settings['site_url']; ?>user/messages">
          <i class="mdi mdi-message-text-outline menu-icon"></i>
          <span class="menu-title">Messages</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#profile" data-toggle="modal">
          <i class="mdi mdi-settings menu-icon"></i>
          <span class="menu-title">Settings</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo $settings['site_url']; ?>user/logout">
          <i class="mdi mdi-logout menu-icon"></i>
          <span class="menu-title">Logout</span>
        </a>
      </li>
    <?php endif; ?>
  </ul>
</nav>