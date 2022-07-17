<?= $this->extend("layouts/user_account_layout"); ?>

<?= $this->section("sidebar_menu"); ?>
    <?= $this->include('test_menu') ?>
<?= $this->endSection(); ?>

<?= $this->section("page-title"); ?>
    Manage Majors
<?= $this->endSection(); ?>

<?= $this->section("breadcrumb"); ?>
    <li class="breadcrumb-item"><a href="#"><i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li class="breadcrumb-item active">Manage Majors</li>
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
                        Majors
                    </h5>
                    <?php if($AddMajors) { ?>
                        <a class="ml-auto btn btn-primary btn-sm d-inline float-right" data-toggle="modal" data-target="#add-major"><i class="fas fa-plus mr-1"></i> Add Major</a>
                    <?php } ?>
                </div>
                <div class="card-body">
                    <table id="ListOfMajorsTable" class="table dt-responsive display compact" style="width:100%">
                        <thead>
                            <tr>
                                <th width="10%">No.</th>
                                <th width="30%">Major</th>
                                <th width="40%">Course</th>
                                <th width="20%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $count = 1;
                                foreach($Majors as $row)
                                {
                            ?>
                                <tr>
                                    <td class="align-middle"><?= $count; ?></td>
                                    <td class="align-middle"><?= $row->major ?></td>
                                    <td class="align-middle"><?= $row->course ?></td>
                                    <td class="align-middle">
                                        <?php if($EditMajors) { ?>
                                            <a href="#edit-major" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#edit-major" data-major-id="<?php echo $row->id; ?>" data-major-name="<?= $row->major ?>" data-major-course="<?php echo $row->course_id; ?>"><i class="fas fa-pencil-alt"></i> </a>
                                        <?php } ?>
                                        <?php if($DeleteMajors) { ?>
                                            <a class="btn btn-danger btn-sm" onclick="del(this)" data-href="Majors/deleteMajor/<?php echo $row->id; ?>" data-delete-name="<?php echo $row->major ?>" id="delete"><i class="fas fa-trash"></i> </a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php
                                $count += 1;
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!------ MODAL FOR ADDING Major ------>
    <div class="modal fade" id="add-major">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-color">
                    <h5 class="modal-title">Add Major</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="<?= base_url('Majors/newMajor'); ?>">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="form-group required">
                                <label for="MajorName">Name</label>
                                <input type="text" class="form-control" id="MajorCode" name="majorName" autocomplete="off" required>
                            </div>
                            <div class="form-group required">
                                <label for="CorrespondingOrg">Corresponding Course</label>
                                <select class="form-control select2bs4" name="course" required>
                                    <option></option>
                                    <?php foreach($Courses as $row) { ?>
                                        <option value="<?= $row['id'] ?>"><?= $row['course_name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!------ MODAL FOR EDITING Major ------>
    <div class="modal fade" id="edit-major">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-color">
                    <h5 class="modal-title">Edit Major</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="<?= base_url('Majors/editMajor'); ?>">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <input type="text" class="form-control" name="majorID" value="" hidden required>
                            <div class="form-group required">
                                <label for="majorName">Name</label>
                                <input type="text" class="form-control" name="majorName" autocomplete="off" required>
                            </div>
                            <div class="form-group required">
                                <label for="CorrespondingOrg">Corresponding Course</label>
                                <select class="custom-select" name="course" required>
                                    <option></option>
                                    <?php foreach($Courses as $row) { ?>
                                        <option value="<?= $row['id'] ?>"><?= $row['course_name']; ?></option>
                                    <?php } ?>
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
            $('#ListOfMajorsTable').DataTable();
        } );
    </script>

    <script> //For Managing Courses Modal
        $('#edit-major').on('show.bs.modal', function(e) {
            var major_id = $(e.relatedTarget).data('major-id');
            var major_name = $(e.relatedTarget).data('major-name');
            var course_id = $(e.relatedTarget).data('major-course');        

            $(e.currentTarget).find('input[name="majorID"]').val(major_id);
            $(e.currentTarget).find('input[name="majorName"]').val(major_name);
            $(e.currentTarget).find('select[name="course"]').val(course_id);
        });
    </script>
<?= $this->endSection(); ?>


