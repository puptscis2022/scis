<?= $this->extend("layouts/user_account_layout"); ?>

<?= $this->section("sidebar_menu"); ?>
    <?= $this->include('test_menu'); ?>
<?= $this->endSection(); ?>

<?= $this->section("page-title"); ?>
    Black List
<?= $this->endSection(); ?>

<?= $this->section("breadcrumb"); ?>
    <li class="breadcrumb-item"><a href="#"><i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li class="breadcrumb-item active">Black List</li>
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
                        <span class="mr-2"><?= $field_name; ?></span> <span class="font-weight-normal badge badge-info badge-pill"><?= $pos_name ?></span>
                    </h5>
                    <?php if($AddBlackList) { ?>
                        <a class="ml-auto btn btn-primary btn-sm d-inline float-right" data-toggle="modal" data-target="#add-student"><i class="fas fa-user-plus mr-1"></i> Add Student</a>
                    <?php } ?>
                </div>
                <div class="card-body">
                    <?php if($blacklist) { ?>
                    <div class="table-responsive">
                        <table id="BlacklistTable" class="table dt-responsive display compact" style="width:100%">
                            <thead>
                            <tr>
                                <th width="10%">No.</th>
                                <th width="25%">Name</th>
                                <th width="15%">Course</th>
                                <th width="15%">Year & Section</th>
                                <th width="25%">Deficiency</th>
                                <th width="10%">Action</th>
                            </tr>
                            </thead>
                            <tbody>

                                    <?php
                                    $count = 1;
                                    foreach($blacklist as $row) {
                                        ?>
                                        <tr>
                                            <td class="align-middle"><?= $count++ ?></td>

                                                <td class="align-middle">
                                                    <?php echo ($row->studID == 0) ? 'All Students' : $row->student_name ?>
                                                </td>
                                                <?php echo ($row->studID == 0) ? '<td class="align-middle" style="display: none;"></td>' : '<td class="align-middle">'.$row->course.'</td>' ?>
                                                <?php echo ($row->studID == 0) ? '<td class="align-middle" style="display: none;"></td>' : '<td class="align-middle">'.$row->year.'-1</td>' ?>

                                            <td class="align-middle"><?= $row->deficiency ?></td>
                                            <td class="align-middle">
                                                <?php if($EditBlackList) { ?>
                                                    <!--<a class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#edit-listed-student" id="edit"><i class="fas fa-pencil-alt"></i></a>-->
                                                <?php } ?>
                                                <?php if($DeleteBlackList) { ?>
                                                    <a class="btn btn-sm btn-danger" onclick="delBlacklist(this)" data-href="<?= base_url('/BlackList/removeStudent/'.$posID.'-'.$row->blID) ?>" data-stud-name="<?= $row->student_name ?>" data-stud-def="<?= $row->deficiency ?>" id="delete"><i class="fas fa-trash"></i></a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } else { ?>
                       <div class="row">
                           <div class="col-md-12"><h4 class="text-center font-weight-bold">No Student Listed</h4></div>
                       </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <!------ Modal for Adding Student to the Black List ------>
    <div class="modal" id="add-student" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-color">
                    <h5 class="modal-title">Add Student</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="<?= base_url("BlackList/addStudent") ?>">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="form-group required">
                                <input type="text" name="fieldID" value="<?= $posID ?>" hidden>
                                <label for="ClearanceField" class="required">Select Student</label>
                                <select class="form-control selectpicker light-border" title="Choose" id="student" name="students" data-live-search="true" data-size="8" required>
                                    <option value="0">All</option>
                                    <?php foreach($students as $stud) { ?>
                                        <option value="<?= $stud->id ?>" data-subtext="<?= $stud->course ?> <?= $stud->year ?>-1"><?= $stud->student_name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group required">
                                <label for="SubmissionType">Deficiency</label>
                                <select class="form-control selectpicker light-border" title="Choose" id="SubmissionType" name="deficiencies" required>
                                    <?php foreach($requirements as $req) { ?>
                                        <option value="<?= $req->id ?>"><?= $req->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!------ MODAL FOR EDITING LISTED STUDENT ------>
    <div class="modal" id="edit-listed-student" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-color">
                    <h5 class="modal-title">Edit Student</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="form-group required">
                                <label for="ClearanceField">Student</label>
                                <select class="form-control selectpicker light-border" name="student" data-live-search="true" required>
                                    <option value="" selected></option>
                                </select>
                            </div>
                            <div class="form-group required">
                                <label for="SubmissionType">Deficiency</label>
                                <select class="form-control selectpicker light-border" name="SubmissionType" required>
                                    <option value="" selected></option>
                                </select>
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


<?= $this->endSection(); ?>

<?= $this->section("script"); ?>
    <script>
        $(document).ready(function() {
            $('#BlacklistTable').DataTable();
        } );
    </script>
    <script>
        function delBlacklist(element){
            var studName = element.dataset.studName;
            const href = element.dataset.href;

            Swal.fire({
                title: 'Confirm Action',
                text: 'Are you sure you want to remove '+ studName.trim() + ' from the blacklist?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes',
                reverseButtons: true

            }).then((result) => {
                if (result.isConfirmed) {
                    document.location.href = href;
                }
            })
        }
    </script>
<?= $this->endSection(); ?>

