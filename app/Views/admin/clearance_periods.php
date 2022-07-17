<?= $this->extend("layouts/user_account_layout"); ?>

<?= $this->section("sidebar_menu"); ?>
    <?= $this->include('test_menu') ?>
<?= $this->endSection(); ?>

<?= $this->section("page-title"); ?>
    Clearance Periods
<?= $this->endSection(); ?>

<?= $this->section("breadcrumb"); ?>
    <li class="breadcrumb-item"><a href="#"><i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li class="breadcrumb-item active">Clearance Periods</li>
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
            <div class="card">
                <div class="card-header d-flex align-items-center" style="color:#800000;">
                    <h5 class="font-weight-bold m-0">
                        Clearance Periods
                    </h5>
                    <?php if($AddClearancePeriods) { ?>
                        <a class="ml-auto btn btn-primary btn-sm d-inline float-right" href="<?php echo base_url("ClearancePeriods/Initiate")?>"><i class="fas fa-calendar mr-1"></i> Initiate Clearance</a>
                    <?php } ?>
                </div>

                <div class="card-body">
                    <table id="clearancePeriodTable" class="table dt-responsive display compact" style="width:100%">
                        <thead>
                            <tr>
                                <th width="10%">No.</th>
                                <th width="15%">School Year</th>
                                <!-- <th>Clearance Type</th> -->
                                <th width="15%">Semester</th>
                                <th width="15%">Start Date</th>
                                <th width="15%">Due Date</th>
                                <th width="15%">Status</th>
                                <th width="15%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $count = 1;
                            foreach($periods as $p) {
                                ?>
                                <tr>
                                    <td class="align-middle"><?= $count++ ?></td>
                                    <td class="align-middle"><?= $p['scYear'] ?></td>
                                    <!-- <td><?php //echo ($p['cType'] == 1) ? "Semestral" : "Graduation"; ?></td> -->
                                    <td class="align-middle"><?php echo ($p['semester'] == '1') ? "1st" : (($p['semester'] == '2') ? "2nd" : "Summer") ; ?></td>
                                    <td class="align-middle"><?= $p['start_date'] ?></td>
                                    <td class="align-middle"><?= $p['end_date'] ?></td>
                                    <td class="align-middle">
                                        <?php if($p['status'] == 0) { ?>
                                            <span class="badge badge-success badges">On Going</span>
                                        <?php } else if($p['status'] == 1) { ?>
                                            <span class="badge badge-secondary badges">Done</span>
                                        <?php } else if($p['status'] == 2) { ?>
                                            <span class="badge badge-primary badges">Upcoming</span>
                                        <?php } ?>
                                    </td>
                                    <td class="align-middle">
                                        <?php if(session()->get('registrar_access')) { ?>
                                            <a href="<?php echo base_url('/ClearanceCompletion/'.$p['id']) ?>" class="btn btn-primary btn-sm" title="View Records">View Records</a>
                                        <?php } else { ?>
                                            <a href="<?php echo base_url('/ClearancePeriods/Records/'.$p['id']) ?>" class="btn btn-primary btn-sm" title="View Records">View Records</a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection(); ?>

<?= $this->section("script"); ?>

    <script>
        $(document).ready(function() {
            $('#clearancePeriodTable').DataTable();
        } );
    </script>

<?= $this->endSection(); ?>



