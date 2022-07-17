<?= $this->extend("layouts/user_account_layout"); ?>

<?= $this->section("sidebar_menu"); ?>
    <?= $this->include('test_menu') ?>
<?= $this->endSection(); ?>

<?= $this->section("page-title"); ?>
    Change Password
<?= $this->endSection(); ?>

<?= $this->section("breadcrumb"); ?>
    <li class="breadcrumb-item"><a href="#"><i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li class="breadcrumb-item active">Change Password</li>
<?= $this->endSection(); ?>

<?= $this->section("content"); ?>
    <div class="row">
        <div class="col-md-12">
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
            <form class="form" method="POST" action="<?= base_url('ProfileManagement/changePassSave'); ?>" >
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-9 px-5">

                                <div class="form-group row mt-2 mb-3">
                                    <label for="currentpass" class="col-md-3 col-form-label" >Enter Current Password</label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="currentpass" name="oldPass" autocomplete="off">
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="far fa-eye" id="toggleCurrentPassword" style="color: #616161;"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row mt-2 mb-3">
                                    <label for="password" class="col-md-3 col-form-label" >Create New Password</label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="password" name="newPass" autocomplete="off">
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="far fa-eye" id="togglePassword" style="color: #616161;"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row mt-2 mb-3">
                                    <label for="confirmpass" class="col-md-3 col-form-label" >Re-enter New Password</label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="confirmpass" name="confirmNewPass" autocomplete="off">
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="far fa-eye" id="toggleConfirmPassword" style="color: #616161;"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary float-right ml-2">Save Changes</button>
                        <a href="<?php echo base_url('/test_dashboard'); ?>" class="btn btn-danger float-right">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?= $this->endSection(); ?>

