<!-- Add -->
<div class="modal fade" id="addnew1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><b>Add New Admin</b></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <form class="form-horizontal" method="POST" action="admin_add.php" enctype="multipart/form-data">
            <div class="form-group">
              <label for="username" class="col-sm-3 control-label">Username</label>
              <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
              <label for="email" class="col-sm-3 control-label">Email</label>
              <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
              <label for="password" class="col-sm-3 control-label">Password</label>
              <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="form-group">
              <label for="firstname" class="col-sm-3 control-label">Firstname</label>
              <input type="text" class="form-control" id="firstname" name="firstname" required>
            </div>
            <div class="form-group">
              <label for="lastname" class="col-sm-3 control-label">Lastname</label>
              <input type="text" class="form-control" id="lastname" name="lastname" required>
            </div>
            <div class="form-group">
              <label for="address" class="col-sm-3 control-label">Address</label>
              <textarea class="form-control" id="address" name="address"></textarea>
            </div>
            <div class="form-group">
              <label for="contact" class="col-sm-3 control-label">Contact Info</label>
              <input type="text" class="form-control" id="contact" name="contact">
            </div>

              <input type="hidden" class="form-control" id="type" name="type" value="1" readonly>

            <div class="form-group">
              <label for="photo" class="col-sm-3 control-label">Photo</label>
              <input type="file" id="photo" name="photo">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default btn-rounded pull-left" data-dismiss="modal"><i class="mdi mdi-window-close"></i> Close</button>
            <button type="submit" class="btn btn-primary btn-rounded" name="add"><i class="mdi mdi-check"></i> Save</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit -->
  <div class="modal fade" id="edit1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
        <h4 class="modal-title"><b>Edit Admin</b></h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          </div>
          <div class="modal-body">
            <form class="form-horizontal" method="POST" action="admin_edit.php">
              <input type="hidden" class="userid" name="id">
              <div class="form-group">
                <label for="edit_username" class="col-sm-3 control-label">Username</label>
                <input type="text" class="form-control" id="edit_username" name="username">
              </div>
              <div class="form-group">
                <label for="edit_email" class="col-sm-3 control-label">Email</label>
                <input type="email" class="form-control" id="edit_email" name="email">
              </div>
              <div class="form-group">
                <label for="edit_password" class="col-sm-3 control-label">Password</label>
                <input type="password" class="form-control" id="edit_password" name="password">
              </div>
              <div class="form-group">
                <label for="edit_firstname" class="col-sm-3 control-label">Firstname</label>
                <input type="text" class="form-control" id="edit_firstname" name="firstname">
              </div>
              <div class="form-group">
                <label for="edit_lastname" class="col-sm-3 control-label">Lastname</label>
                <input type="text" class="form-control" id="edit_lastname" name="lastname">
              </div>
              <div class="form-group">
                <label for="edit_address" class="col-sm-3 control-label">Address</label>
                <textarea class="form-control" id="edit_address" name="address"></textarea>
              </div>
              <div class="form-group">
                <label for="edit_contact" class="col-sm-3 control-label">Contact Info</label>
                <input type="text" class="form-control" id="edit_contact" name="contact">
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default btn-rounded pull-left" data-dismiss="modal"><i class="mdi mdi-window-close"></i> Close</button>
              <button type="submit" class="btn btn-success btn-rounded" name="edit"><i class="mdi mdi-sync"></i> Update</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete -->
    <div class="modal fade" id="delete1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
          <h4 class="modal-title"><b>Deleting...</b></h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
              <form class="form-horizontal" method="POST" action="admin_delete.php">
                <input type="hidden" class="userid" name="id">
                <div class="text-center">
                  <p>DELETE ADMIN</p>
                  <h2 class="bold fullname"></h2>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default btn-rounded pull-left" data-dismiss="modal"><i class="mdi mdi-window-close"></i> Close</button>
                <button type="submit" class="btn btn-danger btn-rounded" name="delete"><i class="mdi mdi-delete"></i> Delete</button>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Delete -->
      <div class="modal fade" id="delete2">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
            <h4 class="modal-title"><b>Deleting...</b></h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
              </div>
              <div class="modal-body">
                <form class="form-horizontal" method="POST" action="newsletter_delete.php">
                  <input type="hidden" class="userid" name="id">
                  <div class="text-center">
                    <p>DELETE SUBSCRIBER</p>
                    <h2 class="bold fullname"></h2>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default btn-rounded pull-left" data-dismiss="modal"><i class="mdi mdi-window-close"></i> Close</button>
                  <button type="submit" class="btn btn-danger btn-rounded" name="delete"><i class="mdi mdi-delete"></i> Delete</button>
                </form>
              </div>
            </div>
          </div>
        </div>

      <!-- Update Photo -->
      <div class="modal fade" id="edit_photo1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
            <h4 class="modal-title"><b><span class="fullname"></span></b></h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
              </div>
              <div class="modal-body">
                <form class="form-horizontal" method="POST" action="admin_photo.php" enctype="multipart/form-data">
                  <input type="hidden" class="userid" name="id">
                  <div class="form-group">
                    <label for="photo" class="col-sm-3 control-label">Photo</label>

                    <input type="file" id="photo" name="photo" required>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default btn-rounded pull-left" data-dismiss="modal"><i class="mdi mdi-window-close"></i> Close</button>
                  <button type="submit" class="btn btn-success btn-rounded" name="upload"><i class="fa fa-check-square-o"></i> Update</button>
                </form>
              </div>
            </div>
          </div>
        </div>


        <!-- Activate -->
        <div class="modal fade" id="activate1">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
              <h4 class="modal-title"><b>Activating...</b></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                  <form class="form-horizontal" method="POST" action="admin_activate.php">
                    <input type="hidden" class="userid" name="id">
                    <div class="text-center">
                      <p>ACTIVATE ADMIN</p>
                      <h2 class="bold fullname"></h2>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-rounded pull-left" data-dismiss="modal"><i class="mdi mdi-window-close"></i> Close</button>
                    <button type="submit" class="btn btn-success btn-rounded" name="activate"><i class="fa fa-check"></i> Activate</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
