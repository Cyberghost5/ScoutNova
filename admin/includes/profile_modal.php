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
            	<form class="form-horizontal" method="POST" action="<?php echo $settings['site_url']; ?>admin/profile_update?return=<?php echo basename($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">
                <center>
                <img src="<?php echo $settings['site_url']; ?>admin/<?php echo (!empty($admin['photo'])) ? 'images/'.$admin['photo'] : 'images/profile.jpg'; ?>" class="img-circle elevation-2" height="120" width="120" alt="User Image">
                </center>
                <div class="form-group">
                  	<label for="email" class="control-label">Email</label>

                  	<div class="col-sm-12">
                    	<input type="text" class="form-control" id="email" value="<?php echo $admin['email']; ?>" readonly>
                  	</div>
                </div>
                <div class="form-group">
                    <label for="password" class="control-label">Password</label>

                    <div class="col-sm-12">
                      <input type="password" class="form-control" id="password" name="password" value="<?php echo $admin['password']; ?>">
                    </div>
                </div>
                <div class="form-group">
                  	<label for="firstname" class="control-label">Firstname</label>

                  	<div class="col-sm-12">
                    	<input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo $admin['firstname']; ?>">
                  	</div>
                </div>
                <div class="form-group">
                  	<label for="lastname" class="control-label">Lastname</label>

                  	<div class="col-sm-12">
                    	<input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo $admin['lastname']; ?>">
                  	</div>
                </div>
                <div class="form-group">
                  	<label for="contact_info" class="control-label">Phone Number</label>

                  	<div class="col-sm-12">
                    	<input type="tel" class="form-control" id="contact_info" name="contact_info" value="<?php echo $admin['contact_info']; ?>">
                  	</div>
                </div>
                <div class="form-group">
                    <label for="photo" class="control-label">Photo:</label>

                    <div class="col-sm-12">
                      <input type="file" id="photo" name="photo">
                    </div>
                </div>
                <hr>
                <div class="form-group">
                    <label for="curr_password" class="control-label">Current Password:</label>

                    <div class="col-sm-12">
                      <input type="password" class="form-control" id="curr_password" name="curr_password" placeholder="Input current password to save changes" required>
                    </div>
                </div>
          	</div>
          	<div class="modal-footer justify-content-between">
            	<button type="button" class="btn btn-danger btn-rounded btn-flat" data-dismiss="modal"><i class="mdi mdi-window-close"></i> Close</button>
            	<button type="submit" class="btn btn-success btn-rounded btn-flat" name="save"><i class="mdi mdi-check"></i> Save</button>
            	</form>
          	</div>
        </div>
    </div>
</div>
