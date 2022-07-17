<?= $this->extend("layouts/user_account_layout"); ?>

<?= $this->section("sidebar_menu"); ?>
    <?= $this->include('test_menu') ?>
<?= $this->endSection(); ?>

<?= $this->section("page-title"); ?>
    Roles and Permissions
<?= $this->endSection(); ?>

<?= $this->section("breadcrumb"); ?>
    <li class="breadcrumb-item"><a href="#"><i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li class="breadcrumb-item active">Roles and Permissions</li>
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
                        Roles and Permissions
                    </h5>
                    <?php if($AddRoles) { ?>
                        <a href="#" data-toggle="modal" data-target="#add-role" class="ml-auto btn btn-primary btn-sm d-inline float-right"><i class="fas fa-plus"></i> Add Role</a>
                    <?php } ?>
                </div>

                <div class="card-body">
                    <table id="rolesTable" class="table dt-responsive" style="width:100%">
                        <thead>
                        <tr>
                            <th width="5%">No.</th>
                            <th width="20%">Role</th>
                            <th width="60%">Permissions</th>
                            <th width="15%">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $count = 1;
                                foreach($RolesData as $role) { 
                            ?>
                                <tr>
                                    <td><?= $count++ ?></td>
                                    <td><?= $role['role_name'] ?></td>
                                    <td>
                                        <?php foreach($role['permissions'] as $perm) { ?>
                                            <span class="badge badges bg-info"><?= $perm['perm_name'] ?></span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php if($EditRoles) { ?>
                                            <a id="edit" href="#edit-role" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#edit-role" data-role-id="<?php echo $role['role_id'] ?>" data-role-name="<?php echo $role['role_name'] ?>" data-role-perm="<?php //echo $role['permIDs']; ?>"><i class="fas fa-pencil-alt"></i></a>
                                        <?php } ?>
                                        <?php if($DeleteRoles) { ?>
                                            <a id="delete" class="btn btn-danger btn-sm" onclick="del(this)" data-href="RoleManagement/deleteRole/<?php echo $role['role_id']; ?>" data-delete-name="<?php echo $role['role_name'] ?>"><i class="fas fa-trash"></i></a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <!------ MODAL FOR ADDING ROLE ------>
    <div class="modal fade" id="add-role">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-color">
                    <h5 class="modal-title">Add Role</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="<?= base_url('RoleManagement/newRole'); ?>">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="form-group required">
                                <label for="role">Role</label>
                                <input type="text" class="form-control" id="role" name="role" autocomplete="off" required>
                            </div>
                            <div class="form-group">
                                <label for="permission">Permission</label>
                                <select class="form-control select2bs4-search" name="permissions[]" multiple="multiple">
                                    <option></option>
                                    <?php foreach($permissions as $perm) { ?>
                                        <option value="<?= $perm['id'] ?>"><?= $perm['permission'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!------ MODAL FOR EDITING ROLE ------>
    <div class="modal" id="edit-role" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-color">
                    <h5 class="modal-title">Edit Role</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="<?= base_url('RoleManagement/editRole'); ?>">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <input type="text" name="roleID" hidden>
                            <div class="form-group required">
                                <label for="role">Role</label>
                                <input type="text" class="form-control" id="role" name="role" value="" autocomplete="off" required>
                            </div>
                            <div class="form-group">
                                <label for="permission">Permission</label>
                                <select class="form-control select2bs4-search" name="permission[]" multiple="multiple">
                                    <option></option>
                                </select>
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
    <div class="modal fade" id="role-delete">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body text-center py-5">
                    <i class="fas fa-exclamation-circle mb-4" style="font-size: 100px; color: #DC3545;"></i> <br>
                    <h4 class="mb-4">Are you sure you want to delete <b id="roleName"></b>?</h4>
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
            $('#rolesTable').DataTable();
        } );


        $('#edit-role').on('show.bs.modal', function(e) {
            var role_id = $(e.relatedTarget).data('role-id');
            var role_name = $(e.relatedTarget).data('role-name');
            var role_perm = $(e.relatedTarget).data('role-perm');        

            console.log(role_perm[2]);

            $(e.currentTarget).find('input[name="roleID"]').val(role_id);
            $(e.currentTarget).find('input[name="role"]').val(role_name);
        });

        $('#role-delete').on('show.bs.modal', function(e) {
            $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
            var name = $(e.relatedTarget).data('role-name');
            var str = "" + name;
            $('#roleName').html(str);
        });
    </script>
<?= $this->endSection(); ?>



