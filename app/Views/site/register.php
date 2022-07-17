<?= $this->extend("layouts/site_layout"); ?>
<?= $this->section("content"); ?>
    <div class="container">
        <div class="row d-flex justify-content-center">
            <div class="col-md-8 col-lg-6 col-sm-12 col-xs-12 col-xl-5 mb-5">
                <div class="card my-5">
                    <h5 class="card-header fw-bold text-center p-2 text-white" style="background-color: #800000;">REGISTER</h5>
                    <div class="card-body px-5 pt-5">
                        <form class="form-register needs-validation" method="POST" action="<?= base_url('/home/submitRegistration') ?>" novalidate>
                            <!--------------------- ALERT ERROR MESSAGE ----------------------------->
                            <?php if(session()->get('err_messages')):
                                $message = session()->get('err_messages');
                                session()->remove('err_messages');
                            ?>
                                <?php foreach($message as $row) { ?>
                                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                                        <i class="fas fa-exclamation-triangle me-1" style="color: #800000;"></i> <?php echo $row; ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php } ?>
                            <?php endif; ?>
                            <!---------------------------------------------------------------------->

                            <?php if(!empty($userInput = session()->get('inputtedData'))){
                                session()->remove('inputtedData');
                            } ?>

                            <div class="form-group mb-3">
                                <label for="reg" class="form-label required">Register as</label>
                                <select id="reg" class="form-control select2bs4" onchange="register()" name="role" required>
                                    <option></option>
                                    <?php foreach($roles as $row) { ?>
                                        <?php if($row['role'] != "Administrator" && $row['role'] != "Super Administrator" && $row['role'] != "Director" && $row['role'] != "Registrar") { ?>
                                            <option value="<?= $row['id'] ?>" <?php if(!empty($userInput['role']) && $userInput['role'] == $row['id'] ) { echo "selected"; } ?> ><?= $row['role'] ?></option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                                <?php if(!empty($userInput['role'])) {
                                   echo "<input type='text' name='role' id='reg' value='".$userInput['role']."' hidden>";
                                } ?>
                            </div>

                            <div class="form-group mb-3">
                                <label for="lastName" class="form-label required">Last Name</label>
                                <input type="text" class="form-control" name="lastName" id="lastName" aria-label="lastName" autocomplete="off" value="<?php if($userInput['lastName']) { echo $userInput['lastName']; } ?>" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="firstName" class="form-label required">First Name</label>
                                <input type="text" class="form-control" name="firstName" id="firstName" aria-label="firstName" autocomplete="off" value="<?php if($userInput['firstName']) { echo $userInput['firstName']; } ?>" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="middleName" class="form-label">Middle Name</label>
                                <input type="text" class="form-control" name="middleName" id="middleName" aria-label="middleName" autocomplete="off" value="<?php if($userInput['middleName']) { echo $userInput['middleName']; } ?>">
                            </div>
                            <div class="form-group mb-3">
                                <label for="suffixName" class="form-label">Suffix Name</label>
                                <input type="text" class="form-control" name="suffixName" id="suffixName" aria-label="suffixName" placeholder="Sr., Jr., III, IV" autocomplete="off" value="<?php if($userInput['suffixName']) { echo $userInput['suffixName']; } ?>">
                            </div>

                            <fieldset id="student">
                                <div class="form-group mb-3">
                                    <label for="studentno" class="form-label required">Student Number</label>
                                    <input type="text" class="form-control" name="studentno" id="studentno" aria-label="studentno" autocomplete="off" value="<?php if($userInput['studentno']) { echo $userInput['studentno']; } ?>">
                                </div>

                                <div class="form-group mb-3">
                                    <label for="course" class="form-label required">Course</label>
                                    <select class="form-select select2bs4" name="course" id="course">
                                        <option></option>
                                        <?php foreach($courses as $row) { ?>
                                                <option value="<?= $row['id'] ?>" <?php if(!empty($userInput['course']) && $userInput['course'] == $row['id'] ) { echo "selected"; } ?> ><?= $row['course_name'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group mb-3">
                                            <label for="year" class="form-label required">Year Level</label>
                                            <select id="year" class="form-select select2bs4" name="yearLevel" >
                                                <option></option>
                                                <option value="1" <?php if(!empty($userInput['yearLevel']) && $userInput['yearLevel'] == 1 ) { echo "selected"; } ?>>1st Year</option>
                                                <option value="2" <?php if(!empty($userInput['yearLevel']) && $userInput['yearLevel'] == 2 ) { echo "selected"; } ?>>2nd Year</option>
                                                <option value="3" <?php if(!empty($userInput['yearLevel']) && $userInput['yearLevel'] == 3 ) { echo "selected"; } ?>>3rd Year</option>
                                                <option value="4" <?php if(!empty($userInput['yearLevel'])&& $userInput['yearLevel'] == 4 ) { echo "selected"; } ?>>4th Year</option>
                                                <option value="5" <?php if(!empty($userInput['yearLevel']) && $userInput['yearLevel'] == 5 ) { echo "selected"; } ?>>5th Year</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group mb-3">
                                            <label for="section" class="form-label required">Section</label>
                                            <select id="section" class="form-select select2bs4" name="section">
                                                <option></option>
                                                <option value="1">1</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="type" class="form-label required">Student Type</label>
                                    <select id="type" class="form-select select2bs4" name="studType" >
                                        <<option></option>
                                        <?php foreach($sTypes as $row) { ?>
                                            <option value="<?= $row['id'] ?>" <?php if(!empty($userInput['studType']) && $userInput['studType'] == $row['id'] ) { echo "selected"; } ?> ><?= $row['type'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </fieldset>

                            <div class="form-group mb-3">
                                <label for="contactno" class="form-label required">Contact Number</label>
                                <input type="text" class="form-control" name="contactno" id="contactno" aria-label="contactno" placeholder="09XXXXXXXXX" autocomplete="off" value="<?php if($userInput['contactno']) { echo $userInput['contactno']; } ?>" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="email" class="form-label required">Email</label>
                                <input type="email" class="form-control" name="email" id="email" placeholder="your@email.com" aria-label="email" autocomplete="off" value="<?php if($userInput['email']) { echo $userInput['email']; } ?>" required >
                            </div>
                            <div class="form-group mb-3">
                                <label for="userName" class="form-label required">Username</label>
                                <input type="text" class="form-control" name="userName" id="userName" aria-label="userName" autocomplete="off" value="<?php if($userInput['userName']) { echo $userInput['userName']; } ?>" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="password" class="form-label required">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" name="password" id="password" aria-label="password" autocomplete="off" required>
                                    <span class="input-group-text"><i class="far fa-eye eye-icon text-muted" id="togglePassword"></i></span>
                                </div>

                            </div>
                            <div class="form-group mb-3">
                                <label for="confirmpass" class="form-label required">Confirm Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" name="confirmpass" id="confirmpass" aria-label="confirmpass" autocomplete="off" required>
                                    <span class="input-group-text"><i class="far fa-eye eye-icon text-muted" id="toggleConfirmPassword"></i></span>
                                </div>

                            </div>

                            <div class="row mt-4">
                                <div class="col text-center">
                                    <input class="btn btn-primary btn-block px-4 text-center" type="submit" name="reset-password" value="Register" >
                                </div>
                            </div>

                            <div class="row mt-4 mb-0" style="font-size: 15px;">
                                <p class="text-decoration-none text-center">Already have an account?
                                    <a href="<?php echo base_url(); ?>/">Login</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection(); ?>

<?= $this->section("script"); ?>
    <script>
        function register(){
            var reg = document.getElementById("reg");

            if (reg.value == "3"){
                document.getElementById("student").style.display="block";
                document.getElementById("type").required = true;
                document.getElementById("section").required = true;
                document.getElementById("year").required = true;
                document.getElementById("course").required = true;
                document.getElementById("studentno").required = true;
            }
            else {
                document.getElementById("student").style.display="none";
                document.getElementById("type").required = false;
                document.getElementById("section").required = false;
                document.getElementById("year").required = false;
                document.getElementById("course").required = false;
                document.getElementById("studentno").required = false;
            }
        }
    </script>
    <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                // Fetch all the forms we want to apply custom Bootstrap validation styles to
                var forms = document.getElementsByClassName('needs-validation');
                // Loop over them and prevent submission
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                            Swal.fire({
                                icon: 'warning',
                                text: 'Please note that all fields marked with an asterisk (*) are required. In order to process your registration, we ask you to provide all required information.',
                                showConfirmButton: true
                            })
                        }
                    }, false);
                });
            }, false);
        })();
    </script>

    <script>
        bootstrapValidate('#lastName', 'regex:^[a-zA-Z ]*$:You can only input alphabetic characters.')
        bootstrapValidate('#firstName', 'regex:^[a-zA-Z ]*$:You can only input alphabetic characters.')
        bootstrapValidate('#middleName', 'regex:^[a-zA-Z ]*$:You can only input alphabetic characters.')
        //bootstrapValidate('#suffixName', 'regex:^[a-zA-Z ]*$:You can only input alphabetic characters.')
        bootstrapValidate('#suffixName', 'regex:^[a-zA-Z.]*$:You can only input alphabetic characters and period.')
        bootstrapValidate('#studentno', 'regex:^[a-zA-Z0-9.-]+$:You can only input alphanumeric characters, period and dash symbol.')
        bootstrapValidate('#studentno', 'max:15:Please enter a valid student number')

        bootstrapValidate('#contactno', 'numeric:You can only input numeric characters.')
        bootstrapValidate('#contactno', 'max:11:Please enter a valid contact number.')

        bootstrapValidate('#email', 'regex:^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$:Please input a valid email address.')
        bootstrapValidate('#userName', 'regex:^[a-zA-Z0-9.-]+$:You can only input alphanumeric characters, period, and dash symbol.')
        //bootstrapValidate('#userName', 'regex:^[a-zA-Z0-9]+$:You can only input alphanumeric characters.')

        bootstrapValidate('#confirmpass', 'matches:#password:The passwords do not match.')

        bootstrapValidate(['#lastName','#firstName','#studentno','#contactno','#userName', '#password','#confirmpass','#email'], 'required:Please fill out this field.')

    </script>
    <script>
        window.onload = function(){
            var selectedVal = $('#reg :selected').text();
            if (selectedVal == "Student"){
                document.getElementById("student").style.display="block";
            }
            else {
                document.getElementById("student").style.display="none";
            }
        };
    </script>
<?= $this->endSection(); ?>
