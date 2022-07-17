<?= $this->extend("layouts/user_account_layout"); ?>

<?= $this->section("sidebar_menu"); ?>
    <?= $this->include('test_menu') ?>
<?= $this->endSection(); ?>

<?= $this->section("page-title"); ?>
    Graduation Clearance Application
<?= $this->endSection(); ?>

<?= $this->section("breadcrumb"); ?>
    <li class="breadcrumb-item"><a href="#"><i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li class="breadcrumb-item active">Graduation Clearance Application</li>
<?= $this->endSection(); ?>

<?= $this->section("content"); ?>
<div class="row">
    <div class="col-12">
        <form class="form" method="POST" action="<?= base_url('GraduationClearance/submitApplication'); ?>" enctype="multipart/form-data">
            <div class="card ">
                <!-- /.card-header -->
                <div class="card-body  p-4">
<!--                     <h5 class="font-weight-bold" style="color: #800000;">Student Information</h5>
                    <hr class="m-0">
                    <div class="form-row mt-4">
                        <div class="form-group required col-lg-4 mb-2">
                            <label class='control-label' for="Username">Student Number</label>
                            <input type="text" class="form-control" id="studentNumber" name="studentNumber" autocomplete="off" required>
                        </div>
                    </div> -->

                    <!-- <div class="form-row">
                        <div class="form-group required col-md-4 mb-2">
                            <label for="LastName">Last Name</label>
                            <input type="text" class="form-control" id="LastName" name="lastName" autocomplete="off" required>
                        </div>
                        <div class="form-group required col-md-4 mb-2">
                            <label for="FirstName">First Name</label>
                            <input type="text" class="form-control" id="FirstName" name="firstName" autocomplete="off" required>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label for="MiddleName">Middle Name</label>
                            <input type="text" class="form-control" id="MiddleName" name="middleName" autocomplete="off">
                        </div>
                    </div> -->

                    <div class="form-row">
                        <div class="col-md-12 form-group required mb-2">
                            <label for="completeAddress">Complete Address</label>
                            <input type="text" class="form-control" id="completeAddress" name="completeAddress" autocomplete="off" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <!-- <div class="col-md-4 form-group required mb-2">
                            <label for="ContactNumber">Contact Number</label>
                            <input type="tel" class="form-control" id="ContactNumber" name="contact" autocomplete="off" required>
                        </div> -->
                        <div class="col-md-6 form-group required mb-2">
                            <label for="dateOfBirth">Date of Birth</label>
                            <input type="date" class="form-control" id="dateOfBirth" name="dateOfBirth" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="ContactNumber">Gender</label>
                            <select class="form-control selectpicker light-border" title="Choose" id="gender" name="gender" required>
                                <option value="1">Male</option>
                                <option value="2">Female</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <!-- <div class="col-md-8 mb-2">
                            <label for="course">Course</label>
                            <select class="form-control selectpicker light-border" title="Choose" id="course" name="course" required>
                                <option value=""></option>

                            </select>
                        </div> -->
                        <div class="col-md-12 mb-2">
                            <label for="major">Major</label>
                            <select class="form-control selectpicker light-border" title="Choose" id="major" name="major" <?php echo (!empty($majors)) ? "required" : "disabled" ; ?> >
                                <?php foreach($majors as $m) { ?>
                                    <option value="<?= $m->id ?>"><?= $m->major ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <h5 class="font-weight-bold mt-4"  style="color: #800000;">Educational Background</h5>
                    <hr class="m-0">
                    <div class="form-row mt-4">
                        <div class="col-md-8">
                            <h6 class="font-weight-bold">Admitted in PUP</h6>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-8 mb-2">
                            <select class="form-control selectpicker light-border" title="School Year" id="schoolYear" name="schoolYearAdmitted" required>
                                <?php foreach($scYears as $year) { ?>
                                    <option value="<?= $year['id'] ?>"><?= $year['school_year'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-2">
                            <select class="form-control selectpicker light-border" title="Semester" id="sem" name="semAdmitted" required>
                                <option value="1">1st Sem</option>
                                <option value="2">2nd Sem</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-8">
                            <h6 class="font-weight-bold">Graduating in PUP</h6>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-8 mb-2">
                            <select class="form-control selectpicker light-border" title="School Year" id="schoolYear" name="schoolYearGraduation" required>
                                <?php foreach($scYears as $year) { ?>
                                    <option value="<?= $year['id'] ?>"><?= $year['school_year'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row mt-4">
                        <div class="col-md-8 form-group required mb-2">
                            <label for="elem">Elementary School</label>
                            <input type="text" class="form-control" id="elem" name="elem" autocomplete="off" required>
                        </div>

                        <div class="col-md-4 form-group required mb-2">
                            <label for="elemYearGrad">Year Graduated</label>
                            <select class="form-control selectpicker light-border" title="Year Graduated" id="elemYearGrad" name="elemYearGrad" required>
                                <?php for($year = 2000;$year<2050;$year++) { ?>
                                    <option value="<?= $year ?>"><?= $year ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-md-8 form-group required mb-2">
                            <label for="hs">High School</label>
                            <input type="text" class="form-control" id="hs" name="hs" required>
                        </div>
                        <div class="col-md-4 form-group required mb-2">
                            <label for="hsYearGrad">Year Graduated</label>
                            <select class="form-control selectpicker light-border" title="Year Graduated" id="hsYearGrad" name="hsYearGrad" autocomplete="off" required>
                                <?php for($year = 2000;$year<2050;$year++) { ?>
                                    <option value="<?= $year ?>"><?= $year ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-8">
                            <h5 class="font-weight-bold"  style="color: #800000;">Respective Professors</h5>
                        </div>

                        <div class="col-md-4">
                            <button type="button" class="float-right btn btn-sm btn-light add"><i class="fas fa-plus-circle"></i> Add</button>
                        </div>
                    </div>


                    <div class="row">
                       <div class="col-md-12">
                           <div class="table-responsive">
                               <table class="table" id="respectiveProfs" style="width: 100%;">
                                   <tr>
                                       <th style="min-width: 400px;">Subject</th>
                                       <th style="min-width: 250px;">Professor</th>
                                       <th style="min-width: 200px;">Days</th>
                                       <th style="min-width: 200px;">Time</th>
                                       <th></th>
                                   </tr>
                                   <tr>
                                       <td>
                                           <select id="chooseSub_0" name="subject_0" class="form-control subject_code" required>
                                               <option value="">Select Subject</option>
                                               <?php foreach($subjects as $row) { ?>
                                                   <option value="<?= $row['id'] ?>"><?= $row['code'] ?> | <?= $row['subject'] ?></option>
                                               <?php } ?>
                                           </select>
                                       </td>
                                       <td>
                                           <select name="prof_0" class="form-control prof" required>
                                               <option value="">Select Professor</option>
                                               <?php foreach($professors as $row) { ?>
                                                   <option value="<?= $row->id ?>"><?= $row->name ?></option>
                                               <?php } ?>
                                           </select>
                                       </td>
                                       <td>
                                           <input type="text" name="days_0" class="form-control days" required />
                                       </td>
                                       <td>
                                           <input type="text" name="time_0" class="form-control time" required />
                                       </td>
                                       <td>
                                           <button type="button" name="remove" class="btn light btn-sm remove"><i class="fas fa-minus-circle"></i></button>
                                       </td>
                                   </tr>
                               </table>
                           </div>

                       </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-8">
                            <h5 class="font-weight-bold"  style="color: #800000;">Attachment</h5>
                        </div>
                    </div>
                    <hr class="m-0">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mt-4">
                                <label for="coc" class="mb-0">Certificate of Candidacy (COC)</label>
                                <p class="font-weight-light">You can download your Certificate of Candidacy (COC) on your SIS account.</p>
                                <input type="file" class="form-control-file" id="coc" name="coc" required>
                            </div>
                        </div>
                    </div>


                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary float-right ml-2">Submit</button>
                    <a href="<?php echo base_url('Dashboard'); ?>" class="btn btn-secondary float-right">Back</a>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection(); ?>


<?= $this->section("script"); ?>
    <script>
        $(document).ready(function(){

            $(document).on('click', '.add', function(regProf){
                var count = $('#respectiveProfs tr').length - 1;
                var html = '';
                html += '<tr>';
                html += '<td><select id="chooseSub_' + count + '" name="subject_' + count + '" class="form-control subject_code select2bs4" required><option value="">Select Subject</option><?php foreach($subjects as $row) { ?><option value="<?= $row['id'] ?>"><?= $row['code'] ?> | <?= $row['subject'] ?></option><?php } ?></select></td>';
                html += '<td><select name="prof_' + count + '" class="form-control prof" required><option value="">Select Professor</option><?php foreach($professors as $row) { ?><option value="<?= $row->id ?>"><?= $row->name ?></option><?php } ?></select></td>';
                html += '<td><input type="text" name="days_' + count + '" class="form-control days" required /></td>';
                html += '<td><input type="text" name="time_' + count + '" class="form-control time" required /></td>';
                html += '<td><button type="button" name="remove" class="btn light btn-sm remove"><i class="fas fa-minus-circle"></i></button></td></tr>';
                $('#respectiveProfs').append(html);
            });

            $(document).on('click', '.remove', function(){
                $(this).closest('tr').remove();
            });
        });
    </script>

    //For changing label of subject
    <!-- <script>
        $("#").change(function() {
            $('#help-text').text($('option:selected').attr('data-content'));
        }).change();
    </script> -->
<?= $this->endSection(); ?>

<!-- $(".addCF").click(function(){
    count = $('#customFields tr').length + 1;
    $("#customFields").append('<td>'+sel[0].outerHTML+'</td><td><input class="form-control" type="text" name="role'+count+'" /></td><td><input type="checkbox" class="mycheckbox" name="can_edit'+count+'"></td><td><input type="checkbox" class="mycheckbox" name="can_read'+count+'"></td><td><input type="checkbox" class="mycheckbox" name="can_execute'+count+'"></td><td><input type="checkbox" class="mycheckbox" name="is_admin'+count+'"></td><td><a href="javascript:void(0);" class="remCF">Remove</a></td></tr>');
    $('.mycheckbox').iCheck({checkboxClass: 'icheckbox_square-blue',radioClass: 'iradio_square-blue'});
}); -->

<!-- $("#customFields").append('<tr><td>'+sel[0].outerHTML+'</td><td><input class="form-control" type="text" name="role[]" /></td><td><input type="checkbox" class="mycheckbox" name="can_edit[]"></td><td><input type="checkbox" class="mycheckbox" name="can_read[]"></td><td><input type="checkbox" class="mycheckbox" name="can_execute[]"></td><td><input type="checkbox" class="mycheckbox" name="is_admin[]"></td><td><a href="javascript:void(0);" class="remCF">Remove</a></td></tr>');
$('.mycheckbox').iCheck({checkboxClass: 'icheckbox_square-blue',radioClass: 'iradio_square-blue'});
 -->

