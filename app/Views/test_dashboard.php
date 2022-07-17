<?= $this->extend("layouts/user_account_layout"); ?>

<?= $this->section("sidebar_menu"); ?>
    <?= $this->include('test_menu') ?>
<?= $this->endSection(); ?>

<?= $this->section("page-title"); ?>
    Dashboard
<?= $this->endSection(); ?>

<?= $this->section("breadcrumb"); ?>
    <li class="breadcrumb-item active"><i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</li>
<?= $this->endSection(); ?>

<?= $this->section("content"); ?>

<?php if(session()->get('superAdmin_access')) { ?>
    <h1 align="text-center">Welcome Crewmates</h1>
<?php } else { ?>

    <!------------------- ALERT ERROR MESSAGE ----------------------------->
    <?php if(session()->get('err_message')):
        $message = session()->get('err_message');
        session()->remove('err_messages');
    ?>
        <?php foreach($message as $row) { ?>
            <div class="alert alert-danger alert-dismissible">
                <i class="fas fa-exclamation-triangle mr-1" style="color: #800000;"></i>
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <?php echo $row; ?>
            </div>
        <?php } ?>
    <?php endif; ?>
    <?php if(session()->get('err_messages')):
        $message = session()->get('err_messages');
        session()->remove('err_messages');
    ?>
        <?php foreach($message as $row) { ?>
        <div class="alert alert-danger alert-dismissible">
            <i class="fas fa-exclamation-triangle mr-1" style="color: #800000;"></i>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <?php echo $row; ?>
        </div>
        <?php } ?>
    <?php endif; ?>
    <!------------------------------------------------------------------->

    <?php if($view_students) { ?>
        <?= $this->include('test_dashboard_contents/students_count') ?>
    <?php } ?>

    <?php if($view_clearancePeriods) { ?>
        <?= $this->include('test_dashboard_contents/clearance_period_detail') ?>
    <?php } ?>

    <!-----------------------MODAL FOR EXTENDING DUE DATE----------------------->
    <div class="modal" id="extend-due" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-color">
                    <h5 class="modal-title">Extend Due Date</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="<?= base_url('ClearancePeriods/extendClearancePeriod') ?>">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="form-group required">
                                <label for="dueDate">Due Date</label>
                                <input type="text" name="pID" hidden>
                                <input type="date" class="form-control" name="currentDueDate" autocomplete="off" hidden>
                                <input type="date" class="form-control" id="dueDate" name="clearanceDueDate" autocomplete="off" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>
<?= $this->endSection(); ?>
