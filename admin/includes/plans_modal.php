<!-- Add -->
<div class="modal fade" id="addnew1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><b>Add New Plan</b></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <form class="form-horizontal" method="POST" action="plans_add.php" enctype="multipart/form-data">
            <div class="form-group">
              <label for="name" class="col-sm-3 control-label">Plan Name</label>
              <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
              <label for="plan_id" class="col-sm-3 control-label">Plan ID</label>
              <input type="text" class="form-control" id="plan_id" name="plan_id">
            </div>
            <div class="form-group">
              <label for="currency" class="col-sm-3 control-label">Currency</label>
              <input type="text" class="form-control" id="currency" name="currency" required>
            </div>
            <div class="form-group">
              <label for="amount" class="col-sm-3 control-label">Amount</label>
              <input type="number" step="none" class="form-control" id="amount" name="amount" required>
            </div>
            <div class="form-group">
              <label for="intervals" class="col-sm-3 control-label">Intervals</label>
              <select name="intervals" id="intervals" class="form-control">
                <option value="">-- Select --</option>
                <option value="Hourly">Hourly</option>
                <option value="Daily">Daily</option>
                <option value="Weekly">Weekly</option>
                <option value="Monthly">Monthly</option>
                <option value="Quarterly">Quarterly</option>
                <option value="Yearly">Yearly</option>
              </select>
            </div>
            <div class="form-group">
              <label for="details" class="col-sm-3 control-label">Plan Details</label>
              <textarea class="form-control" id="details" name="details"></textarea>
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
        <h4 class="modal-title"><b>Edit Plan</b></h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          </div>
          <div class="modal-body">
            <form class="form-horizontal" method="POST" action="plans_edit.php">
              <input type="hidden" class="userid" name="id">
              <div class="form-group">
                <label for="edit_name" class="col-sm-3 control-label">Plan Name</label>
                <input type="text" class="form-control" id="edit_name" name="name" required>
              </div>
              <div class="form-group">
                <label for="edit_plan_id" class="col-sm-3 control-label">Plan ID</label>
                <input type="text" class="form-control" id="edit_plan_id" name="plan_id">
              </div>
              <div class="form-group">
                <label for="edit_currency" class="col-sm-3 control-label">Currency</label>
                <input type="text" class="form-control" id="edit_currency" name="currency" required>
              </div>
              <div class="form-group">
                <label for="edit_amount" class="col-sm-3 control-label">Amount</label>
                <input type="number" step="none" class="form-control" id="edit_amount" name="amount" required>
              </div>
              <div class="form-group">
                <label for="edit_intervals" class="col-sm-3 control-label">Intervals</label>
                <select name="intervals" id="edit_intervals" class="form-control">
                  <option value="">-- Select --</option>
                  <option value="Hourly">Hourly</option>
                  <option value="Daily">Daily</option>
                  <option value="Weekly">Weekly</option>
                  <option value="Monthly">Monthly</option>
                  <option value="Quarterly">Quarterly</option>
                  <option value="Yearly">Yearly</option>
                </select>
              </div>
              <div class="form-group">
                <label for="edit_details" class="col-sm-3 control-label">Plan Details</label>
                <textarea class="form-control" id="edit_details" name="details"></textarea>
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
              <form class="form-horizontal" method="POST" action="plans_delete.php">
                <input type="hidden" id="edit_plan_id2" name="id">
                <div class="text-center">
                  <p>DELETE PLAN</p>
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
                    <p>DELETE PLAN</p>
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
                  <form class="form-horizontal" method="POST" action="plans_activate.php">
                    <input type="hidden" class="userid" name="id">
                    <div class="text-center">
                      <p>ACTIVATE PLAN</p>
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
<!-- Activate -->
        <div class="modal fade" id="details1">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title"><b>Details...</b></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
                <div class="modal-body">
                  <div class="text-center">
                    <p class="fulldetails"></p>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default btn-rounded pull-left" data-dismiss="modal"><i class="mdi mdi-window-close"></i> Close</button>
                </div>
              </div>
            </div>
          </div>
