<?= $this->extend("layouts/user_account_layout"); ?>

<?= $this->section("sidebar_menu"); ?>
    <?= $this->include('test_menu') ?>
<?= $this->endSection(); ?>

<?= $this->section("page-title"); ?>
    Manage Prerequisite
<?= $this->endSection(); ?>

<?= $this->section("breadcrumb"); ?>
    <li class="breadcrumb-item"><a href="#"><i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li class="breadcrumb-item active">Manage Prerequisite</li>
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
                        <span class="mr-2"><?= $FieldName ?></span> <span class="font-weight-normal badge badge-info badge-pill"><?= $PosName ?></span>
                    </h5>
                    <?php if($AddPrerequisites) { ?>
                        <a class="ml-auto btn btn-primary btn-sm d-inline float-right" data-toggle="modal" data-target="#add-prerequisite"><i class="fas fa-plus mr-1"></i> Add Prerequisite</a>
                    <?php } ?>
                </div>
                <div class="card-body">
                    <table id="prerequisiteTable" class="table dt-responsive display compact" style="width:100%">
                        <thead>
                            <tr>
                                <th width="10%">No.</th>
                                <th width="70%">Prerequisites</th>
                                <th width="20%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $count = 1;
                                foreach($Requisites as $row) { 
                            ?>
                                <tr>
                                    <td class="align-middle"><?= $count++ ?></td>
                                    <td class="align-middle"><span class="font-weight-bold mr-2"><?= $row['field_name'] ?></span>  <span class="badge badges badge-info badge-pill"> <?= $row['pos_name'] ?> </span></td>
                                    <td class="align-middle">
                                        <?php if($RemovePrerequisites) { ?>
                                            <a class="btn btn-danger btn-sm" onclick="remove(this)" data-href="<?= base_url('/PreRequisites/Remove/'.$row['id']) ?>" data-req-name="<?= $row['field_name'] ?>" data-field-name="<?= $FieldName ?>" id="remove">Remove</a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!------ MODAL FOR ADDING PREREQUISITE ------>
    <div class="modal fade" id="add-prerequisite">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-color">
                    <h5 class="modal-title">Add Prerequisite</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="<?= base_url('PreRequisites/Add') ?>">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="form-group">
                                <input type="text" name="id" value="<?= $currentPosID ?>" hidden>
                                <label for="">Select Prerequisite</label>
                                <select class="form-control selectpicker light-border" name="cField[]" title="Choose" multiple data-actions-box="true" required>
                                    <?php foreach($Fields as $row) { ?>
                                        <?php if($row->field_id != $currentFieldID || $row->field == "Student Organization") { ?>
                                            <option value="<?= $row->id ?>" data-subtext="<?= $row->name;  ?>"><?= $row->field ?></option>
                                        <?php } ?>
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

    <!------ MODAL FOR EDITING PREREQUISITE ------>
    <div class="modal fade" id="edit-prerequisite">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-color">
                    <h5 class="modal-title">Edit Prerequisite</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="form-group">
                                <label for="">Select Prerequisite</label>
                                <select class="form-control selectpicker" name="" title="Choose" multiple required>
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

    <!------ MODAL FOR CONFIRMING DELETION OF A PREREQUISITE ------>
    <div class="modal fade" id="prerequisite-delete">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body text-center py-5">
                    <i class="fas fa-exclamation-circle mb-4" style="font-size: 100px; color: #DC3545;"></i> <br>
                    <h4 class="mb-4">Are you sure you want to delete <b id="reqName"></b> ?</h4>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <a type="button" class="btn btn-danger btn-ok">Yes, delete it.</a>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection(); ?>

<?= $this->section("script"); ?>
    <script>
        $(document).ready(function() {
            $('#prerequisiteTable').DataTable();
        } );
    </script>
    <script>
        tippy('#remove', {
            content: 'Remove',
            followCursor: true,
        });
    </script>
    <script>
        function remove(element){
            var req = element.dataset.reqName;
            var fieldName = element.dataset.fieldName;
            const href = element.dataset.href;

            Swal.fire({
                title: 'Confirm Action',
                text: 'Are you sure you want to remove '+ req.trim() + ' as prerequisite of ' + fieldName.trim() +' ?',
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


