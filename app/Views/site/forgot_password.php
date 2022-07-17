<?= $this->extend("layouts/site_layout"); ?>
<?= $this->section("content"); ?>
    <div class="container d-flex justify-content-center align-items-center" style="min-height: calc(100vh - 106px);">
        <div class="row" style="width:550px;">
            <div class="mb-5">
                <div class="card my-5">
                    <h5 class="card-header fw-bold text-center p-2 text-white" style="background-color: #800000;">FORGOT PASSWORD?</h5>
                    <div class="card-body pt-5 px-5">
                        <form class="form-forgot-pass" method="POST" action="<?= base_url('home/request_password_reset'); ?>">
                            <div class="row mb-4">
                                <div class="col text-center">
                                    <p>Please enter the email address associated with your account and we'll send you a link to reset your password. </p>
                                </div>
                            </div>
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

                            <div class="input-group mb-4">
                                <span class="input-group-text" id="email-icon"><i class="fas fa-at" style="color: #616161;"></i></span>
                                <input type="email" class="form-control" name="email" id="email" placeholder="Email" aria-label="Email" autocomplete="off" required>
                            </div>
                            <div class="row mb-4">
                                <div class="col text-center">
                                    <input class="btn btn-primary btn-block px-4 text-center" type="submit" name="request-reset-link" value="Request Reset Link">
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                               <p class="align-left" style="font-size: 14px;"><i class="fas fa-long-arrow-alt-left me-1" style="color: #616161; font-size: 11px;"></i> Return to <a class=" text-decoration-none" style="font-size: 14px;" href="<?= base_url() ?>"> Login Page</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection(); ?>