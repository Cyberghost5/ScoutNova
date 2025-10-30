<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
  <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
    <a class="navbar-brand brand-logo mr-5" href="home"><img src="<?php echo $settings['site_url']; ?>assets/images/<?php echo $settings['link_logo'];?>" class="mr-2" alt="logo"/></a>
    <a class="navbar-brand brand-logo-mini" href="home"><img src="<?php echo $settings['site_url']; ?>assets/images/<?php echo $settings['logo'];?>" alt="logo"/></a>
  </div>
  <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
    <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
      <span class="icon-menu"></span>
    </button>
    <?php if($user['role'] == 'agent'): ?>
    <ul class="navbar-nav mr-lg-2">
      <li class="nav-item nav-search d-none d-lg-block">
        <form action="search" method="post">
          <div class="input-group">
            <div class="input-group-prepend hover-cursor" id="navbar-search-icon">
              <span class="input-group-text" id="search">
                <i class="icon-search"></i>
              </span>
            </div>
            <input type="text" name="search" class="form-control" id="navbar-search-input" placeholder="Search players by name, position, or country..." aria-label="search" aria-describedby="search" required>
          </div>
        </form>
      </li>
    </ul>
    <?php endif; ?>
    <ul class="navbar-nav navbar-nav-right">
      <li class="nav-item nav-profile dropdown">
        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
          <img src="<?php echo (!empty($user['photo'])) ? 'images/'.$user['photo'] : 'images/profile.jpg'; ?>" alt="profile"/>
        </a>
        <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
          <a class="dropdown-item" href="#profile" data-toggle="modal">
            <i class="ti-user text-danger"></i>
            <?php echo $user['username'];?><i class="mdi mdi-checkbox-marked-circle-outline text-success" style="font-size:10px;"></i>
          </a>
          <a class="dropdown-item" href="settings">
            <i class="ti-settings text-primary"></i>
             Settings
          </a>
          <?php if($user['verification'] == 0 || $user['verification'] == 2 || $user['verification'] == 3): ?>
          <!-- <a class="dropdown-item" href="kyc">
            <i class="mdi mdi-check-decagram"></i>
            <span class="menu-title">KYC Verification</span>
          </a> -->
          <?php endif; ?>
          <a class="dropdown-item" href="logout">
            <i class="ti-power-off text-primary"></i>
            Logout
          </a>
        </div>
      </li>
    </ul>
    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
      <span class="icon-menu"></span>
    </button>
  </div>
</nav>
<?php include 'includes/profile_modal.php'; ?>
