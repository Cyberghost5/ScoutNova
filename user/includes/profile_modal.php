<!-- Add -->
<div class="modal fade" id="profile">
    <div class="modal-dialog">
        <div class="modal-content">
          	<div class="modal-header">
              <h4 class="modal-title">User Profile</h4>
            	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
              		<span aria-hidden="true">&times;</span>
              </button>
          	</div>
          	<div class="modal-body">
            	<form class="form-horizontal" method="POST" action="<?php echo $settings['site_url']; ?>admin/profile_update?return=<?php echo basename($_SERVER['PHP_SELF'], ".php"); ?>" enctype="multipart/form-data">
                <center>
                <img src="<?php echo $settings['site_url']; ?>user/<?php echo (!empty($user['photo'])) ? 'images/'.$user['photo'] : 'images/profile.jpg'; ?>" class="img-circle elevation-2" height="120" width="120" alt="User Image">
                </center>
                <div class="form-group">
                  <label for="email" class="control-label">Email</label>
                  <input type="text" class="form-control" id="email" value="<?php echo $user['email']; ?>" readonly>
                </div>
                <div class="form-group">
                  <label for="firstname" class="control-label">Firstname</label>
                  <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo $user['firstname']; ?>" readonly>
                </div>
                <div class="form-group">
                  <label for="lastname" class="control-label">Lastname</label>
                  <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo $user['lastname']; ?>" readonly>
                </div>
                <div class="form-group">
                  <label for="contact_info" class="control-label">Phone Number</label>
                  <input type="tel" class="form-control" id="contact_info" name="contact_info" value="<?php echo $user['contact_info']; ?>">
                </div>
                <!-- <div class="form-group">
                  <label for="photo" class="control-label">Photo:</label>
                  <input type="file" id="photo" name="photo" accept="image/*">
                </div> -->
          	</div>
          	<div class="modal-footer justify-content-between">
            	<button type="button" class="btn btn-danger btn-rounded btn-flat" data-dismiss="modal"><i class="mdi mdi-window-close"></i> Close</button>
            	<button type="submit" class="btn btn-success btn-rounded btn-flat" name="save"><i class="mdi mdi-check"></i> Save</button>
            	</form>
          	</div>
        </div>
    </div>
</div>