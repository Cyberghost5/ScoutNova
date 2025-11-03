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
                  <h3 class="font-weight-bold">Transactions</h3>
                  <h6 class="font-weight-normal mb-0">List of all Transactions.</h6>
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
                  <h4 class="card-title">Subscription Transactions</h4>
                  <p class="card-description">
                    List of all <span class="text-primary">Subscription</span> Transactions
                  </p>
                  <div class="table-responsive" id="airtime">
                    <table class="table table-striped" id="example1">
                      <thead>
                        <tr>
                          <th>Trx. ID</th>
                          <th>Amount</th>
                          <th>Receipt</th>
                          <th>Payment Method</th>
                          <th>Date & Time</th>
                          <th>Status</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $conn = $pdo->open();

                        try{
                          $stmt = $conn->prepare("SELECT * FROM transactions ORDER BY id DESC");
                          $stmt->execute();
                          $i = 0;
                          foreach($stmt as $row){
                            if ($row['status'] == 0) {
                              $status = '<div class="badge badge-warning">Pending</div>';
                              // echo '<div class="badge badge-warning">Pending</div>';
                            }
                            if ($row['status'] == 1) {
                              $status = '<div class="badge badge-success">Successfull</div>';
                              // echo '<div class="badge badge-success">Successfull</div>';
                            }
                            if ($row['status'] == 2) {
                              $status = '<div class="badge badge-danger">Rejected</div>';
                              // echo '<div class="badge badge-danger">Rejected</div>';
                            }
                            $i++;
                            echo "
                            <tr>
                              <td>".$row['transaction_id']."</td>
                              <td class='font-weight-bold'>".$row['currency']." ".$row['amount']."</td>
                              <td><a href='transaction?order_id=".$row['transaction_id']."' class='btn btn-success btn-sm btn-flat' target='_blank'><i class='mdi mdi-eye'></i> View</a></td>
                              <td>".ucfirst($row['payment_method'])."</td>
                              <td>".date('M d, Y - h:i a', strtotime($row['created_at']))."</td>
                              <td>
                                ".$status."
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
        <!-- partial -->
      </div>
      <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->

  <?php include 'includes/scripts.php'; ?>
  <script>
    $(function () {
      $('#example1').DataTable({
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
