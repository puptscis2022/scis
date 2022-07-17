<?= $this->extend("layouts/user_account_layout"); ?>

<?= $this->section("sidebar_menu"); ?>
    <?= $this->include('test_menu') ?>
<?= $this->endSection(); ?>

<?= $this->section("page-title"); ?>
    Edit User
<?= $this->endSection(); ?>

<?= $this->section("breadcrumb"); ?>
    <li class="breadcrumb-item"><a href="#"><i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li class="breadcrumb-item active">Edit User</li>
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
            <form class="form" method="POST" action="<?= base_url('UserManagement/EditUserSave/'.$id); ?>" >
                <div class="card">
                    <div class="card-body p-4">
                        <h5 class="font-weight-bold" style="color: #800000;">Account Information</h5>
                        <hr class="mt-0">
                        <div class="form-row mt-2 mb-4">
                            <div class="form-group col-lg-6">
                                <label for="UserRole" class="required">Role</label>
                                <select class="form-control select2bs4" id="UserRole" onchange="editUserInfo()" name="userRole" required>
                                    <option></option>
                                    <option value="<?= $role_id; ?>" selected><?= $role; ?></option>
                                    <?php foreach($roles as $row) { ?>
                                        <?php if($row['id'] != $role_id) { ?>
                                            <option value="<?= $row['id'] ?>"><?= $row['role']; ?></option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="form-group col-lg-6">
                                <label for="Username">Username</label>
                                <input type="text" class="form-control" id="Username" name="userName" value="<?= $username; ?>" autocomplete="off" required>
                            </div>
                        </div>

                        <h5 class="font-weight-bold" style="color: #800000;">Personal Information</h5>
                        <hr class="mt-0">

                        <div class="form-row mt-2">
                            <div class="col-md-3 mb-3">
                                <label for="LastName">Last Name</label>
                                <input type="text" class="form-control" id="LastName" name="lastName" value="<?= $lastName; ?>" autocomplete="off" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="FirstName">First Name</label>
                                <input type="text" class="form-control" id="FirstName" name="firstName" value="<?= $firstName; ?>" autocomplete="off" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="MiddleName">Middle Name</label>
                                <input type="text" class="form-control" id="MiddleName" name="middleName" value="<?= $middleName; ?> " autocomplete="off">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="Suffix">Suffix</label>
                                <input type="text" class="form-control" id="Suffix" name="suffixName" value="<?= $suffixName; ?>" autocomplete="off">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="EmailAddress">Email Address</label>
                                <input type="email" class="form-control" id="EmailAddress" name="email" value="<?= $email; ?>" autocomplete="off">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="ContactNumber">Contact Number</label>
                                <input type="tel" class="form-control" id="ContactNumber" name="contact" value="<?= $contactNo; ?>" autocomplete="off">
                            </div>
                        </div>

                        <div class="form-row" id="officeField">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="Office">Office</label>
                                    <select id="Office" class="custom-select" onchange="forStudentOrg()" name="office">
                                        <option value="" selected></option>
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <fieldset id="StudentOrg">
                            <div class="form-row" >
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="StudentOrganization">Student Organization</label>
                                        <select id="StudentOrganization" class="custom-select" name="org">
                                            <option value="" selected></option>
                                            <option value=""></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="Position">Position</label>
                                        <select id="Position" class="custom-select" name="position">
                                            <option value="" selected></option>
                                            <option value=""></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                            <fieldset id="editStudentfields">
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <label for="StudentNumber">Student Number</label>
                                        <input type="text" class="form-control" id="StudentNumber" name="studentNo" value="<?= $studentNumber; ?>" autocomplete="off">
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="Year">Year</label>
                                            <select id="Year" class="form-control select2bs4" name="year" required>
                                                <option></option>
                                                <option value="<?= $year_id ?>" selected><?= $level ?></option>
                                                <?php foreach($year_levels as $row) { ?>
                                                    <?php if($row['id'] != $year_id) { ?>
                                                        <option value="<?= $row['id'] ?>"><?= $row['level']; ?></option>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="Section">Section</label>
                                            <select id="Section" class="form-control select2bs4">
                                                <option></option>
                                                <option selected>1</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-9">
                                        <div class="form-group">
                                            <label for="Course">Course</label>
                                            <select id="Course" class="form-control select2bs4" name="course">
                                                <option></option>
                                                <option value="<?= $course_id ?>" selected><?= $course ?></option>
                                                <?php foreach($courses as $row) { ?>
                                                    <?php if($row['id'] != $course_id) { ?>
                                                        <option value="<?= $row['id'] ?>"><?= $row['course_name']; ?></option>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="StudentType">Student Type</label>
                                            <select id="StudentType" class="form-control select2bs4" name="studentType">
                                                <option></option>
                                                <option value="<?= $type_id ?>" selected><?= $type ?></option>
                                                <?php foreach($types as $row) { ?>
                                                    <?php if($row['id'] != $type_id) { ?>
                                                        <option value="<?= $row['id'] ?>"><?= $row['type']; ?></option>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary float-right ml-2">Save</button>
                        <a href="<?php echo base_url('UserManagement/userslist'); ?>" class="btn btn-secondary float-right">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?= $this->endSection(); ?>
