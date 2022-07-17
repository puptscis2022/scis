<?= $this->extend("layouts/user_account_layout"); ?>

<?= $this->section("sidebar_menu"); ?>
    <?= $this->include('test_menu') ?>
<?= $this->endSection(); ?>

<?= $this->section("page-title"); ?>
    Profile
<?= $this->endSection(); ?>

<?= $this->section("breadcrumb"); ?>
    <li class="breadcrumb-item"><a href="#"><i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li class="breadcrumb-item active">View Profile</li>
<?= $this->endSection(); ?>

<?= $this->section("content"); ?>
    <div class="row">
        <div class="col-12">
            <!-- ----------------- ALERT ERROR MESSAGE ------------------------- -->
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
            <div class="card">
                <div class="card-body">

                    <!--ROLE ID--->
                    <input type="text" class="form-control" id="UserRole" name="userRole" value="<?= $isStudent ?>" hidden>

                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-md-10 px-3">
                                <!--PROFILE PICTURE--->
                                <div class="text-center">
                                    <img class="img-fluid img-circle my-4" src="<?= base_url($profilePic) ?>"  style="border: 3px solid #adb5bd; margin: 0 auto; padding: 3px; width: 150px; height:150px;" alt="User profile picture">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 px-5">
                                <?php if($isStudent){ ?>
                                    <div class="form-group row mt-2 mb-2">
                                        <label for="StudentNumber" class="col-md-2 col-form-label" >Student Number</label>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control" id="StudentNumber" name="StudentNumber" value="<?php if(isset($userData['student_number'])){ echo $userData['student_number']; } ?>" disabled>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="form-group row mt-2 mb-3">
                                    <label for="LastName" class="col-md-2 col-form-label" >Last Name</label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" id="LastName" name="LastName" value="<?= $userData['last_name'] ?>" disabled>
                                    </div>
                                </div>

                                <div class="form-group row mt-2 mb-3">
                                    <label for="FirstName" class="col-md-2 col-form-label" >First Name</label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" id="FirstName" name="FirstName" value="<?= $userData['first_name'] ?>" disabled>
                                    </div>
                                </div>

                                <div class="form-group row mt-2 mb-3">
                                    <label for="MiddleName" class="col-md-2 col-form-label" >Middle Name</label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" id="MiddleName" name="MiddleName" value="<?= $userData['middle_name'] ?>" disabled>
                                    </div>
                                </div>

                                <div class="form-group row mt-2 mb-3">
                                    <label for="Suffix" class="col-md-2 col-form-label" >Suffix Name</label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" id="Suffix" name="Suffix" value="<?= $userData['suffix_name'] ?>" disabled>
                                    </div>
                                </div>

                                <div class="form-group row mt-2 mb-3">
                                    <label for="Username" class="col-md-2 col-form-label" >Username</label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" id="Username" name="userName" value="<?= $userData['username'] ?>" disabled>
                                    </div>
                                </div>

                                <div class="form-group row mt-2 mb-3">
                                    <label for="EmailAddress" class="col-md-2 col-form-label" >Email Address</label>
                                    <div class="col-md-10">
                                        <input type="email" class="form-control" id="EmailAddress" name="EmailAddress" value="<?= $userData['email'] ?>" disabled>
                                    </div>
                                </div>

                                <div class="form-group row mt-2 mb-3">
                                    <label for="ContactNumber" class="col-md-2 col-form-label" >Contact Number</label>
                                    <div class="col-md-10">
                                        <input type="tel" class="form-control" id="ContactNumber" name="ContactNumber" value="<?= $userData['contact_no'] ?>" disabled>
                                    </div>
                                </div>
                                <?php if($isStudent){ ?>
                                    <div class="studentProfile form-group row mt-1 mb-3">
                                        <label for="Course" class="col-md-2 col-form-label">Course</label>
                                        <div class="col-md-10">
                                            <input type="text" id="Course" class="form-control" name="Course" value="<?php if(isset($userData['courseName'])) { echo $userData['courseName']; } ?>" disabled>
                                        </div>
                                    </div>
                                    <div class="studentProfile form-group row mt-2 mb-3">
                                        <label for="Year" class="col-md-2 col-form-label" >Year Level</label>
                                        <div class="col-md-10">
                                            <input type="text" id="Year" class="form-control" name="year" value="<?php if(isset($userData['level'])) { echo $userData['level']; } ?>" disabled>
                                        </div>
                                    </div>

                                    <div class="studentProfile form-group row mt-2 mb-3">
                                        <label for="Section" class="col-md-2 col-form-label" >Section</label>
                                        <div class="col-md-10">
                                            <input type="text" id="Section" class="form-control" name="Section" value="1" disabled>
                                        </div>
                                    </div>

                                    <div class="studentProfile form-group row mt-2 mb-3">
                                        <label for="StudentType" class="col-md-2 col-form-label" >Student Type</label>
                                        <div class="col-md-10">
                                            <input type="text" id="StudentType" class="form-control" name="StudentType" value="<?php if(isset($userData['studTypeName'])) { echo $userData['studTypeName']; } ?>" disabled>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="<?php echo base_url('ProfileManagement/editProfilePage'); ?>" class="btn btn-primary float-right ml-2">Edit Profile</a>
                    <a href="<?php echo base_url('test_dashboard'); ?>" class="btn btn-secondary float-right">Back</a>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection(); ?>
