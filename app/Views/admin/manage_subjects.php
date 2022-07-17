<?= $this->extend("layouts/user_account_layout"); ?>

<?= $this->section("sidebar_menu"); ?>
    <?= $this->include('test_menu') ?>
<?= $this->endSection(); ?>

<?= $this->section("page-title"); ?>
    Manage Subjects
<?= $this->endSection(); ?>

<?= $this->section("breadcrumb"); ?>
    <li class="breadcrumb-item"><a href="#"><i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li class="breadcrumb-item active">Manage Subjects</li>
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
                        Subjects
                    </h5>
                    <?php if($AddSubjects) { ?>
                        <a class="ml-auto btn btn-primary btn-sm d-inline float-right" data-toggle="modal" data-target="#add-subject"><i class="fas fa-plus mr-1"></i> Add Subject</a>
                    <?php } ?>
                </div>
                <div class="card-body">
                    <table id="ListOfSubjectsTable" class="table dt-responsive display compact" style="width:100%">
                        <thead>
                            <tr>
                                <th width="10%">No.</th>
                                <th width="15%">Code</th>
                                <th width="55%">Subject Description</th>
                                <th width="20%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $count = 1;
                                foreach($Subjects as $row)
                                {
                            ?>
                                <tr>
                                    <td class="align-middle"><?= $count; ?></td>
                                    <td class="align-middle"><?= $row['code'] ?></td>
                                    <td class="align-middle"><?= $row['subject'] ?></td>
                                    <td class="align-middle">
                                        <?php if($EditSubjects) { ?>
                                            <a href="#edit-subject" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#edit-subject" data-subject-id="<?php echo $row['id']; ?>" data-subject-code="<?= $row['code'] ?>" data-subject-desc="<?php echo $row['subject']; ?>"><i class="fas fa-pencil-alt"></i> </a>
                                        <?php } ?>
                                        <?php if($DeleteSubjects) { ?>
                                            <a class="btn btn-danger btn-sm" onclick="del(this)" data-href="Subjects/deleteSubject/<?php echo $row['id']; ?>" data-delete-name="<?php echo $row['code'] ?>" id="delete"><i class="fas fa-trash"></i> </a>
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

    <!------ MODAL FOR ADDING Subject ------>
    <div class="modal fade" id="add-subject">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-color">
                    <h5 class="modal-title">Add Subject</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="<?= base_url('Subjects/newSubject'); ?>">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="form-group required">
                                <label for="SubjectName">Code</label>
                                <input type="text" class="form-control" id="SubjectCode" name="subjectCode" autocomplete="off" required>
                            </div>
                            <div class="form-group required">
                                <label for="SubjectDesc">Subject Description</label>
                                <input type="text" class="form-control" id="SubjectDesc" name="subjectDesc" autocomplete="off" required>
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

    <!------ MODAL FOR EDITING Subject ------>
    <div class="modal fade" id="edit-subject">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-color">
                    <h5 class="modal-title">Edit Subject</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="<?= base_url('Subjects/editSubject'); ?>">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <input type="text" class="form-control" name="subjectID" value="" hidden required>
                            <div class="form-group required">
                                <label for="SubjectName">Code</label>
                                <input type="text" class="form-control" name="subjectCode" autocomplete="off" required>
                            </div>
                            <div class="form-group required">
                                <label for="SubjectDesc">Subject Description</label>
                                <input type="text" class="form-control" name="subjectDesc" value="" autocomplete="off" required>
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
            $('#ListOfSubjectsTable').DataTable();
        } );
    </script>

    <script> //For Managing Subjects Modal
        $('#edit-subject').on('show.bs.modal', function(e) {
            var subject_id = $(e.relatedTarget).data('subject-id');
            var subject_code = $(e.relatedTarget).data('subject-code');
            var subject_desc = $(e.relatedTarget).data('subject-desc');        

            $(e.currentTarget).find('input[name="subjectID"]').val(subject_id);
            $(e.currentTarget).find('input[name="subjectDesc"]').val(subject_desc);
            $(e.currentTarget).find('input[name="subjectCode"]').val(subject_code);
        });
    </script>
<?= $this->endSection(); ?>


