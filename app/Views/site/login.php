<?= $this->extend("layouts/site_layout"); ?>
<?= $this->section("content"); ?>
    <div class="container d-flex justify-content-center align-items-center" style="min-height: calc(100vh - 106px);">
        <div class="row">
            <div class="mb-5">
                <div class="card my-5">
                    <h5 class="card-header fw-bold text-center p-2 text-white" style="background-color: #800000;">LOG IN</h5>
                    <div class="card-body px-5 pt-5">
                        <form class="form-login" method="POST" action="<?= base_url('home/login'); ?>" >
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
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="user-icon"><i class="fas fa-user" style="color: #616161;"></i></span>
                                <input type="text" class="form-control" id="username" placeholder="Username" aria-label="Username" name="username" autocomplete="off" required>
                            </div>

                            <div class="input-group mb-4">
                                <span class="input-group-text"><i class="fas fa-lock" style="color: #616161;"></i></span>
                                <input type="password" class="form-control" id="password" placeholder="Password" aria-label="Password" name="password" autocomplete="off" aria-describedby="togglePassword" required>
                                <span class="input-group-text"><i class="far fa-eye" id="togglePassword" style="color: #616161;"></i></span>
                            </div>

                            <div class="row mb-4">
                                <div class="col text-center">
                                    <input class="btn btn-primary btn-block px-4 text-center" type="submit" name="login" value="Log In">
                                </div>
                            </div>
                            <div class="row text-center" style="font-size: 14px;">
                                <a class="text-decoration-none" href="<?php echo base_url(); ?>/home/forgot_password">Forgot your password?</a>
                                <p class="text-decoration-none">Don't have an account yet?
                                    <a href="<?php echo base_url(); ?>/home/register">Register</a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


<?= $this->endSection(); ?>