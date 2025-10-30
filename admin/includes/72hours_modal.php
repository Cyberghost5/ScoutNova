<div class="modal fade" id="details">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title"><b>From <span class="fullname"></span></b></h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <b>Full Name:</b> <p id="name"></p>
                <b>Prayer Time:</b> <p id="emailre"></p>
                <b>Prayer Request:</b> <p id="desc"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-rounded pull-left" data-dismiss="modal"><i class="mdi mdi-window-close"></i> Close</button>
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
              <form class="form-horizontal" method="POST" action="72hours_delete">
                <input type="hidden" class="userid" name="id">
                <div class="text-center">
                  <p>DELETE FOR</p>
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

      <div class="modal fade" id="deleteall">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
            <h4 class="modal-title"><b>Deleting...</b></h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
              </div>
              <div class="modal-body">
                <form class="form-horizontal" method="POST" action="72hours_delete_all">
                  <div class="text-center">
                    <p>DELETE ALL REGISTRATIONS</p>
                    <h2 class="bold">Are you sure you want to delete all registrations?</h2>
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

        <div class="modal fade" id="export">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
              <h4 class="modal-title"><b>Exporting...</b></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                  <form class="form-horizontal" method="POST" action="72hours_export">
                    <div class="text-center">
                      <p>EXPORT ALL REGISTRATIONS</p>
                      <h2 class="bold">Export all registrations as a CSV file?</h2>
                    </div>

                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-rounded pull-left" data-dismiss="modal"><i class="mdi mdi-window-close"></i> Close</button>
                    <button type="submit" class="btn btn-info btn-rounded" name="export"><i class="mdi mdi-file-export"></i> Export</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
