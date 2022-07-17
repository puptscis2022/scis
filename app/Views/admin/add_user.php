<?= $this->extend("layouts/user_account_layout"); ?>

<?= $this->section("sidebar_menu"); ?>
    <?= $this->include('test_menu') ?>
<?= $this->endSection(); ?>

<?= $this->section("page-title"); ?>
    Add User
<?= $this->endSection(); ?>

<?= $this->section("breadcrumb"); ?>
    <li class="breadcrumb-item"><a href="#"><i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li class="breadcrumb-item active">Add User</li>
<?= $this->endSection(); ?>

<?= $this->section("content"); ?>
    <div class="row">
        <div class="col-12">
            <!------------------- ALERT ERROR MESSAGE ----------------------------->
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
            <!------------------------------------------------------------------->
            <form class="form" method="POST" action="<?= base_url('UserManagement/NewUserSave'); ?>">
                <div class="card">
                    <div class="card-body p-4">
                        <h5 class="font-weight-bold" style="color: #800000;">Account Information</h5>
                        <hr class="mt-0">
                        <div class="form-row mt-2 mb-4">
                            <div class="form-group required col-lg-4">
                                <label for="UserRole" class='control-label'>Role</label>
                                <select class="form-control select2bs4" id="UserRole" onchange="addUserForm()" name="userRole" required>
                                    <option></option>
                                    <?php foreach($roles as $row) { ?>
                                        <option value="<?= $row['id'] ?>"><?= $row['role']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="form-group required col-lg-4">
                                <label class='control-label' for="Username">Username</label>
                                <input type="text" class="form-control" id="Username" placeholder="Username" name="userName" autocomplete="off" required>
                            </div>

                            <div class="form-group required col-lg-4">
                                <label for="Password">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="pass" autocomplete="off" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="far fa-eye" id="togglePassword" style="color: #616161;"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h5 class="font-weight-bold" style="color: #800000;">Personal Information</h5>
                        <hr class="mt-0">

                        <div class="form-row mt-2">
                            <div class="form-group required col-md-3 mb-3">
                                <label for="LastName">Last Name</label>
                                <input type="text" class="form-control" id="LastName" placeholder="Last Name" name="lastName" autocomplete="off" required>
                            </div>
                            <div class="form-group required col-md-3 mb-3">
                                <label for="FirstName">First Name</label>
                                <input type="text" class="form-control" id="FirstName" placeholder="First Name" name="firstName" autocomplete="off" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="MiddleName">Middle Name</label>
                                <input type="text" class="form-control" id="MiddleName" placeholder="Middle Name" name="middleName" autocomplete="off">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="Suffix">Suffix Name</label>
                                <input type="text" class="form-control" id="Suffix" placeholder="Sr., Jr., III, IV" name="suffixName" autocomplete="off">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group required col-md-6 mb-3">
                                <label for="EmailAddress">Email Address</label>
                                <input type="email" class="form-control" id="EmailAddress" placeholder="Email Address" name="email" autocomplete="off">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="ContactNumber">Contact Number</label>
                                <input type="tel" class="form-control" id="ContactNumber" placeholder="Contact Number" name="contact" autocomplete="off">
                            </div>
                        </div>

                        <fieldset id="forStudentfields">
                            <div class="form-row">
                                <div class="form-group required col-md-6">
                                    <label for="StudentNumber">Student Number</label>
                                    <input type="text" class="form-control" id="StudentNumber" placeholder="Student Number" name="studentNo" autocomplete="off">
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group required">
                                        <label for="Year">Year</label>
                                        <select id="Year" class="form-control select2bs4" name="year">
                                            <option></option>
                                            <option value="1">1st Year</option>
                                            <option value="2">2nd Year</option>
                                            <option value="3">3rd Year</option>
                                            <option value="4">4th Year</option>
                                            <option value="5">5th Year</option>
                                            <option value="0">Graduate</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group required">
                                        <label for="Section">Section</label>
                                        <select id="Section" class="form-control select2bs4" name="section">
                                            <option></option>
                                            <option value="1">1</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-9">
                                    <div class="form-group required">
                                        <label for="Course">Course</label>
                                        <select id="Course" class="form-control select2bs4" title="Choose" name="course">
                                            <option></option>
                                            <?php foreach($courses as $row) { ?>
                                                <option value="<?= $row['id'] ?>"><?= $row['course_name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group required">
                                        <label for="StudentType">Student Type</label>
                                        <select id="StudentType" class="form-control select2bs4" name="studentType">
                                            <option></option>
                                            <?php foreach($studentTypes as $row) { ?>
                                                <option value="<?= $row['id'] ?>"><?= $row['type']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary float-right">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?= $this->endSection(); ?>
