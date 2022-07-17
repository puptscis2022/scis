<?= $this->extend("layouts/user_account_layout"); ?>

<?= $this->section("sidebar_menu"); ?>
    <?= $this->include('test_menu'); ?>
<?= $this->endSection(); ?>

<?= $this->section("page-title"); ?>
    Manage Requirements
<?= $this->endSection(); ?>

<?= $this->section("breadcrumb"); ?>
    <li class="breadcrumb-item"><a href="#"><i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li class="breadcrumb-item active">Manage Requirements</li>
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
                        <?= $fieldName; ?>
                    </h5>
                    <?php if($AddRequirements) { ?>
                        <a class="ml-auto btn btn-primary btn-sm d-inline float-right" data-toggle="modal" data-target="#add-requirement"><i class="fas fa-plus mr-1"></i> Add Requirement</a>
                    <?php } ?>
                </div>
                <div class="card-body">
                    <table id="ListOfRequirementsTable" class="table dt-responsive display compact" style="width:100%">
                        <thead>
                            <tr>
                                <th width="10%">No.</th>
                                <th width="30%">Requirement</th>
                                <th width="15%">Submission Type</th>
                                <th width="30%">File Type / Instruction</th>
                                <th width="15%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $count = 1;
                                foreach($Requirements as $row)
                                {
                            ?>
                            <tr>
                                <td class="align-middle"><?= $count; ?></td>
                                <td class="align-middle"><?= $row->name; ?></td>
                                <td class="align-middle"><?php echo ($row->sub_type == 1) ? "Online" : "Personal" ?></td>
                                <td class="align-middle">
                                    <?php echo ($row->file_type_id == 0) ? $row->ins : "<p class='m-0 text-primary'><b> ".$row->file_type." File</b></p>".$row->ins ?>
                                </td>
                                <td class="align-middle">
                                    <?php if($EditRequirements) { ?>
                                        <a href="#edit-requirement" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#edit-requirement" data-req-id="<?php echo $row->id; ?>" data-req-name="<?php echo $row->name; ?>" data-field-id="<?php echo $row->field_id; ?>" data-sub-id="<?php echo $row->sub_type; ?>" data-file-id="<?php echo $row->file_type_id; ?>" data-req-ins="<?=  $row->ins ?>" id="edit"><i class="fas fa-pencil-alt"></i> </a>
                                    <?php } ?>
                                    <?php if($DeleteRequirements) { ?>
                                        <a class="btn btn-danger btn-sm" onclick="del(this)" data-href="<?php echo base_url('Requirements/deleteRequirement/'.$row->id); ?>" data-delete-name="<?php echo $row->name; ?>" id="delete"><i class="fas fa-trash"></i> </a>
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

    <!------ MODAL FOR ADDING REQUIREMENT ------>
    <div class="modal fade" id="add-requirement">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-color">
                    <h5 class="modal-title">Add Requirement</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="<?= base_url('Requirements/newRequirement'); ?>">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="form-group required">
                                <label for="RequirementName">Requirement Name</label>
                                <input type="text" class="form-control" id="RequirementName" name="reqName" autocomplete="off" required>
                            </div>
                            <input type="text" name="reqFieldID" value="<?= $cField ?>" hidden>
                            <div class="form-group">
                                <label for="SubmissionType">Submission Type</label>
                                <select class="form-control select2bs4" id="SubmissionType" name="SubmissionType" onchange="forAddingReqs()" required>
                                    <option></option>
                                    <option value="1">Online</option>
                                    <option value="0">Personal</option>
                                </select>
                            </div>

                            <div class="form-group" id="forOnlineSubmission">
                                <label for="FileType">File Type</label>
                                <select class="form-control select2bs4" name="FileType">
                                    <option></option>
                                    <?php foreach($FileTypes as $row) { ?>
                                        <option value="<?= $row['id']?>"><?= $row['type'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            
                             <div class="form-group">
                                <label for="FileType">Instruction</label>
                                <textarea class="form-control" name="reqIns" rows="3" autocomplete="off"></textarea>
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

    <!------ MODAL FOR EDITING REQUIREMENTS ------>
    <div class="modal fade" id="edit-requirement">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-color">
                    <h5 class="modal-title">Edit Requirement</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="<?= base_url('Requirements/editRequirement'); ?>">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <input type="text" class="form-control" id="RequirementID" name="reqID" value="" hidden required>
                            <div class="form-group required">
                                <label for="RequirementName">Requirement Name</label>
                                <input type="text" class="form-control" id="RequirementName" name="reqName" value="" autocomplete="off" required>
                            </div>
                            <input type="text" name="reqFieldID" value="<?= $cField ?>" hidden>
                            <div class="form-group">
                                <label for="EditSubmissionType">Submission Type</label>
                                <select class="custom-select" id="EditSubmissionType" name="EditSubmissionType" onchange="forEditingReqs()" required>
                                    <option value="1">Online</option>
                                    <option value="0">Personal</option>
                                </select>
                            </div>

                            <div class="form-group" id="forEditingFileType">
                                <label for="EditFileType">File Type</label>
                                <select class="custom-select" id="EditFileType" name="EditFileType">
                                    <?php foreach($FileTypes as $row) { ?>
                                        <option value="<?= $row['id']?>"><?= $row['type'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="FileType">Instruction</label>
                                <textarea class="form-control" id="reqInstruction" name="reqIns" rows="3" autocomplete="off"></textarea>
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
            $('#ListOfRequirementsTable').DataTable();
        } );
    </script>

<?= $this->endSection(); ?>



