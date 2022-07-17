<?= $this->extend("layouts/user_account_layout"); ?>

<?= $this->section("sidebar_menu"); ?>
    <?= $this->include('test_menu') ?>
<?= $this->endSection(); ?>

<?= $this->section("page-title"); ?>
Graduation Clearance
<?= $this->endSection(); ?>

<?= $this->section("breadcrumb"); ?>
<li class="breadcrumb-item"><a href="#"><i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</a></li>
<li class="breadcrumb-item active">Graduation Clearance</li>
<?= $this->endSection(); ?>

<?= $this->section("content"); ?>
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
    
    <div class="row">
        <div class="col-md-12">
                <?php if($eligible) { ?>
                    <?php if($form) { ?>
                        <?php if($form->approval_status == 0): ?>
                <div class="card px-3 py-5">
                    <div class="row mt-3 justify-content-center">
                        <i class="icon fa fa-calendar-check text-success" style="font-size: 5rem;"></i>
                    </div>
                    <div class="row mt-3 justify-content-center">
                        <h5 class="col-md-6 font-weight-bold mb-0 text-center px-5">
                            Your application for Graduation Clearance is on process. Kindly wait for its approval.
                        </h5>
                    </div>
                </div>


                        <?php elseif($form->approval_status == 2): ?>
                        <div class="card px-3 py-5">
                            <div class="row justify-content-center">
                                <i class="fas fa-exclamation-circle" style="font-size: 5rem; color: #800000; "></i>
                            </div>
                            <div class="row mt-3 justify-content-center">
                                <h5 class="col-md-6 font-weight-bold mb-0 text-center">
                                    Your application for Graduation Clearance has been Rejected
                                </h5>
                            </div>
                            <?php if($form->reject_reason){ ?>
                                <div class="row mt-3 justify-content-center ">
                                    <p class="col-md-4 text-center border border-danger p-3">
                                        <b>Reason:</b> <?= $form->reject_reason; ?>
                                    </p>
                                </div>
                            <?php } ?>
                            <div class="row justify-content-center mt-3">
                                <button onclick="window.location.href='<?= base_url('GraduationClearance/Apply'); ?>'" class="btn btn-md btn-primary">Re-Apply for Graduation Clearance</button>
                            </div>
                        </div>


                        <?php elseif($form->approval_status == 1): ?>
                            <?php 
                                $data = $form->data;
                                $profs = $form->resProf;
                                $cFields = $form->cEntries;
                            ?>

                        <div class="card">
                            <div class="card-header d-flex align-items-center" style="color:#800000;">
                                <h5 class="font-weight-bold m-0">
                                    Graduation Clearance Form
                                </h5>
                                <form class="form ml-auto" method="POST" action="<?= base_url('GenerateReports/GradFormReport')?>" target="_blank">
                                    <input type="text" name="form_id" value="<?= $data->clearance_form_id ?>" hidden>
                                    <button type="submit" class="btn btn-sm btn-success float-right ml-2"><i class="fas fa-print"></i> Generate Form</button>
                                </form>
                            </div>

                            <div class="card-body px-5">
                                <h5 class="font-weight-bold" style="color: #800000;">Student Information</h5>
                                <hr class="m-0">
                                <div class="table-responsive my-4">
                                    <table class="table table-sm table-bordered">

                                        <tbody>
                                        <tr class="">
                                            <th class="text-bold">Name</th>
                                            <td colspan="5"><?= $data->student_name ?></td>
                                            <th class="text-bold">Student Number</th>
                                            <td colspan="2"><?= $data->student_number ?></td>
                                        </tr>
                                        <tr class="">
                                            <th wclass="text-bold">Address</th>
                                            <td colspan="8"><?= $data->address ?></td>
                                        </tr>
                                        <tr>

                                            <th class="text-bold">Date of Birth</th>
                                            <td colspan="3"><?= $data->dob ?></td>
                                            <th class="text-bold">Gender</th>
                                            <td><?php echo ($data->gender == 1) ? "Male" : "Female"; ?></td>
                                            <th class="text-bold">Contact Number</th>
                                            <td colspan="2"><?= $data->contact ?></td>
                                        </tr>
                                        <tr>

                                        </tr>
                                        <tr>
                                            <th class="text-bold">Course</th>
                                            <td colspan="5"><?= $data->course ?></td>
                                            <th class="text-bold">Major</th>
                                            <td colspan="3"><?= ($data->major) ? $data->major : "None" ?></td>
                                        </tr>
                                        <tr>
                                            <th width="12.5%" class="text-bold" style="min-width: 120px;">Admitted in PUP</th>
                                            <th width="12.5%" class="text-bold" style="min-width: 90px;">School Year</th>
                                            <td width="12.5%" colspan="2" style="min-width: 90px;"><?= $data->admitted_year ?></td>
                                            <th width="12.5%" class="text-bold" style="min-width: 90px;">Semester</th>
                                            <td width="12.5%" style="min-width: 120px;">
                                                <?php if($data->admitted_term == 1){ ?>
                                                    <?= $data->admitted_term ?>st
                                                <?php } elseif ($data->admitted_term == 2) { ?>
                                                    <?= $data->admitted_term ?>nd
                                                <?php } ?>
                                                Semester
                                            </td>
                                            <th width="12.5%" class="text-bold" style="min-width: 130px;">Graduation</th>
                                            <th width="12.5%" class="text-bold" style="min-width: 90px;">School Year</th>
                                            <td width="12.5%" style="min-width: 100px;"><?= $data->graduation_sy ?></td>
                                        </tr>
                                        <tr>
                                            <th class="text-bold">Elementary</th>
                                            <td colspan="5"><?= $data->elem ?></td>
                                            <th class="text-bold">Year Graduated</th>
                                            <td colspan="2"><?= $data->elem_year ?></td>
                                        </tr>
                                        <tr>
                                            <th class="text-bold">High School</th>
                                            <td colspan="5"><?= $data->hs ?></td>
                                            <th class="text-bold">Year Graduated</th>
                                            <td colspan="2"><?= $data->hs_year ?></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <h5 class="font-weight-bold" style="color: #800000;">Respective Professors</h5>
                                <hr class="m-0">

                                <div class="table-responsive my-4">
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th width="21%" style="min-width: 150px;">Professor</th>
                                            <th width="10%" style="min-width: 140px;">Subject Code</th>
                                            <th width="26%" style="min-width: 280px;">Subject Description</th>
                                            <th width="13%" style="min-width: 150px;">Days</th>
                                            <th width="13%" style="min-width: 150px;">Time</th>
                                            <th width="10%" style="min-width: 120px;">Status</th>
                                            <th width="7%" style="min-width: 50px;">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach($profs as $row){ ?>
                                            <tr>
                                                <td><?= $row->professor_name; ?></td>
                                                <td><?= $row->sub_code; ?></td>
                                                <td><?= $row->sub_name; ?></td>
                                                <td><?= $row->days; ?></td>
                                                <td><?= $row->time; ?></td>
                                                <td>
                                                    <?php if($row->status == 1) { ?>
                                                        <span class="badge badge-success badges">Cleared</span>
                                                    <?php } else { ?>
                                                        <span class="badge badge-warning badges">Pending</span>
                                                    <?php } ?>
                                                </td>
                                                <td><a href="<?= base_url('RespectiveProfessors/Requirements/'.$row->entry_id) ?>" class="btn btn-primary btn-sm" title="View">View
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div>


                                <h5 class="font-weight-bold" style="color: #800000;">Clearance Fields</h5>
                                <hr class="m-0">
                                <div class="table-responsive my-4">
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th width="5%" style="min-width: 50px;">No.</th>
                                            <th width="33%" style="min-width: 250px;">Clearance Field</th>
                                            <th width="25%" style="min-width: 200px;">Position</th>
                                            <th width="24%" style="min-width: 180px;">Officer</th>
                                            <th width="11%" style="min-width: 100px;">Status</th>
                                            <th width="7%" style="min-width: 50px;">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $count = 1;
                                        foreach($cFields as $row){ ?>
                                            <tr>
                                                <td><?= $count++; ?></td>
                                                <td><?= $row->field; ?></td>
                                                <td><?= $row->position; ?></td>
                                                <td><?= $row->officer_name; ?></td>
                                                <td>
                                                    <?php if($row->status == 1) { ?>
                                                        <span class="badge badge-success badges">Cleared</span>
                                                    <?php } else { ?>
                                                        <span class="badge badge-warning badges">Pending</span>
                                                    <?php } ?>
                                                </td>
                                                <td><a href="<?= base_url('Clearance/Requirements/0-'.$row->position_id.'-'.$row->entry_id) ?>" class="btn btn-primary btn-sm" title="View">View</a></td><!--view reqs-->
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                        </div>




                            </div>

                        <?php endif; ?>
                    <?php } else { ?>
                        <div class="card px-3 py-5">
                            <div class="row justify-content-center">
                                <i class="fas fa-exclamation-circle text-primary" style="font-size: 65px; "></i>
                            </div>
                            <div class="row mt-3 justify-content-center">
                                <h5 class="col-md-6 font-weight-bold mb-0 text-center">
                                    Before proceeding with the graduation clearance process,
                                    kindly click the button below and provide the information needed.
                                </h5>
                            </div>
                            <div class="row justify-content-center mt-3">
                                <button onclick="window.location.href='<?= base_url('GraduationClearance/Apply'); ?>'" class="btn btn-md btn-primary">Apply for Graduation Clearance</button>
                            </div>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <div class="card px-3 py-5">
                        <div class="row justify-content-center">
                            <i class="fas fa-times-circle text-secondary" style="font-size: 65px;"></i>
                        </div>
                        <div class="row mt-3 justify-content-center">
                            <h5 class="col-md-6 font-weight-bold mb-0 text-center">
                                You are not yet eligible to apply for this
                            </h5>
                        </div>

                    </div>
                <?php } ?>

        </div>
    </div>
<?= $this->endSection(); ?>

