<?= $this->extend("layouts/user_account_layout"); ?>

<?= $this->section("sidebar_menu"); ?>
    <?= $this->include('test_menu') ?>
<?= $this->endSection(); ?>

<?= $this->section("page-title"); ?>
    Manage Positions
<?= $this->endSection(); ?>

<?= $this->section("breadcrumb"); ?>
    <li class="breadcrumb-item"><a href="#"><i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li class="breadcrumb-item active">Manage Positions</li>
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
                        Positions
                    </h5>
                    <?php if($AddPositions) { ?>
                        <a class="ml-auto btn btn-primary btn-sm d-inline float-right" data-toggle="modal" data-target="#add-position"><i class="fas fa-plus mr-1"></i> Add Position</a>
                    <?php } ?>
                </div>
                <div class="card-body">
                    <table id="ListOfPositionsTable" class="table dt-responsive display compact" style="width:100%">
                        <thead>
                            <tr>
                                <th width="10%">No.</th>
                                <th width="20%">Position</th>
                                <th width="30%">Clearance Field</th>
                                <th width="25%">Officer in Charge</th>
                                <th width="15%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $count = 1;
                                foreach($Positions as $pos)
                                {
                            ?>
                                <tr>
                                    <td class="align-middle"><?= $count; ?></td>
                                    <td class="align-middle"><?= $pos->name; ?></td>
                                    <td class="align-middle"><?= $pos->field; ?></td>
                                    <td class="align-middle">
                                        <?php if($pos->field == 'Student Organization') { ?>
                                            <a href="<?php echo base_url('Positions/OrganizationsOfficers/'.$pos->id)?>" class="badge badge-warning badges"> <i class="fas fa-external-link-alt mr-1"></i> List of <?= $pos->name; ?></a>
                                        <?php }else{ ?>
                                            <?php if($pos->co_name== null) { ?>
                                                <!--SHOW ONLY IF NO CO IS ASSIGNED-->
                                                <span class="badge badge-danger badges">No Assigned Officer</span>
                                            <?php }else{ ?>
                                                <a href="#" class="badge badge-info badges"><?= $pos->co_name; ?></a>
                                            <?php } ?>
                                        <?php } ?>
                                    </td>
                                    <td class="align-middle">
                                        <?php if($pos->field != 'Student Organization' && $pos->co_name == null) { ?>
                                            <!--SHOW ONLY IF NO CO IS ASSIGNED-->
                                            <?php if($AddClearanceFieldOfficers) { ?>
                                                <a href="#assign-officer" class="btn btn-primary btn-sm assign" data-toggle="modal" data-target="#assign-officer" data-position-id="<?php echo $pos->id; ?>"><i class="fas fa-user-tag"></i></a>
                                            <?php } ?>
                                        <?php } else if ($pos->field == 'Student Organization') { ?>
                                            <!--SHOW ONLY IF CLEARANCE FIELD IS STUDENT ORG-->
                                            <?php if($AddClearanceFieldOfficers || $EditClearanceFieldOfficers) { ?>
                                                <a class="btn btn-primary btn-sm assign" href="<?php echo base_url('Positions/OrganizationsOfficers/'.$pos->id)?>" ><i class="fas fa-user-tag"></i> </a>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <?php if($EditClearanceFieldOfficers) { ?>
                                                <!--SHOW ONLY IF THERE'S ALREADY CO ASSIGNED-->
                                                <a href="#edit-assigned-officer" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#edit-assigned-officer" data-pos-id="<?= $pos->co_pos_id; ?>" data-officer-id="<?= $pos->co_id ?>" id="change"><i class="fas fa-user-edit"></i> </a>
                                            <?php } ?>
                                        <?php } ?>

                                        <?php if($EditPositions) { ?>
                                            <a href="#edit-position" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#edit-position" data-position-id="<?php echo $pos->id; ?>" data-position-name="<?php echo $pos->name; ?>" data-field-id="<?php echo $pos->field_id; ?>" id="editpos"><i class="fas fa-pencil-alt"></i> </a>
                                        <?php } ?>
                                        <?php if($DeletePositions) { ?>
                                             <a class="btn btn-danger btn-sm" onclick="del(this)" data-href="Positions/deletePosition/<?php echo $pos->id; ?>" data-delete-name="<?php echo $pos->name ?>" id="deletepos"><i class="fas fa-trash"></i> </a>
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

    <!------ MODAL FOR ADDING POSITION ------>
    <div class="modal fade" id="add-position">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-color">
                    <h5 class="modal-title">Add Position</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="<?= base_url('Positions/newPosition'); ?>">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="form-group required">
                                <label for="PositionName">Position Name</label>
                                <input type="text" class="form-control" id="PositionName" name="posName" autocomplete="off" required>
                            </div>
                            <div class="form-group required">
                                <label for="ClearanceField">Clearance Field</label>
                                <select class="form-control select2bs4-search" title="Choose" name="posField" required>
                                    <option></option>
                                    <?php foreach($ClearanceFields as $row) { ?>
                                        <option value="<?= $row['id'] ?>"><?= $row['field_name']; ?></option>
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


    <!------ MODAL FOR ASSIGNING OFFICER ------>
    <div class="modal fade" id="assign-officer">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-color">
                    <h5 class="modal-title">Assign Clearance Officer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="<?= base_url('Positions/assignOfficerToPosition'); ?>">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <input type="text" name="posID" value="" hidden>
                            <div class="form-group required">
                                <label for="ClearanceOfficer">Clearance Officer</label>
                                <select class="form-control select2bs4-search" title="Choose" name="clearanceofficer" required>
                                    <option></option>
                                    <?php foreach($ClearanceOfficers as $row) { ?>
                                        <option value="<?= $row->id ?>"><?= $row->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Assign</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!------ MODAL FOR EDITING POSITION ------>
    <div class="modal fade" id="edit-position">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-color">
                    <h5 class="modal-title">Edit Position</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="<?= base_url('Positions/editPosition'); ?>">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="form-group required">
                                <input type="text" name="posID" value="" hidden>
                            </div>
                            <div class="form-group required">
                                <label for="PositionName">Position Name</label>
                                <input type="text" class="form-control" name="posName" value="" required>
                            </div>
                            <div class="form-group required">
                                <label for="ClearanceField">Clearance Field</label>
                                <select class="custom-select" name="posFieldID" value="" required>
                                    <option></option>
                                     <?php foreach($ClearanceFields as $row) { ?>
                                        <option value="<?= $row['id'] ?>"><?= $row['field_name']; ?></option>
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

    <!------ MODAL FOR EDITING ASSIGNED OFFICER ------>
    <div class="modal fade" id="edit-assigned-officer">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-color">
                    <h5 class="modal-title">Change Assigned Officer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="<?= base_url('Positions/editPositionOfficer'); ?>">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="form-group">
                                <input type="text" name="coPosID" value="" hidden>
                                <label for="ClearanceOfficer">Clearance Officer</label>
                                <select class="form-control select2bs4-search" title="Choose" name="clearanceofficer">
                                    <option></option>
                                    <?php foreach($ClearanceOfficers as $row) { ?>
                                        <option value="<?= $row->id ?>"><?= $row->name ?></option>
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
            $('#ListOfPositionsTable').DataTable();
        } );
    </script>
    <script>
        tippy('#editpos', {
            content: 'Edit Position',
            followCursor: true,
        });
        tippy('#deletepos', {
            content: 'Delete Position',
            followCursor: true,
        });
        tippy('.assign', {
            content: 'Assign Officer',
            followCursor: true,
        });
        tippy('#change', {
            content: 'Change Officer',
            followCursor: true,
        });

    </script>
<?= $this->endSection(); ?>
