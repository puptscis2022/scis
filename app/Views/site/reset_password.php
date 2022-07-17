<?= $this->extend("layouts/site_layout"); ?>
<?= $this->section("content"); ?>
    <div class="container d-flex justify-content-center align-items-center" style="min-height: calc(100vh - 106px);">
        <div class="row" style="width:550px;">
            <div class="mb-5">
                <div class="card my-5">
                    <h5 class="card-header fw-bold text-center p-2 text-white" style="background-color: #800000;">RESET PASSWORD</h5>
                    <div class="card-body pt-5 px-5">
                        <form class="form-reset-pass" method="POST" action="<?= base_url('home/reset/'.$reset_code); ?>">
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
                            <div class="mb-3">
                                <label for="NewPassword" class="form-label required">Create a New Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" name="pass" id="password" aria-label="NewPassword" autocomplete="off" required>
                                    <span class="input-group-text"><i class="far fa-eye" id="togglePassword" style="color: #616161;"></i></span>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="ConfirmNewPassword" class="form-label required">Re-enter your New Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" name="confirm_pass" id="confirmpass" aria-label="ConfirmNewPassword" autocomplete="off" required>
                                    <span class="input-group-text"><i class="far fa-eye" id="toggleConfirmPassword" style="color: #616161;"></i></span>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col text-center">
                                    <input class="btn btn-primary btn-block px-4 text-center" type="submit" name="reset-password" value="Reset Password">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection(); ?>