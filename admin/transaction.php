<?php include 'includes/head.php'; ?>
<?php include 'js/style.php'; ?>

<body style="background:#fff;;min-width:auto;" class="no-touch">


  <div class="container mt-5" id="receipt">
    <header class="w-100 d-flex justify-content-center text-center mt-5 align-items-center pt-2 pb-2" >
      <center>
        <img src="<?php echo $settings['site_url']; ?>assets/images/<?php echo $settings['favicon']; ?>" srcset="<?php echo $settings['site_url']; ?>assets/images/<?php echo $settings['favicon']; ?> 2x" width="150px" height="auto" class="img-fluid mb-1" alt="logo">
      </center>
      <!-- <legend class="h1 font-weight-bold"><?php echo $settings['site_name']; ?> Electricity Bill Receipt</legend> -->
    </header>
    <h6 class="card-sub-title">Your Transaction Details are as Follows:</h6>

    <?php
    $order_id = $_GET['order_id'];
    $order_id = preg_replace('/\.php$/', '', $order_id);

    $conn = $pdo->open();

    try{
      $stmt = $conn->prepare("SELECT * FROM transactions WHERE transaction_id=:order_id");
      $stmt->execute(['order_id'=>$order_id]);
      $i = 0;
      foreach($stmt as $row){
        $stmt = $conn->prepare("SELECT * FROM users WHERE id=:userid");
        $stmt->execute(['userid'=>$row['user_id']]);
        $iow = $stmt->fetch();
        if ($row['status'] == 0) {
          $status = '<div class="badge badge-danger">Unsuccessful</div>';
          $status1 = "<div class='alert alert-danger alert-center alert-dismissible fade show'>
            Your transaction of <b>".$row['currency']." ".$row['amount']."</b> using ".$row['payment_method']." was $status.
          </div>";
          // echo '<div class="badge badge-warning">Pending</div>';
        }
        if ($row['status'] == 1) {
          $status = '<div class="badge badge-success">Successfull</div>';
          $status1 = "<div class='alert alert-success alert-center alert-dismissible fade show'>
            Your transaction of <b>".$row['currency']." ".$row['amount']."</b> using ".$row['payment_method']." was $status.
          </div>";
          // echo '<div class="badge badge-success">Successfull</div>';
        }
        // $status = ($row['status']) ? '<div class="badge badge-success">Successfull</div>' : '<div class="badge badge-warning">Pending</div>';
        $i++;
        echo "

        $status1

        <ul class='data-details-list'>
          <li>
            <div class='data-details-head'>Username - ID</div>
            <div class='data-details-des'><b><a href='../user/".$iow['uuid']."'>".$iow['username']." - ".$iow['uuid']."</a></b></div>
          </li>

          <li>
            <div class='data-details-head'>Amount</div>
            <div class='data-details-des'><b>".$row['currency']." ".$row['amount']."</b></div>
          </li>

          <li>
            <div class='data-details-head'>Payment Method</div>
            <div class='data-details-des'><b>".ucfirst($row['payment_method'])."</b></div>
          </li>
          <li>
            <div class='data-details-head'>Transaction ID</div>
            <div class='data-details-des'><b>".$row['transaction_id']."</b></div>
          </li>
          <li>
            <div class='data-details-head'>Status</div>
            <div class='data-details-des'><b>".$status."</b></div>
          </li>
          <li>
            <div class='data-details-head'>Date</div>
            <div class='data-details-des'><b>".date('Y M, d - h:i a', strtotime($row['created_at']))."</b></div>
          </li>


        </ul>
    ";
  }
}
catch(PDOException $e){
  echo $e->getMessage();
}

$pdo->close();
?>
<p class="text-center mt-4">&copy; All rights reserved | <?php echo $settings['site_name']; ?></p>

  </div>

  <center>
    <div style="margin-bottom:20px; padding-top: 15px;">
      <button class="btn btn-info" onclick="printContent('receipt');">Print</button> <button class="btn btn-info" id="btnPrint">Save</button>
    </div>
  </center>

  <script>
  function printContent(el){
    var restorepage = $('body').html();
    var printcontent = $('#' + el).clone();
    $('body').empty().html(printcontent);
    window.print();
    $('body').html(restorepage);
  }
  </script>

  <?php include 'includes/scripts.php'; ?>

  <script type="text/javascript" src="js/pdfmake.min.js"></script>
  <script type="text/javascript" src="js/html2canvas.min.js"></script>

  <script type="text/javascript">
  document.getElementById('btnPrint').addEventListener('click',
  Export);

  function Export() {
    html2canvas(document.getElementById('receipt'), {
      onrendered: function (canvas) {
        var data = canvas.toDataURL();
        var docDefinition = {
          content: [{
            image: data,
            width: 500
          }]
        };
        pdfMake.createPdf(docDefinition).download("<?php echo $settings['site_name']; ?>_airtime_receipt.pdf");
      }
    });
  }  </script>


</body>
</html>
