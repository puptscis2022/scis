<?= $this->extend("layouts/user_account_layout"); ?>

<?= $this->section("sidebar_menu"); ?>
    <?= $this->include('test_menu') ?>
<?= $this->endSection(); ?>

<?= $this->section("page-title"); ?>
    <?php if($reg) { ?>
        Register Request
    <?php } else { ?>
        View User
    <?php } ?>
<?= $this->endSection(); ?>

<?= $this->section("breadcrumb"); ?>
    <li class="breadcrumb-item"><a href="#"><i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li class="breadcrumb-item active">View User</li>
<?= $this->endSection(); ?>

<?= $this->section("content"); ?>
    <div class="row">
        <div class="col-12">
            <form action="">
                <div class="card">
                    <div class="card-body p-4">
                        <h5 class="font-weight-bold" style="color: #800000;">Account Information</h5>
                        <hr class="mt-0">
                        <div class="form-row mt-2 mb-4">
                            <div class="form-group col-lg-6">
                                <input type="text" class="form-control" id="UserRole" name="userRole" value="<?= $isStudent ?>" hidden>
                                <label for="UserRole" class="required">Role</label>
                                <input type="text" class="form-control" id="view-userRole" name="userRole" value="<?= $role; ?>" disabled>
                            </div>

                            <div class="form-group col-lg-6">
                                <label for="Username">Username</label>
                                <input type="text" class="form-control" id="Username" name="userName" value="<?= $username; ?>" disabled>
                            </div>
                        </div>

                        <h5 class="font-weight-bold" style="color: #800000;">Personal Information</h5>
                        <hr class="mt-0">

                        <div class="form-row mt-2">
                            <div class="col-md-3 mb-3">
                                <label for="LastName">Last Name</label>
                                <input type="text" class="form-control" id="LastName" name="lastName" value="<?= $lastName; ?>" disabled>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="FirstName">First Name</label>
                                <input type="text" class="form-control" id="FirstName" name="firstName" value="<?= $firstName; ?>" disabled>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="MiddleName">Middle Name</label>
                                <input type="text" class="form-control" id="MiddleName" name="middleName" value="<?= $middleName; ?>" disabled>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="Suffix">Suffix</label>
                                <input type="text" class="form-control" id="Suffix" name="suffixName" value="<?= $suffixName; ?>" disabled>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="EmailAddress">Email Address</label>
                                <input type="email" class="form-control" id="EmailAddress" name="email" value="<?= $email; ?>" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="ContactNumber">Contact Number</label>
                                <input type="tel" class="form-control" id="ContactNumber" name="contact" value="<?= $contactNo; ?>" disabled>
                            </div>
                        </div>

                        <div class="form-row" id="officeField">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="Office">Office</label>
                                    <select id="Office" class="custom-select" onchange="forStudentOrg()" name="office" disabled>
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
                                        <select id="StudentOrganization" class="custom-select" name="org" disabled>
                                            <option value="" selected></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="Position">Position</label>
                                        <select id="Position" class="custom-select" name="position" disabled>
                                            <option value="" selected></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        
                        <?php if($role_id == 3){ ?>
                            <fieldset id="viewStudentfields">
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <label for="StudentNumber">Student Number</label>
                                        <input type="text" class="form-control" id="StudentNumber" name="studentNo" value="<?= $studentNumber; ?>" disabled>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="Year">Year</label>
                                            <input type="text" class="form-control" id="Year" name="year" value="<?= $year; ?>" disabled>
                                        </div>
                                    </div>
    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="section">Section</label>
                                            <input type="text" class="form-control" id="section" value="1" disabled>
                                        </div>
                                    </div>
                                </div>
    
                                <div class="form-row">
                                    <div class="col-md-9">
                                        <div class="form-group">
                                            <label for="Course">Course</label>
                                            <input type="text" class="form-control" id="Course" name="course"  value="<?= $course; ?>" disabled>
                                        </div>
                                    </div>
    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="StudentType">Student Type</label>
                                            <input type="text" class="form-control" id="StudentType" name="studentType"  value="<?= $type; ?>" disabled>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        <?php } ?>
                    </div>
                    <div class="card-footer">
                        <?php if($reg) { ?>
                            <a href="<?php echo base_url('UserManagement/verifyUsers')?>" class="btn btn-primary float-right">Back</a>
                        <?php } else { ?>
                            <a href="<?php echo base_url('UserManagement/Userslist')?>" class="btn btn-primary float-right">Back</a>
                        <?php } ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?= $this->endSection(); ?>
