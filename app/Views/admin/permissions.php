<?= $this->extend("layouts/user_account_layout"); ?>

<?= $this->section("sidebar_menu"); ?>
    <?= $this->include('test_menu') ?>
<?= $this->endSection(); ?>

<?= $this->section("page-title"); ?>
    Permissions
<?= $this->endSection(); ?>

<?= $this->section("breadcrumb"); ?>
    <li class="breadcrumb-item"><a href="#"><i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li class="breadcrumb-item active">Permissions</li>
<?= $this->endSection(); ?>

<?= $this->section("content"); ?>
    <div class="row">
        <div class="col-12">
            <!------------------- ALERT ERROR MESSAGE ---------------------------->
            <?php if(session()->get('err_messages')):
                $message = session()->get('err_messages');
                session()->remove('err_messages');
                ?>
                <?php foreach($message as $row) { ?>
                <tr>
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        <i class="fas fa-exclamation-triangle me-1" style="color: #800000;"></i> <?php echo $row; ?>
                    </div>
                </tr>
            <?php } ?>
            <?php endif; ?>
            <!---------------------------------------------------------------------->
            <div class="card">
                <div class="card-header d-flex align-items-center" style="color:#800000;">
                    <h5 class="font-weight-bold m-0">
                        Permissions
                    </h5>
                    <a href="#" data-toggle="modal" data-target="#add-permission" class="ml-auto btn btn-primary btn-sm d-inline float-right"><i class="fas fa-plus"></i> Add Permission</a>
                </div>

                <div class="card-body">
                    <table id="permissionsTable" class="table dt-responsive display compact nowrap" style="width:100%">
                        <thead>
                        <tr>
                            <th>No.</th>
                            <th>Permission</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td></td>
                            <td></td>
                            <td>
                                <a href="#" data-toggle="modal" data-target="#edit-permission" class="btn btn-secondary btn-sm" title="Edit"><i class="fas fa-pencil-alt"></i></a>
                                <a class="btn btn-danger btn-sm" data-toggle="modal" data-target="#delete-permission" title="Delete"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!------ MODAL FOR ADDING PERMISSION ------>
    <div class="modal" id="add-permission" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-color">
                    <h5 class="modal-title">Add Permission</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="#">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="form-group required">
                                <label for="role">Permission</label>
                                <input type="text" class="form-control" id="permission" name="permission" autocomplete="off" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!------ MODAL FOR EDITING PERMISSION ------>
    <div class="modal" id="edit-permission" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-color">
                    <h5 class="modal-title">Edit Permission</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="#">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="form-group required">
                                <label for="role">Permission</label>
                                <input type="text" class="form-control" id="permission" name="permission" value="" autocomplete="off" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <!------ MODAL FOR CONFIRMING DELETION OF ROLE ------>
    <div class="modal fade" id="delete-permission">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body text-center py-5">
                    <i class="fas fa-exclamation-circle mb-4" style="font-size: 100px; color: #DC3545;"></i> <br>
                    <h4 class="mb-4">Are you sure you want to delete this permission?</h4>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <a type="button" class="btn btn-danger btn-ok">Yes, delete it.</a>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection(); ?>

<?= $this->section("script"); ?>
    <script>
        $(document).ready(function() {
            $('#permissionsTable').DataTable();
        } );
    </script>
<?= $this->endSection(); ?>



