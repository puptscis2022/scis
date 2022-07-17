<?= $this->extend("layouts/user_account_layout"); ?>

<?= $this->section("sidebar_menu"); ?>
    <?= $this->include('test_menu') ?>
<?= $this->endSection(); ?>

<?= $this->section("page-title"); ?>
    Clearance Records
<?= $this->endSection(); ?>

<?= $this->section("breadcrumb"); ?>
    <li class="breadcrumb-item"><a href="#"><i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li class="breadcrumb-item active">Clearance Records</li>
<?= $this->endSection(); ?>

<?= $this->section("content"); ?>
<div class="row">
    <div class="col-12">
        <!------------------- ALERT ERROR MESSAGE ----------------------------->
        <?php if(session()->get('err_message')):
            $message = session()->get('err_message');
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
        <div class="row">
            <div class="col-md-12">
                <div class="callout callout-light">
                    <table class="table table-borderless m-2">
                        <tbody>
                        <tr>
                            <td class="font-weight-bold pt-1 pb-1">School Year</td>
                            <td class=" pt-1 pb-1"><?= $periodData['scyear'] ?></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold pt-1 pb-1">Clearance Type</td>
                            <td class=" pt-1 pb-1"><?= $periodData['clearanceType'] ?></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold pt-1 pb-1">Semester</td>
                            <td class=" pt-1 pb-1"><?= $periodData['sem'] ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="clearancePeriodRecords" class="table dt-responsive display compact" style="width:100%">
                            <thead>
                                <tr>
                                    <th width="5%">No.</th>
                                    <th width="15%">Student Number</th>
                                    <th width="25%">Name</th>
                                    <th width="15%">Course</th>
                                    <th width="15%">Year & Section</th>
                                    <th width="15%">Form Status</th>
                                    <th width="10%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $count = 1;
                                    foreach($student_list as $stud)
                                    {
                                ?>
                                    <tr>
                                        <td class="align-middle"><?= $count++ ?></td>
                                        <td class="align-middle"><?= $stud->student_number ?></td>
                                        <td class="align-middle"><?= $stud->student_name ?></td>
                                        <td class="align-middle"><?= $stud->course ?></td>
                                        <td class="align-middle"><?= $stud->year ?> - 1 </td>
                                        <td class="align-middle"><?php echo ($stud->status == 1) ? "<span class='badge badge-success badges'>Completed</span>" : "<span class='badge badge-danger badges'>Incomplete</span>" ;?></td>
                                        <td class="align-middle">
                                            <a href="<?= base_url('Clearance/StudentStatus/'.$stud->form_id.'-'.$period) ?>" class="btn btn-secondary btn-sm" title="View"><i class="fas fa-eye"></i></a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


<?= $this->endSection(); ?>

<?= $this->section("script"); ?>
<script>
    $(document).ready(function() {
        $('#clearancePeriodRecords').DataTable();
    } );
</script>
<?= $this->endSection(); ?>



