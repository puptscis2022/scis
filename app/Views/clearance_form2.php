<?= $this->extend("layouts/user_account_layout"); ?>

<?= $this->section("sidebar_menu"); ?>
<?= $this->include('test_menu') ?>
<?= $this->endSection(); ?>

<?= $this->section("page-title"); ?>
Clearance Form
<?= $this->endSection(); ?>

<?= $this->section("breadcrumb"); ?>
<li class="breadcrumb-item"><a href="#"><i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</a></li>
<li class="breadcrumb-item active">Clearance Form</li>
<?= $this->endSection(); ?>

<?= $this->section("content"); ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center" style="color:#800000;">
                <h5 class="font-weight-bold m-0">
                    Clearance Form
                </h5>
                <?php if(session()->get('admin_access') || session()->get('registrar_access')) ?>
                <?php if($gradClearance) { ?>
                    <form class="form ml-auto" method="POST" action="<?= base_url('GenerateReports/GradFormReport')?>" target="_blank">
                        <input type="text" name="form_id" value="<?= $formID  ?>" hidden>
                        <button type="submit" class="btn btn-sm btn-success float-right ml-2"><i class="fas fa-print"></i> Generate Form</button>
                    </form>
                <?php } else { ?>
                    <form class="form ml-auto" method="POST" action="<?= base_url('GenerateReports/FormReport')?>" target="_blank">
                        <input type="text" name="form_id" value="<?= $formID  ?>" hidden>
                        <button type="submit" class="btn btn-sm btn-success float-right ml-2"><i class="fas fa-print"></i> Generate Form</button>
                    </form>
                <?php } ?>
            </div>
            <div class="card-body px-5">
                <table class="table table-bordered table-sm table-responsive-lg">
                    <tbody>
                    <tr>
                        <th width="10%" class="font-weight-bold">Name</th>
                        <td width="50%"><?= $formData->student_name ?></td>
                        <th width="15%" class="font-weight-bold">Student Number</th>
                        <td width="25%"><?= $formData->student_number ?></td>
                    </tr>
                    <tr>
                        <th width="10%" class="font-weight-bold">Course</th>
                        <td width="50%"><?= $formData->course ?></td>
                        <th width="15%" class="font-weight-bold">Year & Section</th>
                        <td width="25%"><?= $formData->year ?>-1</td>
                    </tr>
                    <tr>
                        <th width="10%" class="font-weight-bold">Student Type</th>
                        <td width="50%"><?= $formData->studType ?></td>
                        <th width="15%" class="font-weight-bold">Contact Number</th>
                        <td width="25%"><?= $formData->contact_no ?></td>
                    </tr>
                    </tbody>
                </table>
                <div class="row mt-4">
                    <div class="col-md-12">
                        <table class="table table-bordered table-responsive-lg">
                            <thead>
                            <tr>
                                <th scope="col" style="width:5%">No.</th>
                                <th scope="col" style="width:30%">Clearance Field</th>
                                <th scope="col" style="width:25%">Position</th>
                                <th scope="col" style="width:25%">Clearance Officer</th>
                                <th scope="col" style="width:15%">Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $count = 1;
                            foreach($Entries as $ent) {
                                ?>
                                <tr>
                                    <td><?= $count++; ?></td>
                                    <td><?= $ent['clearance_field']; ?></td>
                                    <td><?= $ent['pos'] ?></td>
                                    <td><?= $ent['clearance_officer']; ?></td>
                                    <td>
                                        <?php if($ent['status'] == 1) { ?>
                                            <span class="badge badge-success badges">Cleared</span>
                                        <?php } else { ?>
                                            <span class="badge badge-danger badges">Pending</span>
                                        <?php } ?>
                                    </td>
                                    <!-- <td>
                                        <a href="<?= base_url('Clearance/Requirements/'.$ent['id']) ?>" class="btn btn-primary btn-sm" title="View Requirements"><i class="fas fa-eye"></i> </a>
                                    </td> -->
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php if($gradClearance) { ?>
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <table class="table table-bordered table-responsive-lg">
                                <thead>
                                <tr>
                                    <th scope="col" style="width:5%">No.</th>
                                    <th scope="col" style="width:45%">Subject</th>
                                    <th scope="col" style="width:35%">Professor</th>
                                    <th scope="col" style="width:15%">Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $count = 1;
                                foreach($Subjects as $sub) {
                                    ?>
                                    <tr>
                                        <td><?= $count++; ?></td>
                                        <td><?= $sub['subject']; ?></td>
                                        <td><?= $sub['professor'] ?></td>
                                        <td>
                                            <?php if($sub['status'] == 1) { ?>
                                                <span class="badge badge-success badges">Cleared</span>
                                            <?php } else { ?>
                                                <span class="badge badge-danger badges">Pending</span>
                                            <?php } ?>
                                        </td>
                                        <!-- <td>
                                            <a href="<?= base_url('Clearance/Requirements/'.$ent['id']) ?>" class="btn btn-primary btn-sm" title="View Requirements"><i class="fas fa-eye"></i> </a>
                                        </td> -->
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php } ?>

                <div class="container">
                    <div class="row justify-content-around">
                        <div class="col-md-4">
                            <div class="card shadow-none border d-flex flex-fill">
                                <div class="card-body p-3">
                                    <div class="row">
                                        <div class="col-md-12 my-auto text-center">
                                            <h2 class="lead mb-0"><b>Dr. Marissa B. Ferrer</b></h2>
                                            <h6 class="text-muted mt-0">Director</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="footer border-top">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <?php if($director_sign) { ?>
                                                <div class="text-center text-success p-1" >
                                                    <i class="fas fa-check mr-1"></i> Approved
                                                </div>
                                            <?php } else { ?>
                                                <div class="text-center text-danger p-1">
                                                    <i class="fas fa-clock mr-1"></i> Pending
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card shadow-none border d-flex flex-fill">
                                <div class="card-body p-3">
                                    <div class="row">
                                        <div class="col-md-12 my-auto text-center">
                                            <h2 class="lead mb-0"><b>Prof. Mhel P. Garcia</b></h2>
                                            <h6 class="text-muted mt-0">Registrar</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="footer border-top">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <?php if($registrar_sign) { ?>
                                                <div class="text-center text-success p-1">
                                                    <i class="fas fa-check mr-1"></i> Received
                                                </div>
                                            <?php } else { ?>
                                                <div class="text-center text-danger p-1">
                                                    <i class="fas fa-clock mr-1"></i> Pending
                                                </div>
                                            <?php } ?>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <input type="button" class="btn btn-secondary float-right" value="Back" onclick="history.back()">
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>