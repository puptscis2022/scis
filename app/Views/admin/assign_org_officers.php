<?= $this->extend("layouts/user_account_layout"); ?>

<?= $this->section("sidebar_menu"); ?>
    <?= $this->include('test_menu') ?>
<?= $this->endSection(); ?>

<?= $this->section("page-title"); ?>
    Student Organizations
<?= $this->endSection(); ?>

<?= $this->section("breadcrumb"); ?>
    <li class="breadcrumb-item"><a href="#"><i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li class="breadcrumb-item active">Student Organizations | <?= $PositionName ?>s</li>
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
                    <h5 class="font-weight-bold m-0 py-2">
                        <?= $PositionName ?>s
                    </h5>
                </div>
                <div class="card-body">
                    <table id="OrgPositionsTable" class="table dt-responsive nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th width="5%">No.</th>
                                <th width="50%">Organization</th>
                                <th width="30%">Assigned Officer</th>
                                <th width="15%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $count = 1;
                        foreach($Organizations as $row) {
                            ?>
                            <tr>
                                <td><?= $count++; ?></td>
                                <td>
                                    <?= $row['organization_name']; ?>
                                </td>
                                <td>
                                    <?php
                                    $Officer = '';
                                    $ClearanceOfficerPositionID;
                                    $OfficerID = '';
                                    $ClearanceOfficerOrgID;
                                    foreach($AssignedOfficers as $row2) {

                                        if($row['organization_name'] == $row2->org_name && $row2->pos_id == $PositionID)
                                        {
                                            $Officer = $row2->co_name;
                                            $ClearanceOfficerPositionID = $row2->co_pos_id;
                                            $ClearanceOfficerOrgID = $row2->co_org_id;
                                            $OfficerID = $row2->co_id;
                                        }
                                    }
                                    ?>

                                    <?php if($Officer != '') { ?>
                                        <a href="#" class="badge badge-info badges"><?= $Officer ?></a>
                                    <?php }else { ?>
                                        <span class="badge badge-danger badges">No Assigned Officer</span>
                                    <?php } ?>

                                </td>
                                <td>
                                    <?php if($Officer == '') { ?>
                                        <!---------SHOW IF NO CO IS ASSIGNED---------->
                                        <?php if($AddClearanceFieldOfficers) { ?>
                                            <a class="btn btn-primary btn-sm" data-toggle="modal" data-target="#assign-officer2" data-position-id="<?php echo $PositionID; ?>" data-org-id="<?php echo $row['id']; ?>" id="assign"><i class="fas fa-user-tag"></i></a>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <!------SHOW IF THERE'S ALREADY CO ASSIGNED------->
                                        <?php if($EditClearanceFieldOfficers) { ?>
                                            <a href="#edit-assigned-officer2" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#edit-assigned-officer2" data-pos-id="<?= $ClearanceOfficerPositionID; ?>" data-officer-id="<?= $OfficerID ?>" data-org-id="<?= $ClearanceOfficerOrgID ?>" id="change"><i class="fas fa-user-edit"></i></a>
                                        <?php } ?>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php ; } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <a href="<?php echo base_url("Positions")?>" class="btn btn-secondary"><i class="fas fa-arrow-circle-left mr-2"></i> Back</a>
        </div>
    </div>

    <!------ MODAL FOR ASSIGNING OFFICER ------>
    <div class="modal fade" id="assign-officer2">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-color">
                    <h5 class="modal-title">Assign Clearance Officer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="<?= base_url('Positions/assignOfficerToOrganizationPosition'); ?>">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="form-group">
                                <input type="text" name="retID" value="<?php echo $PositionID; ?>" hidden >
                                <input type="text" name="posID" hidden>
                                <input type="text" name="orgID" hidden>
                                <label for="ClearanceOfficer">Clearance Officer</label>
                                <select class="form-control select2bs4-search" title="Choose" name="clearanceofficer" required>
                                    <option></option>
                                    <?php
                                    foreach($ClearanceOfficers as $row1)
                                    {
                                        $available = 1;
                                        foreach($AssignedOfficers as $row2)
                                        {
                                            if($row1->id == $row2->co_id ) {
                                                $available = 0;
                                            }
                                        }
                                        ?>

                                        <?php if($available==1) { ?>
                                            <option value="<?= $row1->id ?>"><?= $row1->name ?></option>
                                        <?php } ?>
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

    <!------ MODAL FOR EDITING ASSIGNED OFFICER ------>
    <div class="modal fade" id="edit-assigned-officer2">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-color">
                    <h5 class="modal-title">Change Assigned Officer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="<?= base_url('Positions/editOrganizationPositionOfficer'); ?>">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="form-group">
                                <input type="text" name="retID" hidden value="<?php echo $PositionID; ?>">
                                <input type="text" name="coPosID" value="" hidden>
                                <input type="text" name="coOrgID" value="" hidden>
                                <label for="ClearanceOfficer">Clearance Officer</label>
                                <select class="form-control select2bs4-search" title="Choose" name="clearanceofficer">
                                    <option></option>
                                    <?php foreach($ClearanceOfficers as $row1) { ?>
                                        <option value="<?= $row1->id ?>"><?= $row1->name ?></option>
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
            $('#OrgPositionsTable').DataTable();
        } );
    </script>
    <script>
        tippy('#assign', {
            content: 'Assign Officer',
            followCursor: true,
        });
        tippy('#change', {
            content: 'Change Officer',
            followCursor: true,
        });

    </script>
<?= $this->endSection(); ?>


