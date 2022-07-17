<?= $this->extend("layouts/user_account_layout"); ?>

<?= $this->section("sidebar_menu"); ?>
    <?= $this->include('test_menu') ?> 
<?= $this->endSection(); ?>

<?= $this->section("page-title"); ?>
    Manage Clearance Fields
<?= $this->endSection(); ?>

<?= $this->section("breadcrumb"); ?>
    <li class="breadcrumb-item"><a href="#"><i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li class="breadcrumb-item active">Manage Clearance Fields</li>
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
                        Clearance Fields
                    </h5>
                    <?php if($AddClearanceFields) { ?>
                        <a class="ml-auto btn btn-primary btn-sm" data-toggle="modal" data-target="#add-clearance-field"><i class="fas fa-plus mr-1"></i> Add Clearance Field</a>
                    <?php } ?>
                </div>
                <div class="card-body">
                    <table id="ListOfClearanceFieldsTable" class="table dt-responsive display compact" style="width:100%;">
                        <thead>
                            <tr>
                                <th width="5%">No.</th>
                                <th width="25%">Clearance Field</th>
                                <th width="45%">Description</th>
                                <th width="15%">Clearance Type</th>
                                <th width="10%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(session()->get('message')): ?>
                                <tr>
                                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                                        <i class="fas fa-exclamation-triangle me-1" style="color: #800000;"></i> <?php echo session()->get('message'); ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                </tr>
                            <?php else: ?>
                                <?php
                                    $count = 1;
                                    foreach($ClearanceFields as $Field)
                                    {
                                ?>
                                    <tr>
                                        <td class="align-middle"><?= $count; ?></td>
                                        <td class="align-middle"><?= $Field->name; ?></td>
                                        <td class="align-middle"><?= $Field->desc; ?></td>
                                        <td class="align-middle"><?= $Field->clearance_type; ?></td>
                                        <td class="align-middle">
                                            <?php if($EditClearanceFields) { ?>
                                                <a href="#edit-clearance-field" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#edit-clearance-field" data-field-id="<?php echo $Field->id; ?>" data-field-name="<?php echo $Field->name; ?>" data-field-desc="<?php echo $Field->desc; ?>" data-field-type="<?php echo $Field->type_id; ?>" id="edit"><i class="fas fa-pencil-alt"></i></a>
                                            <?php } ?>
                                            <?php if($DeleteClearanceFields) {  ?>
                                                <!-- <a href="deleteCField/<?php echo $Field->id; ?>" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</a> -->
                                                <a class="btn btn-danger btn-sm" onclick="del(this)" data-href="<?php echo base_url() ?>/ClearanceFields/deleteCField/<?php echo $Field->id; ?>" data-delete-name="<?php echo $Field->name ?>" id="delete"><i class="fas fa-trash"></i></a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php
                                    $count += 1;
                                    }
                                ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!------ MODAL FOR ADDING CLEARANCE FIELD ------>
    <div class="modal fade" id="add-clearance-field">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-color">
                    <h5 class="modal-title">Add Clearance Field</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="<?= base_url('ClearanceFields/newCField'); ?>">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="form-group required">
                                <label for="ClearanceFieldName">Clearance Field Name</label>
                                <input type="text" class="form-control" id="ClearanceFieldName" name="fieldName" autocomplete="off" required>
                            </div>
                            <div class="form-group">
                                <label for="ClearanceFieldDesc">Description</label>
                                <textarea class="form-control" id="ClearanceFieldDesc" name="fieldDesc" rows="3" autocomplete="off"> </textarea>
                            </div>
                            <div class="form-group required">
                                <label for="ClearanceType">Clearance Type</label>
                                <select class="form-control select2bs4" name="fieldType" required>
                                    <option></option>
                                    <?php foreach($ClearanceTypes as $row) { ?>
                                        <option value="<?= $row['id'] ?>"><?= $row['type']; ?></option>
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

    <!------ MODAL FOR EDITING CLEARANCE FIELD ------>
    <div class="modal fade" id="edit-clearance-field">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-color">
                    <h5 class="modal-title">Edit Clearance Field</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="<?= base_url('ClearanceFields/editCField'); ?>">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="form-group required">
                                <input type="text" class="form-control" name="fieldID" value="" hidden>
                            </div>
                            <div class="form-group required">
                                <label for="ClearanceFieldName">Clearance Field Name</label>
                                <input type="text" class="form-control" name="fieldName" value="" autocomplete="off" required>
                            </div>
                            <div class="form-group">
                                <label for="ClearanceFieldDesc">Description</label>
                                <textarea class="form-control" rows="3" name="fieldDesc" value="" autocomplete="off"> </textarea>
                            </div>
                            <div class="form-group required">
                                <label for="ClearanceType">Clearance Type</label>
                                <select class="custom-select" name="fieldType" required>
                                    <?php foreach($ClearanceTypes as $row) { ?>
                                        <option value="<?= $row['id'] ?>"><?= $row['type']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <input type="submit" class="btn btn-primary" name="save" value="Save">
                    </div>
                </form>
            </div>
        </div>
    </div>
<?= $this->endSection(); ?>

<?= $this->section("script"); ?>
    <script>
        $(document).ready(function() {
            $('#ListOfClearanceFieldsTable').DataTable();
        } );
    </script>
<?= $this->endSection(); ?>



