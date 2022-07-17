<?= $this->extend("layouts/user_account_layout"); ?>

<?= $this->section("sidebar_menu"); ?>
    <?= $this->include('test_menu') ?>
<?= $this->endSection(); ?>

<?= $this->section("page-title"); ?>
    Graduation Clearance Application
<?= $this->endSection(); ?>

<?= $this->section("breadcrumb"); ?>
    <li class="breadcrumb-item"><a href="#"><i class="nav-icon fas fa-tachometer-alt"></i>Dashboard</a></li>
    <li class="breadcrumb-item active">Graduation Clearance Application</li>
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
            <div class="card" style="background-color: #F7F7F7;">
                <div class="card-header d-flex align-items-center" style="color:#800000;">
                    <h5 class="font-weight-bold m-0">
                        Graduation Clearance Application
                    </h5>
                </div>

                <form class="form" method="POST" action="#" >
                    <div class="card-body">
                        <div class="row pb-2">
                            <div class="col-md-8">
                                <label for="Course" style="font-size: 14px;">Course</label>
                                <select class="form-control selectpicker light-border" title="Select Course" name="course" id="Course">
                                    <option value="all" <?php echo ($courseFil == "all") ? "selected" : ""; ?>>All</option>
                                    <?php foreach($courses as $c) { ?>
                                        <option value="<?= $c['id'] ?>" <?php echo ($courseFil == $c['id']) ? "selected" : ""; ?> ><?= $c['course_name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="YearLevel" style="font-size: 14px;">Year</label>
                                <select class="form-control selectpicker light-border" title="Select Year Level" name="year" id="YearLevel">
                                    <option value="all" <?php echo ($yearFil == "all") ? "selected" : ""; ?>>All</option>
                                    <option value="1" <?php echo ($yearFil == "1") ? "selected" : ""; ?>>1st Year</option>
                                    <option value="2" <?php echo ($yearFil == "2") ? "selected" : ""; ?>>2nd Year</option>
                                    <option value="3" <?php echo ($yearFil == "3") ? "selected" : ""; ?>>3rd Year</option>
                                    <option value="4" <?php echo ($yearFil == "4") ? "selected" : ""; ?>>4th Year</option>
                                    <option value="5" <?php echo ($yearFil == "5") ? "selected" : ""; ?>>5th Year</option>
                                    <option value="x" <?php echo ($yearFil == "0") ? "selected" : ""; ?>>Graduate</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-success mr-1 "> Filter </button>
                                <input type="submit" name="clearFilter" class="btn btn-secondary" value="Clear Filter">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card">
                <div class="card-body">
                    <table id="gradApplicationTable" class="table dt-responsive" style="width:100%">
                        <thead>
                            <tr>
                                <th>Date Submitted</th>
                                <th>Student Number</th>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>
                                    <a href="#" class="btn btn-primary btn-sm" title="View"><i class="fas fa-eye"></i></a>
                                    <a class="btn btn-success btn-sm" href="#" title="Approve"> <i class="fas fa-check"></i> </a>
                                    <button class="btn btn-danger btn-sm" title="Reject"> <i class="fas fa-times"></i></button>
                                </td>
                            </tr>
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
            $('#gradApplicationTable').dataTable( {
                "language": {
                    "emptyTable": "No Submitted Application"
                }
            } );
        } );
    </script>
<?= $this->endSection(); ?>



