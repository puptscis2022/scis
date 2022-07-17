<?= $this->extend("layouts/user_account_layout"); ?>

<?= $this->section("sidebar_menu"); ?>
    <?= $this->include('test_menu') ?>
<?= $this->endSection(); ?>

<?= $this->section("page-title"); ?>
    Users List
<?= $this->endSection(); ?>

<?= $this->section("breadcrumb"); ?>
    <li class="breadcrumb-item"><a href="#"><i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li class="breadcrumb-item active">Users List</li>
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
                        Users
                    </h5>
                    <?php if($AddUsers) { ?>
                        <a href="<?php echo base_url("UserManagement/AddUser")?>" class="ml-auto btn btn-primary btn-sm d-inline float-right"><i class="fas fa-user-plus"></i> Add User</a>
                        <a href="#upload-users" class="btn btn-sm btn-secondary ml-2" data-toggle="modal" data-target="#upload-users"><i class="fas fa-file-upload mr-1"></i> Upload Users</a>
                    <?php } ?>
                </div>

                <div class="card-body">
                    <table id="ListOfUsersTable" class="table dt-responsive display compact nowrap" style="width:100%">
                        <thead>
                        <tr>
                            <th width="10%">No.</th>
                            <th width="20%">Username</th>
                            <th width="25%">Name</th>
                            <th width="25%">Role</th>
                            <th width="20%">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $row_num = 1;
                        foreach($users as $user ) {
                            ?>
                            <tr>
                                <td class="align-middle"><?= $row_num; ?></td>
                                <td class="align-middle"><?= $user->username; ?></td>
                                <td class="align-middle"><?= $user->name; ?></td>
                                <td class="align-middle"><?php foreach($user->roles as $role) { echo $role."<br>"; } ?></td>
                                <td class="align-middle">
                                     <a href="ViewUser/<?php echo $user->id; ?>" class="btn btn-primary btn-sm" id="view"><i class="fas fa-eye"></i></a>
                                    <?php if($EditUsers) { ?>
                                        <a href="EditUser/<?php echo $user->id; ?>" class="btn btn-secondary btn-sm" id="edit"><i class="fas fa-pencil-alt"></i></a>
                                    <?php } ?>
                                    <?php if($DeleteUsers) { ?>
                                        <a class="btn btn-danger btn-sm" onclick="del(this)" data-href="DeleteUser/<?php echo $user->id; ?>" data-delete-name="<?php echo $user->name; ?>" id="delete"><i class="fas fa-trash"></i></a>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php
                            $row_num += 1;
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal for Uploading Users -->
    <div class="modal fade" id="upload-users" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <!-- Upload csv file for users info -->
                <form class="form" method="POST" action="<?= base_url('UserManagement/UploadUsers'); ?>" enctype="multipart/form-data">
                    <div class="modal-header modal-header-color">
                        <h5 class="modal-title">Upload Users</b></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group required">
                                    <label for="Role">Role</label>
                                    <select id="Role" class="form-control select2bs4" name="role">
                                        <option></option>
                                        <?php foreach($roles as $r) { ?>
                                            <option value="<?= $r['id'] ?>"><?= $r['role'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="container-fluid">
                            <input type="file" class="dropify" data-max-file-size="30M" data-allowed-file-extensions="csv" name="newUsers">
                            <p class="mt-2 mb-0"><span class="text-danger">*</span>File must be in <strong>csv</strong> file format. </p>
                            <p class="mb-0"><span class="text-danger">*</span>Maximum file size is 30 MB.</p>
                            <p><span class="text-danger">*</span>You can download the format for uploading users <a href="<?= base_url('uploads/scis_files/Upload User Format.csv') ?>">here</a>.</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?= $this->endSection(); ?>

<?= $this->section("script"); ?>
    <script>
        $(document).ready(function() {
            $('#ListOfUsersTable').DataTable();
        } );
    </script>
<?= $this->endSection(); ?>



