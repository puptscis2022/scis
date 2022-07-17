<?= $this->extend("layouts/user_account_layout"); ?>

<?= $this->section("sidebar_menu"); ?>
    <?= $this->include('test_menu') ?>
<?= $this->endSection(); ?>

<?= $this->section("page-title"); ?>
    Activity Logs
<?= $this->endSection(); ?>

<?= $this->section("breadcrumb"); ?>
    <li class="breadcrumb-item"><a href="#"><i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li class="breadcrumb-item active">Activity Logs</li>
<?= $this->endSection(); ?>

<?= $this->section("content"); ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="ActivityLogsTable" class="table dt-responsive display compact" style="width: 100%;">
                        <thead>
                        <tr>
                            <th width="10%">No.</th>
                            <th width="25%">Name</th>
                            <th width="50%">Activity</th>
                            <th width="15%">Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $count = 1;
                        foreach($logs as $row) {
                            ?>
                            <tr>
                                <td class="align-middle"><?= $count++; ?></td>
                                <td class="align-middle"><?= $row['name']; ?></td>
                                <td class="align-middle"><?= $row['activity']; ?></td>
                                <td class="align-middle"><?= $row['time_stamp']; ?></td>
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
            $('#ActivityLogsTable').DataTable();
        } );
    </script>
<?= $this->endSection(); ?>



