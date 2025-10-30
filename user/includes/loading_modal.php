  <!--<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">-->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>



<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" aria-labelledby="loadingModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="loadingModalLabel">Purchasing...</h5>
      </div>
      <div class="modal-body text-center text-primary">
        <p>Please Wait</p>
        <div class="spinner-border" role="status">
          <span class="visually-hidden"></span>
        </div>
      </div>
      <!-- Optional: Add a message or additional content here -->
      <!-- Remove the close button to make it not dismissible -->
    </div>
  </div>
</div>

<script>
document.getElementById("form_airtime").addEventListener("submit", function() {
    //document.getElementById("buy_button").disabled = true;
    $('#loadingModal').modal('show');
    $('#buy_data').modal('hide');
});
</script>
