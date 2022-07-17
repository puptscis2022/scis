<?= $this->extend("layouts/user_account_layout"); ?>

<?= $this->section("sidebar_menu"); ?>
    <?= $this->include('test_menu') ?>
<?= $this->endSection(); ?>

<?= $this->section("page-title"); ?>
    Edit Profile
<?= $this->endSection(); ?>

<?= $this->section("breadcrumb"); ?>
    <li class="breadcrumb-item"><a href="#"><i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li class="breadcrumb-item active">Edit Profile</li>
<?= $this->endSection(); ?>

<?= $this->section("content"); ?>
    <div class="row">
        <div class="col-md-12">
            <!------------------- ALERT ERROR MESSAGE --------------------------->
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
            <form class="form" method="POST" action="<?= base_url('ProfileManagement/editProfileSave'); ?>" enctype="multipart/form-data">
                <div class="card">
                    <div class="card-body ">

                        <!--ROLE ID--->
                        <input type="text" class="form-control" id="UserRole" name="userRole" value="<?= $isStudent ?>" hidden>

                        <div class="container">
                            <div class="row justify-content-center">
                                <div class="col-md-12 px-5">
                                    <!--PROFILE PICTURE--->
                                    <div class="text-center">
                                        <img class="img-fluid img-circle my-4" src="<?= base_url($profilePic) ?>"  id="profileDisplay" style="border: 3px solid #adb5bd; margin: 0 auto; padding: 3px; width: 150px; height:150px;" alt="User profile picture">
                                    </div>
                                </div>
                            </div>

                            <div class="row justify-content-center mb-3">
                                <div class="col-md-12 px-5 text-center">
                                    <h6 class="font-weight-bold">Change Image</h6>
                                    <h6 class="text-muted">You can upload a JPG, PNG or GIF file (File size limit is 5MB).<br> Recommended size 200x200.</h6>
                                    <input type="file" onChange="displayImage(this)" id="profileImage" data-max-file-size="5M" data-allowed-file-extensions="jpeg png jpg" name="profilePic" hidden/>
                                    <label for="profileImage" class="btn btn-sm btn-primary m-0"><i class="fas fa-upload"></i> Choose Image</label>
                                    <a class="btn btn-sm btn-outline-secondary m-0">Remove</a>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 px-5">
                                    <?php if($isStudent){ ?>
                                        <div class="form-group row mt-4 mb-2">
                                            <label for="StudentNumber" class="col-md-2 col-form-label" >Student Number</label>
                                            <div class="col-md-10">
                                                <input type="text" class="form-control" id="StudentNumber" name="StudentNumber" value="<?php if(isset($userData['student_number'])){ echo $userData['student_number']; } ?>">
                                            </div>
                                        </div>
                                    <?php } ?>

                                    <div class="form-group row mt-2 mb-3">
                                        <label for="LastName" class="col-md-2 col-form-label" >Last Name</label>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control" id="LastName" name="LastName" value="<?= $userData['last_name'] ?>" autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="form-group row mt-2 mb-3">
                                        <label for="FirstName" class="col-md-2 col-form-label" >First Name</label>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control" id="FirstName" name="FirstName" value="<?= $userData['first_name'] ?>" autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="form-group row mt-2 mb-3">
                                        <label for="MiddleName" class="col-md-2 col-form-label" >Middle Name</label>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control" id="MiddleName" name="MiddleName" value="<?= $userData['middle_name'] ?>" autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="form-group row mt-2 mb-3">
                                        <label for="Suffix" class="col-md-2 col-form-label" >Suffix Name</label>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control" id="Suffix" name="Suffix" value="<?= $userData['suffix_name'] ?>" autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="form-group row mt-2 mb-3">
                                        <label for="Username" class="col-md-2 col-form-label" >Username</label>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control" id="Username" name="userName" value="<?= $userData['username'] ?>" autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="form-group row mt-2 mb-3">
                                        <label for="EmailAddress" class="col-md-2 col-form-label" >Email Address</label>
                                        <div class="col-md-10">
                                            <input type="email" class="form-control" id="EmailAddress" name="EmailAddress" value="<?= $userData['email'] ?>" autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="form-group row mt-2 mb-3">
                                        <label for="ContactNumber" class="col-md-2 col-form-label" >Contact Number</label>
                                        <div class="col-md-10">
                                            <input type="tel" class="form-control" id="ContactNumber" name="ContactNumber" value="<?= $userData['contact_no'] ?>" autocomplete="off">
                                        </div>
                                    </div>
                                    <?php if($isStudent){ ?>
                                        <div class="form-group row mt-1 mb-3">
                                            <label for="Course" class="col-md-2 col-form-label">Course</label>
                                            <div class="col-md-10">
                                                <select id="Course" class="form-control select2bs4" name="Course"  >
                                                    <option value="<?= $userData['courseId']  ?>" selected><?= $userData['courseName'] ?></option>
                                                    <?php foreach($courses as $row) { ?>
                                                        <?php if($row['id'] != $userData['courseId']) { ?>
                                                            <option value="<?= $row['id'] ?>"><?= $row['course_name']; ?></option>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row mt-2 mb-3">
                                            <label for="Year" class="col-md-2 col-form-label" >Year Level</label>
                                            <div class="col-md-10">
                                                <select id="Year" class="form-control select2bs4" name="year" >
                                                    <option value="<?= $userData['yearId'] ?>" selected><?= $userData['level'] ?></option>
                                                    <?php foreach($year_level as $row) { ?>
                                                        <?php if($row['id'] != $userData['yearId']) { ?>
                                                            <option value="<?= $row['id'] ?>"><?= $row['level']; ?></option>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row mt-2 mb-3">
                                            <label for="Section" class="col-md-2 col-form-label" >Section</label>
                                            <div class="col-md-10">
                                                <select id="Section" class="form-control select2bs4" name="Section">
                                                    <option value="1" selected>1</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row mt-2 mb-3">
                                            <label for="StudentType" class="col-md-2 col-form-label" >Student Type</label>
                                            <div class="col-md-10">
                                                <select id="StudentType" class="form-control select2bs4" name="StudentType" >
                                                    <option value="<?= $userData['studTypeId'] ?>" selected><?= $userData['studTypeName'] ?></option>
                                                    <?php foreach($studTypes as $row) { ?>
                                                        <?php if($row['id'] != $userData['studTypeId']) { ?>
                                                            <option value="<?= $row['id'] ?>"><?= $row['type']; ?></option>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>


                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary float-right ml-2">Save Changes</button>
                        <a href="<?php echo base_url('ProfileManagement'); ?>" class="btn btn-danger float-right">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?= $this->endSection(); ?>


