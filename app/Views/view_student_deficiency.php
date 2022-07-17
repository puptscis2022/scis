<?= $this->extend("layouts/user_account_layout"); ?>

<?= $this->section("sidebar_menu"); ?>
    <?= $this->include('test_menu'); ?>
<?= $this->endSection(); ?>

<?= $this->section("page-title"); ?>
    Manage Deficiency <?php //echo ($gradClearance) ? '(Graduation Clearance)': '(Semestral Clearance)' ; ?>
<?= $this->endSection(); ?>

<?= $this->section("breadcrumb"); ?>
    <li class="breadcrumb-item"><a href="#"><i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li class="breadcrumb-item active">Manage Deficiency</li>
<?= $this->endSection(); ?>

<?= $this->section("content"); ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center" style="color:#800000;">
                    <h5 class="font-weight-bold m-0">
                        <i class="fas fa-info-circle mr-1"></i> <?= $Office ?>
                    </h5>
                    <!-- Add Deficienies for Student -->
                    <?php if($TagDeficiencies) { ?>
                        <a class="btn btn-primary btn-sm ml-auto <?php if($approvedClearance) { ?> disabled <?php }; ?>" href="#tag-indiv-deficiency" data-toggle="modal" data-target="#tag-indiv-deficiency" data-ent-id="<?= $entryID ?>" ><i class="fas fa-tag mr-1"></i> Tag Deficiency</a>
                    <?php } ?>
                </div>
                <div class="card-body">
                    <h5 class="font-weight-bold">Student Information</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <tbody>
                            <tr>
                                <th width="10%" class="font-weight-bold">Name</th>
                                <td width="50%"><?= $student_info->last_name; ?><?= $student_info->suffix_name; ?>, <?= $student_info->first_name; ?> <?= $student_info->middle_name; ?></td>
                                <th width="15%" class="font-weight-bold">Student Number</th>
                                <td width="25%"><?= $student_info->student_number; ?></td>
                            </tr>
                            <tr>
                                <th width="10%" class="font-weight-bold">Course</th>
                                <td width="50%"><?= $student_info->course_name; ?></td>
                                <th width="15%" class="font-weight-bold">Year & Section</th>
                                <td width="25%"><?= $student_info->year_level; ?>-1</td>
                            </tr>
                            <tr>
                                <th width="10%" class="font-weight-bold">Student Type</th>
                                <td width="50%"><?= $student_info->type; ?></td>
                                <th width="15%" class="font-weight-bold">Contact Number</th>
                                <td width="25%"><?= $student_info->contact_no ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <?php if($preRequisites) { ?>
                        <h5 class="font-weight-bold mt-5">PreRequisites</h5>
                        <div class="callout callout-info">
                            <h5 class="font-weight-bolder text-info"><i class="fas fa-info-circle"></i> Note:</h5>
                            Clearance on the following offices/departments were set as prerequisite for the clearance in <?= $Office ?>.
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col" style="width:10%">No.</th>
                                        <th scope="col" style="width:40%">Clearance Field</th>
                                        <th scope="col" style="width:30%">Position</th>
                                        <th scope="col" style="width:20%">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $count = 1;
                                        foreach($preRequisites as $preReq)
                                        {
                                    ?>
                                        <tr>
                                            <td><?= $count++ ?></td>
                                            <td><?= $preReq->field_name ?></td>
                                            <td><?= $preReq->position_name ?></td>
                                            <td>
                                                <?php if($preReq->status  == 1) { ?>
                                                    <span class="badge badge-success badges">Cleared</span>
                                                 <?php } else { ?>
                                                    <span class="badge badge-danger badges">Pending</span>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>

                    <h5 class="font-weight-bold mt-5">List of Deficiency</h5>
                    <?php if($Deficiencies) { ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-responsive-lg">
                                <thead>
                                <tr>
                                    <th width="5%" scope="col">No.</th>
                                    <th width="45%" scope="col">Deficiency</th>
                                    <th width="25%" scope="col">Status</th>
                                    <?php //if(session()->get('approved')) { ?>
                                    <th width="25%" scope="col">Action</th>
                                    <?php //} ?>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $count = 1;
                                foreach($Deficiencies as $def) {
                                    ?>
                                    <tr>
                                        <td class="align-middle"><?= $count++; ?></td>
                                        <td class="align-middle"><?= $def->req_name; ?></td>
                                        <td class="align-middle">
                                            <?php if($def->def_status == 0) { ?>
                                                <span class="badge badge-danger badges">Uncleared</span>
                                            <?php } else if($def->def_status == 1){ ?>
                                                <span class="badge badge-warning badges">Submission Rejected</span>
                                            <?php } else if($def->def_status == 2){ ?>
                                                <span class="badge badge-success badges">Cleared</span>
                                            <?php } ?>
                                        </td>
                                        <?php //if(session()->get('approved')) { ?>
                                        <td class="align-middle">
                                            <?php if($EditDeficiencies) { ?>
                                                <?php if($def->def_status == 0 || $def->def_status == 1) { ?>
                                                    <a id="clear" class="btn btn-success btn-sm" onclick="clearReq(this)" data-href="../ClearDeficiency/<?php echo $def->def_id."-".$magic; ?>" data-req-name="<?php echo $def->req_name; ?>">Clear</a>
                                                <?php } else if($def->def_status == 2) {?>
                                                    <a id="unclear" class="btn btn-secondary btn-sm" onclick="unclear(this)" data-href="../UnclearDeficiency/<?php echo $def->def_id."-".$magic; ?>" data-req-name="<?php echo $def->req_name; ?>">Unclear</a>
                                                <?php } ?>
                                            <?php } ?>

                                            <?php if($RemoveDeficiencies) { ?>
                                                <a id="remove" class="btn btn-danger btn-sm" onclick="remove(this)" data-href="../RemoveDeficiency/<?php echo $def->def_id."-".$magic; ?>" data-delete-name="<?php echo $def->req_name; ?>">Remove</a>
                                            <?php } ?>
                                        </td>
                                        <?php //} ?>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } else { ?>
                        <div class="callout callout-success">
                            <h5 class="font-weight-bolder text-success"><i class="fas fa-info-circle"></i> This student is cleared!</h5>
                            This student has no deficiency tagged.
                        </div>
                    <?php } ?>


                </div>
                <div class="card-footer">
                    <form>
                        <input type="button" class="btn btn-secondary float-right" value="Back" onclick="history.back()">
                    </form>
                </div>
            </div>
            <!-- /.card -->

            <?php //if($period) { ?>
                <!-- <a href="<?php //echo base_url("ClearancePeriods/Records/".$period) ?>" class="btn btn-secondary mb-4"> <i class="fas fa-arrow-circle-left mr-2"></i> Back</a> -->
            <?php //} else { ?>                
                <!-- <a href="<?php //echo base_url("Deficiencies/List/".$field_id)?>" class="btn btn-secondary mb-4"> <i class="fas fa-arrow-circle-left mr-2"></i> Back</a> -->
            <?php //} ?>


        </div>
    </div>
    <?php session()->remove('approved'); ?>


    <!------ MODAL FOR TAGGING DEFICIENCY INDIVIDUALLY ------>
    <div class="modal fade" id="tag-indiv-deficiency">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-color">
                    <h5 class="modal-title">Tag Deficiency</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="<?= base_url('Deficiencies/SingleTagging'); ?>">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="form-group required">
                                <input type="text" name="entID" value="" hidden required>
                                <label for="Deficiency">Deficiency</label>
                                <select class="form-control selectpicker light-border" data-actions-box="true" data-live-search="true" multiple title="Choose" id="Deficiency" name="SelectedDeficiency[]" required>
                                    <!-- <option value="clear">Cleared</option> -->
                                    <?php foreach($Requirements as $req) { ?>
                                        <option value="<?= $req['id']; ?>"><?= $req['name']?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Apply</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?= $this->endSection(); ?>

<?= $this->section("script"); ?>

    <script>
        tippy('#clear', {
            content: 'Clear',
            followCursor: true,
        });
        tippy('#unclear', {
            content: 'Unclear',
            followCursor: true,
        });
        tippy('#remove', {
            content: 'Remove',
            followCursor: true,
        });
    </script>


    <script>
        function remove(element){
            var name = element.dataset.deleteName;
            const href = element.dataset.href;

            Swal.fire({
                title: 'Confirm Action',
                text: 'Are you sure you want to remove '+ name.trim() + '?',
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

    <script>
        function clearReq(element){
            var name = element.dataset.reqName;
            const href = element.dataset.href;

            Swal.fire({
                title: 'Confirm Action',
                text: 'Are you sure you want to clear '+ name.trim() + '?',
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

    <script>
        function unclear(element){
            var name = element.dataset.reqName;
            const href = element.dataset.href;

            Swal.fire({
                title: 'Confirm Action',
                text: 'Are you sure you want to unclear '+ name.trim() + '?',
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

