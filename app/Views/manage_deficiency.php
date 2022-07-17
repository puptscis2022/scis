<?= $this->extend("layouts/user_account_layout"); ?>

<?= $this->section("sidebar_menu"); ?>
        <?= $this->include('test_menu'); ?>
<?= $this->endSection(); ?>

<?= $this->section("page-title"); ?>
    Manage Deficiency <?php echo ($gradClearance) ? '(Graduation Clearance)': '(Semestral Clearance)' ; ?> 
<?= $this->endSection(); ?>

<?= $this->section("breadcrumb"); ?>
    <li class="breadcrumb-item"><a href="#"><i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li class="breadcrumb-item active">Manage Deficiency</li>
<?= $this->endSection(); ?>

<?= $this->section("content"); ?>
    <div class="row">
        <div class="col-md-12">
            <!-- MAP & BOX PANE -->
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h5 class="font-weight-bold m-0" style="color:#800000;">
                        <i class="fas fa-info-circle mr-1"></i> <span class="mr-2"><?php echo $Office ?> </span> <span class="font-weight-normal badge badge-info badge-pill"><?php echo $Position ?></span>
                    </h5>

                    <div class="card-tools ml-auto pr-2">
                        <button type="button" class="btn btn-sm btn-outline-dark" data-toggle="collapse" data-target="#filterCollapse" aria-expanded="false">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="collapse" id="filterCollapse">
                    <div class="card-body">
                        <form class="form" method="GET">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="Course" style="font-size: 14px;">Course</label>
                                    <select class="form-control select2bs4" title="Select Course" name="course">
                                        <option value="all" <?php echo ($courseFil == "all") ? "selected" : ""; ?>>All</option>
                                        <?php foreach($courses as $c) { ?>
                                            <option value="<?= $c['id'] ?>" <?php echo ($courseFil == $c['id']) ? "selected" : ""; ?> ><?= $c['course_name'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <?php if (!$gradClearance) {?>
                                <div class="col-md-2">
                                    <label for="YearLevel" style="font-size: 14px;">Year</label>
                                    <select class="form-control select2bs4" name="year">
                                        <option value="all" <?php echo ($yearFil == "all") ? "selected" : ""; ?>>All</option>
                                        <option value="1" <?php echo ($yearFil == "1") ? "selected" : ""; ?>>1st Year</option>
                                        <option value="2" <?php echo ($yearFil == "2") ? "selected" : ""; ?>>2nd Year</option>
                                        <option value="3" <?php echo ($yearFil == "3") ? "selected" : ""; ?>>3rd Year</option>
                                        <option value="4" <?php echo ($yearFil == "4") ? "selected" : ""; ?>>4th Year</option>
                                        <option value="5" <?php echo ($yearFil == "5") ? "selected" : ""; ?>>5th Year</option>
                                        <option value="x" <?php echo ($yearFil == "0") ? "selected" : ""; ?>>Graduate</option>
                                    </select>
                                </div>
                                <?php } ?>
                                <div class="<?php echo ($gradClearance) ? 'col-md-4': 'col-md-2' ; ?>">
                                    <label for="Status" style="font-size: 14px;">Status</label>
                                    <select class="form-control select2bs4" name="status">
                                        <option value="all" <?php echo ($statusFil == "all") ? "selected" : ""; ?>>All</option>
                                        <option value="1" <?php echo ($statusFil == "1") ? "selected" : ""; ?>>Cleared</option>
                                        <option value="x" <?php echo ($statusFil == "0") ? "selected" : ""; ?>>Uncleared</option>
                                    </select>
                                </div>

                                <div class="col-md-2 d-flex align-items-end">
                                    <input type="submit" name="clearFilter" class="btn btn-secondary mr-2 " value="Clear">
                                    <button type="submit" class="btn btn-success "> Apply</button>
                                </div>
                            </div><!-- /.d-md-flex -->
                        </form>
                    </div>
                    <!-- /.card-body -->
                </div>

            </div>
            <!-- /.card -->
        </div>
    </div>





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
            <?php if($clearance_check) { ?>
                <div class="card">
                    <div class="card-header d-flex align-items-center" style="color:#800000;">
                        <h5 class="font-weight-bold m-0">
                            List of Students
                        </h5>
                        <?php if($TaggingDeficiencies) { ?>
                            <a class="ml-auto btn btn-sm btn-primary d-inline float-right" data-toggle="modal" data-target="#tag-deficiency"><i class="fas fa-tag mr-1"></i> Multi-Tag Deficiency</a>
                        <?php } ?>
                        <?php if($ClearStudents) { ?>
                            <form id="clearAllForm" method="POST" action="<?= base_url('Deficiencies/MultipleTagging'); ?>">
                                <select multiple title="Choose" name="SelectedStudent[]" hidden>
                                    <?php foreach($Entries as $ent) { ?>
                                        <option value="<?= $ent['entry_id']; ?>" selected><?= $ent['student_name']; ?></option>
                                    <?php } ?>
                                </select>
                                <select multiple title="Choose" id="Deficiency" name="SelectedDeficiency[]" hidden>
                                    <option value="clear" selected>Cleared</option>
                                </select>
                                <button type="submit" id="clear-all" class="ml-2 btn btn-sm btn-success d-inline float-right"><i class="fas fa-check mr-1"></i> Clear All</button>
                            </form>
                        <?php } ?>
                    </div>
                    <div class="card-body">
                        <table id="ManageDeficiencyTable" class="table dt-responsive display compact" style="width:100%">
                            <thead>
                                <tr>
                                    <th width="5%">No.</th>
                                    <th width="15%">Student Number</th>
                                    <th width="25%">Name</th>
                                    <th width="15%">Course</th>
                                    <th width="15%">Year & Section</th>
                                    <th width="10%">Status</th>
                                    <!--<th width="10%">Deficiency</th>-->
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $count = 1;
                                foreach($Entries as $row) {
                                    ?>
                                    <tr>
                                        <td class="align-middle"><?= $count++; ?></td>
                                        <td class="align-middle"><?= $row['student_number']; ?></td>
                                        <td class="align-middle"><?= $row['student_name']; ?></td>
                                        <td class="align-middle"><?= $row['course']; ?></td>
                                        <td class="align-middle"><?= $row['year']; ?> - 1</td>
                                        <td class="align-middle"><?php if($row['status'] == 1) { ?>
                                                <span class="badge badge-pill badge-success badges">Cleared</span>
                                            <?php }else{ ?>
                                                <span class="badge badge-pill badge-danger badges">Uncleared</span>
                                            <?php } ?>
                                        </td>
                                        <!--<td><?php if($row['status'] == 1) { ?>
                                                Cleared
                                            <?php }else{ ?>
                                                <?php if(!empty($row['deficiencies'])) { ?>
                                                    <?php foreach($row['deficiencies'] as $def) { ?>
                                                        <?= $def->req_name; ?> | <?php echo ($def->def_status == 2) ? "Clear" : (($def->def_status == 1) ? "Rejected": "Missing");?>
                                                        <br>
                                                    <?php } ?>
                                                <?php }else { ?>
                                                    None
                                                <?php } ?>
                                            <?php } ?>
                                        </td>-->
                                        <td class="align-middle">
                                            <?php if($TaggingDeficiencies) { ?>
                                                <a id="tagDef" class="btn btn-primary btn-sm <?php if($row['approvedClearance']) { echo "disabled";} ?>" href="#tag-indiv-deficiency" data-toggle="modal" data-target="#tag-indiv-deficiency" data-ent-id="<?= $row['entry_id'] ?>" ><i class="fas fa-tag"></i> </a>
                                            <?php } ?>
                                            <a id="manageDef" href="<?php echo base_url("Deficiencies/StudentDeficiencies/".$row['entry_id'].'-'.$row['student_id'].'-'.$field_id)?>" class="btn btn-secondary btn-sm" onclick="<?php session()->setFlashData('approved',$row['approvedClearance']); ?>"><i class="fas fa-eye"></i></a>
                                            <?php if($row['status'] != 1) { ?>
                                                <?php if($ClearStudents) { ?>
                                                    <a id="clearStud" onclick="clearStud(this)" data-href="<?php echo base_url("Deficiencies/clearAllDeficiency/".$row['entry_id'])?>" data-stud-name="<?= $row['student_name']; ?>" class="btn btn-success btn-sm"><i class="fas fa-check"></i></a>
                                                <?php } ?>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <!------ MODAL FOR MULTIPLE TAGGING DEFICIENCY ------>
    <div class="modal fade" id="tag-deficiency">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-color">
                    <h5 class="modal-title">Multi-Tag Deficiency</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="<?= base_url('Deficiencies/MultipleTagging'); ?>">
                        <div class="modal-body">
                            <div class="container-fluid">
                                <div class="form-group required">
                                    <label for="Deficiency">Deficiency</label>
                                    <select class="form-control selectpicker light-border" data-actions-box="true" data-live-search="true" multiple title="Choose" id="Deficiency" name="SelectedDeficiency[]" required>
                                        <!-- <option value="clear">Cleared</option> -->
                                        <?php foreach($Requirements as $req) { ?>
                                            <option value="<?= $req['id']; ?>"><?= $req['name']?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group required">
                                    <label for="student">Student</label>
                                    <select class="form-control selectpicker light-border" data-actions-box="true" data-live-search="true" multiple title="Choose" id="student" name="SelectedStudent[]" required>
                                        <?php foreach($Entries as $ent) { ?>
                                            <option value="<?= $ent['entry_id']; ?>"><?= $ent['student_name']; ?></option>
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
        $(document).ready(function() {
            $('#ManageDeficiencyTable').DataTable();
        } );
    </script>

    <script>
        function setApproveStatus($stat)
        {
            '<%session()->setFlashData("approved".'.$stat.')';
        }
    </script>
    <script>
        tippy('#tagDef', {
            content: 'Tag Deficiency',
            followCursor: true,
        });
        tippy('#manageDef', {
            content: 'Manage Deficiency',
            followCursor: true,
        });
        tippy('#clearStud', {
            content: 'Clear Student',
            followCursor: true,
        });
    </script>
    <script>
        $(document).on('click', '#clear-all', function(e) {
            e.preventDefault();
            swal.fire({
                title: 'Confirm Action',
                text: 'Are you sure you want to clear all students?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28A745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes',
                reverseButtons: true
            }).then(function (result) {
                if (result.isConfirmed) {
                    $('#clearAllForm').submit();
                }
            });
        });

    </script>
    <script>
        function clearStud(element){
            var name = element.dataset.studName;
            const href = element.dataset.href;

            Swal.fire({
                title: 'Confirm Action',
                text: 'Are you sure you want to clear ' + name.trim() +'?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28A745',
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
