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
        <form class="form" method="POST" action="<?= base_url('GraduationClearance/submitApplication'); ?>" >
            <div class="card ">
                <div class="card-body  p-4">
                    <h5 class="font-weight-bold" style="color: #800000;">Student Information</h5>
                    <hr class="m-0">

                    <div class="table-responsive my-4">
                        <table class="table table-sm table-bordered">

                            <tbody>
                            <tr class="">
                                <th class="text-bold">Name</th>
                                <td colspan="3"><?= $forms->student_name ?></td>
                                <th class="text-bold">Student Number</th>
                                <td><?= $forms->student_number ?></td>
                            </tr>
                            <tr class="">
                                <th class="text-bold">Address</th>
                                <td colspan="5"><?= $forms->address ?></td>
                            </tr>
                            <tr>

                                <th width="16%" class="text-bold">Date of Birth</th>
                                <td width="18%"><?= $forms->dob ?></td>
                                <th width="16%" class="text-bold">Gender</th>
                                <td width="16%"><?php echo ($forms->gender == 1) ? "Male" : "Female"; ?></td>
                                <th width="16%" class="text-bold">Contact Number</th>
                                <td width="18%"><?= $forms->contact ?></td>
                            </tr>
                            <tr>

                            </tr>
                            <tr>
                                <th class="text-bold">Course</th>
                                <td colspan="3"><?= $forms->course ?></td>
                                <th class="text-bold">Major</th>
                                <td colspan="2"><?= ($forms->major) ? $data->major : "None" ?></td>
                            </tr>

                            </tbody>
                        </table>
                    </div>



                    <h5 class="font-weight-bold mt-4"  style="color: #800000;">Educational Background</h5>
                    <hr class="m-0">

                    <div class="table-responsive my-4">
                        <table class="table table-sm table-bordered">
                            <tbody>
                            <tr>
                                <th width="12.5%" class="text-bold">Admitted in PUP</th>
                                <th width="10%" class="text-bold">School Year</th>
                                <td width="15%" ><?= $forms->admitted_year ?></td>
                                <th width="12.5%" class="text-bold">Semester</th>
                                <td width="12.5%">
                                    <?php if($forms->admitted_term == 1){ ?>
                                        <?= $forms->admitted_term ?>st
                                    <?php } elseif ($forms->admitted_term == 2) { ?>
                                        <?= $forms->admitted_term ?>nd
                                    <?php } ?>
                                    Semester
                                </td>
                                <th width="12.5%" class="text-bold">Graduating in PUP</th>
                                <th width="10%" class="text-bold">School Year</th>
                                <td width="15%" ><?= $forms->graduation_sy ?></td>
                            </tr>
                            <tr>
                                <th class="text-bold">Elementary</th>
                                <td colspan="3"><?= $forms->elem ?></td>
                                <th class="text-bold">Year Graduated</th>
                                <td colspan="3"><?= $forms->elem_year ?></td>
                            </tr>
                            <tr>
                                <th class="text-bold">High School</th>
                                <td colspan="3"><?= $forms->hs ?></td>
                                <th class="text-bold">Year Graduated</th>
                                <td colspan="3"><?= $forms->hs_year ?></td>
                            </tr>

                            </tbody>
                        </table>
                    </div>

                    <h5 class="font-weight-bold"  style="color: #800000;">Respective Professors</h5>
                    <hr class="m-0">


                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive my-4">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th width="21%">Professor</th>
                                        <th width="10%">Subject Code</th>
                                        <th width="30%">Subject Description</th>
                                        <th width="16%">Days</th>
                                        <th width="16%">Time</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($respectiveProf as $prof){ ?>
                                        <tr>
                                            <td><?= $prof->professor_name; ?></td>
                                            <td><?= $prof->sub_code; ?></td>
                                            <td><?= $prof->sub_name; ?></td>
                                            <td><?= $prof->days; ?></td>
                                            <td><?= $prof->time; ?></td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <h5 class="font-weight-bold"  style="color: #800000;">Attachment</h5>
                    <hr class="m-0">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive my-4">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th width="50%">Document Name</th>
                                            <th width="50%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Certificate of Candidacy (COC)</td>
                                            <td><a data-fancybox href="<?= base_url('uploads/certificate_of_candicacy/'.$forms->coc); ?>" class="btn btn-primary btn-sm">View</a></td><!--view reqs-->
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="<?= base_url('GraduationClearance/Applications'); ?>" class="btn btn-secondary float-right">Back</a>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection(); ?>

