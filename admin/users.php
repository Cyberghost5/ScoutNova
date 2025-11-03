<?php include 'includes/head.php'; ?>
<body class="sidebar-dark">
  <div class="container-scroller">
    <!-- partial:partials/_navbar.html -->
    <?php include 'includes/navbar.php'; ?>
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
      <!-- partial:partials/_settings-panel.html -->
      <?php include 'includes/settings.php'; ?>
      <!-- partial -->
      <!-- partial:partials/_sidebar.html -->
      <?php include 'includes/sidebar.php'; ?>
      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-md-12 grid-margin">
              <div class="row">
                <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                  <h3 class="font-weight-bold">Users</h3>
                  <h6 class="font-weight-normal mb-0">Check out all users on <?php echo $settings['site_name']; ?>.</h6>
                </div>
              </div>
            </div>
            <div class="col-md-12">
              <?php
                if(isset($_SESSION['error'])){
                  echo "
                    <div class='alert alert-danger alert-dismissible'>
                      <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                      <h4><i class='icon mdi mdi-close'></i>Error!</h4>
                      ".$_SESSION['error']."
                    </div>
                  ";
                  unset($_SESSION['error']);
                }
                if(isset($_SESSION['success'])){
                  echo "
                    <div class='alert alert-success alert-dismissible'>
                      <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                      <h4><i class='icon mdi mdi-check'></i> Success!</h4>
                      ".$_SESSION['success']."
                    </div>
                  ";
                  unset($_SESSION['success']);
                }
              ?>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Users</h4>
                  <p class="card-description">
                    All Users on <?php echo $settings['site_name']; ?>
                  </p>
                  <div class="table-responsive">
                    <a href="#addnew1" data-toggle="modal" class="btn btn-rounded btn-primary btn-sm mb-3"><i class="mdi mdi-account-plus"></i> New</a>
                    <table class="table table-striped" id="example2">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Image</th>
                          <th>Username</th>
                          <th>Fullname</th>
                          <th>Type</th>
                          <th>Email</th>
                          <th>Date Joined</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $conn = $pdo->open();

                        try{
                          $stmt = $conn->prepare("SELECT * FROM users WHERE type = 0 ORDER BY id DESC");
                          $stmt->execute();
                          $i = 0;
                          foreach($stmt as $row){
                            if ($row['role'] == 'user') {
                              $type = '<div class="badge badge-primary">Player</div>';
                              $link = 'player/'.$row['uuid'];
                            }
                            if ($row['role'] == 'agent') {
                              $type = '<div class="badge badge-info">Agent</div>';
                              $link = 'agent/'.$row['uuid'];
                            }
                            $i++;
                            $image = (!empty($row['photo'])) ? '/user/images/'.$row['photo'] : '/user/images/profile.jpg';
                            $status = ($row['status']) ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Not Verified</span>';
                            $active = (!$row['status']) ? '<span class="pull-right"><a href="#activate1" class="status" data-toggle="modal" data-id="'.$row['id'].'"><i class="fa fa-check"></i></a></span>' : '';
                            echo "
                            <tr data-widget='expandable-table' aria-expanded='false'>
                              <td>".$i."</td>
                              <td class='py-1'><img src='".$settings['site_url']."".$image."' class='img-thumbnail m-2' height='100px' width='100px' style='border-radius:50%;'></td>
                              <td>".$row['username']."</td>
                              <td>".$row['firstname']." ".$row['lastname']."</td>
                              <td>".$type." <a href=".$link."><i class='mdi mdi-eye'></i></a> </td>
                              <td>".$row['email']."</td>
                              <td>".date('M d, Y', strtotime($row['date_created']))."</td>
                              <td>
                                <a class='btn btn-info btn-sm btn-rounded' href='user/".$row['uuid']."'><i class='mdi mdi-eye'></i> View</a>
                                <button class='btn btn-success btn-sm edit btn-rounded' data-id='".$row['id']."'><i class='mdi mdi-pencil'></i> Edit</button>
                                <button class='btn btn-danger btn-sm delete btn-rounded' data-id='".$row['id']."'><i class='mdi mdi-delete'></i> Delete</button>
                              </td>
                            </tr>
                        ";
                            }
                          }
                          catch(PDOException $e){
                            echo $e->getMessage();
                          }

                          $pdo->close();
                        ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
        <!-- content-wrapper ends -->
        <!-- partial:partials/_footer.html -->
        <?php include 'includes/footer.php'; ?>
        <?php include 'includes/users_modal.php'; ?>
        <!-- partial -->
      </div>
      <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->

  <?php include 'includes/scripts.php'; ?>
  <script>
  $(function(){

    $(document).on('click', '.edit', function(e){
      e.preventDefault();
      $('#edit1').modal('show');
      var id = $(this).data('id');
      getRow(id);
    });

    $(document).on('click', '.delete', function(e){
      e.preventDefault();
      $('#delete1').modal('show');
      var id = $(this).data('id');
      getRow(id);
    });

    $(document).on('click', '.photo', function(e){
      e.preventDefault();
      var id = $(this).data('id');
      getRow(id);
    });

    $(document).on('click', '.status', function(e){
      e.preventDefault();
      var id = $(this).data('id');
      getRow(id);
    });

  });

  function getRow(id){
    $.ajax({
      type: 'POST',
      url: 'admin_row.php',
      data: {id:id},
      dataType: 'json',
      success: function(response){
        $('.userid').val(response.id);
        $('#edit_email').val(response.email);
        $('#edit_password').val(response.password);
        $('#edit_firstname').val(response.firstname);
        $('#edit_lastname').val(response.lastname);
        $('#edit_username').val(response.username);
        $('#edit_role').val(response.role);
        $('#edit_address').val(response.address);
        $('#edit_contact').val(response.contact_info);
        $('.fullname').html(response.firstname+' '+response.lastname);
      }
    });
  }
  </script>
  <script>
    $(function () {
      $('#example2').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": false,
        "info": true,
        "autoWidth": false,
        "responsive": true,
      });
    });
  </script>
</body>

</html>
