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
                  <h3 class="font-weight-bold">Plans</h3>
                  <h6 class="font-weight-normal mb-0">Check out all plans on <?php echo $settings['site_name']; ?>.</h6>
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
                  <h4 class="card-title">Plans</h4>
                  <p class="card-description">
                    All plans on <?php echo $settings['site_name']; ?>
                  </p>
                  <div class="table-responsive">
                    <a href="#addnew1" data-toggle="modal" class="btn btn-rounded btn-primary btn-sm mb-3"><i class="mdi mdi-account-plus"></i> New</a>
                    <table class="table table-striped" id="example2">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Name</th>
                          <th>Plan ID</th>
                          <th>Amount</th>
                          <th>Details</th>
                          <th>Interval</th>
                          <th>Date Created</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $conn = $pdo->open();

                        try{
                          $stmt = $conn->prepare("SELECT * FROM plans ORDER BY id DESC");
                          $stmt->execute();
                          $i = 0;
                          foreach($stmt as $row){
                            $i++;
                            echo "
                            <tr data-widget='expandable-table' aria-expanded='false'>
                              <td>".$i."</td>
                              <td>".$row['name']."</td>
                              <td>".$row['plan_id']."</td>
                              <td>".$row['currency']." ".$row['amount']."</td>
                              <td>".$row['details']."</td>
                              <td>".$row['intervals']."</td>
                              <td>".date('M d, Y', strtotime($row['created_at']))."</td>
                              <td>
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
        <?php include 'includes/plans_modal.php'; ?>
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

    $(document).on('click', '.status', function(e){
      e.preventDefault();
      var id = $(this).data('id');
      getRow(id);
    });

  });

  function getRow(id){
    $.ajax({
      type: 'POST',
      url: 'plans_row.php',
      data: {id:id},
      dataType: 'json',
      success: function(response){
        $('.userid').val(response.id);
        $('#edit_plan_id').val(response.plan_id);
        $('#edit_plan_id2').val(response.plan_id);
        $('#edit_name').val(response.name);
        $('#edit_amount').val(response.amount);
        $('#edit_currency').val(response.currency);
        $('#edit_intervals').val(response.intervals);
        $('#edit_details').val(response.details);
        $('#edit_status').val(response.status);
        $('.fullname').html(response.name+' - '+response.intervals);
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
