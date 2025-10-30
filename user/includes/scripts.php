<!-- plugins:js -->
<script src="<?php echo $settings['site_url']; ?>vendors/js/vendor.bundle.base.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- endinject -->
  <!-- plugin js for this page -->
  <script src="<?php echo $settings['site_url']; ?>vendors/tinymce/tinymce.min.js"></script>
  <script src="<?php echo $settings['site_url']; ?>vendors/quill/quill.min.js"></script>
  <script src="<?php echo $settings['site_url']; ?>vendors/simplemde/simplemde.min.js"></script>
  <!-- End plugin js for this page -->
<!-- endinject -->
<!-- Plugin js for this page -->
<script src="<?php echo $settings['site_url']; ?>vendors/chart.js/Chart.min.js"></script>
<script src="<?php echo $settings['site_url']; ?>vendors/datatables.net/jquery.dataTables.js"></script>
<script src="<?php echo $settings['site_url']; ?>vendors/datatables.net-bs4/dataTables.bootstrap4.js"></script>
<script src="<?php echo $settings['site_url']; ?>js/dataTables.select.min.js"></script>

<!-- End plugin js for this page -->
<!-- inject:js -->
<script src="<?php echo $settings['site_url']; ?>js/off-canvas.js"></script>
<script src="<?php echo $settings['site_url']; ?>js/hoverable-collapse.js"></script>
<script src="<?php echo $settings['site_url']; ?>js/template.js"></script>
<script src="<?php echo $settings['site_url']; ?>js/settings.js"></script>
<script src="<?php echo $settings['site_url']; ?>js/todolist.js"></script>
<script src="<?php echo $settings['site_url']; ?>js/tooltips.js"></script>
<script src="<?php echo $settings['site_url']; ?>js/codeEditor.js"></script>
<script src="<?php echo $settings['site_url']; ?>js/tabs.js"></script>
<!-- endinject -->
<!-- Custom js for this page-->
<script src="<?php echo $settings['site_url']; ?>js/dashboard.js"></script>
<script src="<?php echo $settings['site_url']; ?>js/Chart.roundedBarCharts.js"></script>
<script src="<?php echo $settings['site_url']; ?>js/editorDemo.js"></script>
<!-- End custom js for this page-->

<!-- endinject -->
<!-- Plugin js for this page -->
<script src="<?php echo $settings['site_url']; ?>vendors/typeahead.js/typeahead.bundle.min.js"></script>
<script src="<?php echo $settings['site_url']; ?>vendors/select2/select2.min.js"></script>
<script src="<?php echo $settings['site_url']; ?>vendors/bootstrap-tagsinput/bootstrap-tagsinput.min.js"></script>
<script src="<?php echo $settings['site_url']; ?>vendors/codemirror/codemirror.js"></script>
<script src="<?php echo $settings['site_url']; ?>vendors/codemirror/javascript.js"></script>
<script src="<?php echo $settings['site_url']; ?>vendors/codemirror/shell.js"></script>
<script src="<?php echo $settings['site_url']; ?>vendors/pwstabs/jquery.pwstabs.min.js"></script>
<!-- End plugin js for this page -->
<!-- inject:js -->

<!-- Custom js for this page-->
<script src="<?php echo $settings['site_url']; ?>js/file-upload.js"></script>
<script src="<?php echo $settings['site_url']; ?>js/typeahead.js"></script>
<script src="<?php echo $settings['site_url']; ?>js/select2.js"></script>
<!-- End custom js for this page-->

<!-- Bootstrap Modal -->
<div class="modal fade" id="uploadingModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center p-4">
      <h5>Uploading...</h5>
      <p>Your video is being processed.</p>
      <div class="spinner-border text-primary" role="status"></div>
    </div>
  </div>
</div>

<script>
document.querySelector('form').addEventListener('submit', function() {
  var modal = new bootstrap.Modal(document.getElementById('uploadingModal'));
  modal.show();
});
</script>