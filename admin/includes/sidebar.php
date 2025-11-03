<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="list active text-white">
      <div class="profile"><img src="<?php echo $settings['site_url']; ?>admin/<?php echo (!empty($admin['photo'])) ? 'images/'.$admin['photo'] : 'images/profile.jpg'; ?>" class="img-circle elevation-2" height="60" width="60" alt="image" style="border-radius:50%;"><span class="online"></span></div>
      <div class="info mb-3 mt-3">
        <h5><?php echo $admin['firstname'];?> <?php echo $admin['lastname'];?></h5>
      </div>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="<?php echo $settings['site_url']; ?>admin/home">
        <i class="mdi mdi-home menu-icon"></i>
        <span class="menu-title">Dashboard</span>
      </a>
    </li>
    
    <li class="nav-item">
      <a class="nav-link" href="<?php echo $settings['site_url']; ?>admin/players">
        <i class="mdi mdi-account-multiple-outline menu-icon"></i>
        <span class="menu-title">Players</span>
      </a>
    </li>
    
    <li class="nav-item">
      <a class="nav-link" href="<?php echo $settings['site_url']; ?>admin/agents">
        <i class="mdi mdi-account-switch menu-icon"></i>
        <span class="menu-title">Scouts/Agents</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="<?php echo $settings['site_url']; ?>admin/users">
        <i class="mdi mdi-account-multiple menu-icon"></i>
        <span class="menu-title">Users</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="<?php echo $settings['site_url']; ?>admin/admins">
        <i class="mdi mdi-account-star menu-icon"></i>
        <span class="menu-title">Admins</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="<?php echo $settings['site_url']; ?>admin/verifications">
        <i class="mdi mdi-account-check menu-icon"></i>
        <span class="menu-title">Verification</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="<?php echo $settings['site_url']; ?>admin/messages">
        <i class="mdi mdi-message-text-outline menu-icon"></i>
        <span class="menu-title">Messages</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="<?php echo $settings['site_url']; ?>admin/analytics">
        <i class="mdi mdi-chart-bar menu-icon"></i>
        <span class="menu-title">Analytics</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="<?php echo $settings['site_url']; ?>admin/videos">
        <i class="mdi mdi-cloud-upload menu-icon"></i>
        <span class="menu-title">Video Reviews</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="<?php echo $settings['site_url']; ?>admin/processing">
        <i class="mdi mdi-memory menu-icon"></i>
        <span class="menu-title">AI Processing</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="<?php echo $settings['site_url']; ?>admin/plans">
        <i class="mdi mdi-credit-card menu-icon"></i>
        <span class="menu-title">Plans</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="<?php echo $settings['site_url']; ?>admin/subscriptions">
        <i class="mdi mdi-credit-card-multiple menu-icon"></i>
        <span class="menu-title">Subscriptions</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="<?php echo $settings['site_url']; ?>admin/transactions">
        <i class="mdi mdi-currency-usd menu-icon"></i>
        <span class="menu-title">Transactions</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="<?php echo $settings['site_url']; ?>admin/negotiations">
        <i class="mdi mdi-animation menu-icon"></i>
        <span class="menu-title">Negotiations</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="<?php echo $settings['site_url']; ?>admin/#profile" data-toggle="modal">
        <i class="mdi mdi-account menu-icon"></i>
        <span class="menu-title">Profile</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" data-toggle="collapse" href="#form-elements" aria-expanded="false" aria-controls="form-elements">
        <i class="mdi mdi-settings menu-icon"></i>
        <span class="menu-title">Settings</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="form-elements">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"><a class="nav-link" href="<?php echo $settings['site_url']; ?>admin/gen-settings">General Settings</a></li>
        </ul>
      </div>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="<?php echo $settings['site_url']; ?>admin/logout">
        <i class="mdi mdi-logout menu-icon"></i>
        <span class="menu-title">Logout</span>
      </a>
    </li>
  </ul>
</nav>
