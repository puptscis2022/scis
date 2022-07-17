<?= $this->extend("layouts/user_account_layout"); ?>

<?= $this->section("sidebar_menu"); ?>
    <?= $this->include('test_menu') ?>
<?= $this->endSection(); ?>

<?= $this->section("page-title"); ?>
    Initiate Clearance
<?= $this->endSection(); ?>

<?= $this->section("breadcrumb"); ?>
    <li class="breadcrumb-item"><a href="#"><i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li class="breadcrumb-item active">Initiate Clearance</li>
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
            <form class="form" method="POST" action="<?= base_url('ClearancePeriods/InitiateClearance'); ?>">
                <div class="card ">
                    <div class="card-body p-4">
                        <div class="form-row mt-2">
                            <div class="form-group required col-lg-4">
                                <label for="schoolYear" class="control-label">School Year</label>
                                <select class="form-control select2bs4" title="Choose" id="schoolYear" name="schoolYear" required>
                                    <option></option>
                                    <?php foreach($ScYears as $row) { ?>
                                        <option value="<?= $row['id'] ?>"><?= $row['school_year'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="form-group required col-lg-4" hidden>
                                <label for="clearanceType" class="control-label">Clearance Type</label>
                                <select class="form-control select2bs4"" name="clearanceType" required>
                                    <option value="1" selected>Semestral</option>
                                    <?php foreach($ClearanceTypes as $row) { ?>
                                        <option value="<?= $row['id'] ?>"><?= $row['type'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="form-group required col-lg-4">
                                <label for="semester" class='control-label'>Semester</label>
                                <select class="form-control select2bs4" name="semester" required>
                                    <option></option>
                                    <option value="1">1st Semester</option>
                                    <option value="2">2nd Semester</option>
                                    <option value="3">Summer</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group required col-lg-4">
                                <label for="startDate">Start Date</label>
                                <input type="date" class="form-control" id="startDate" name="clearanceStartDate" autocomplete="off"  required >
                            </div>

                            <div class="form-group required col-lg-4">
                                <label for="dueDate">Due Date</label>
                                <input type="date" class="form-control" id="dueDate" name="clearanceDueDate" autocomplete="off" required>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <?php if(empty($eligibilityCheck)) { ?>
                            <button type="submit" class="btn btn-primary float-right">Initiate</button>
                        <?php }else{ ?>
                            <button type="submit" class="btn btn-primary float-right" disabled>Initiate</button>
                        <?php } ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?= $this->endSection(); ?>
