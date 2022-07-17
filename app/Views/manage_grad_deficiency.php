<?= $this->extend("layouts/user_account_layout"); ?>

<?= $this->section("sidebar_menu"); ?>
<?= $this->include('test_menu'); ?>
<?= $this->endSection(); ?>

<?= $this->section("page-title"); ?>
Manage Subject Deficiency
<?= $this->endSection(); ?>

<?= $this->section("breadcrumb"); ?>
<li class="breadcrumb-item"><a href="#"><i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</a></li>
<li class="breadcrumb-item active">Manage Subject Deficiency</li>
<?= $this->endSection(); ?>

<?= $this->section("content"); ?>
<div class="row">
    <div class="col-md-12">
        <!-- MAP & BOX PANE -->
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h5 class="font-weight-bold m-0" style="color:#800000;">
                    <i class="fas fa-info-circle mr-1"></i> <span class="mr-2"><?php echo $subject_detail['code']." | ".$subject_detail['subject'] ?> </span>
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
                    <form class="form" method="POST">
                        <div class="row d-flex justify-content-center">
                            <div class="col-md-6">
                                <label for="Course" style="font-size: 14px;">Course</label>
                                <select class="form-control select2bs4" title="Select Course" name="course">

                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="YearLevel" style="font-size: 14px;">School Year</label>
                                <select class="form-control select2bs4" name="year">

                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="Status" style="font-size: 14px;">Status</label>
                                <select class="form-control select2bs4" name="status">

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
            <div class="card">
                <div class="card-header d-flex align-items-center" style="color:#800000;">
                    <h5 class="font-weight-bold m-0">
                        List of Students
                    </h5>
                    <?php if($TaggingDeficiencies) { ?>
                        <a class="ml-auto btn btn-sm btn-primary d-inline float-right" data-toggle="modal" data-target="#multiTag-subDeficiency"><i class="fas fa-tag mr-1"></i> Multi-Tag Deficiency</a>
                    <?php } ?>
                    <?php if($ClearStudents) { ?>
                        <form id="clearAllForm" method="POST" action="<?= base_url('RespectiveProfessors/MultiClearing'); ?>">
                            <select multiple title="Choose" name="SelectedStudent[]" hidden>
                                <?php foreach($rProf_entries as $ent) { ?>
                                    <option value="<?= $ent['id']; ?>" selected><?= $ent['student_name']; ?></option>
                                <?php } ?>
                            </select>
                            <button type="submit" id="clear-all" class="ml-2 btn btn-sm btn-success d-inline float-right"><i class="fas fa-check mr-1"></i> Clear All</button>
                        </form>
                    <?php } ?>
                </div>
                <div class="card-body">
                    <table id="ManageDeficiencyGradTable" class="table dt-responsive" style="width:100%">
                        <thead>
                        <tr>
                            <th width="5%">No.</th>
                            <th width="15%">Student Number</th>
                            <th width="20%">Name</th>
                            <th width="25%">Course</th>
                            <th width="10%">Status</th>
                            <th width="15%">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $count = 1;
                        foreach($rProf_entries as $row) {
                            ?>
                            <tr>
                                <td><?= $count++; ?></td>
                                <td><?= $row['student_number']; ?></td>
                                <td><?= $row['student_name']; ?></td>
                                <td><?= $row['course']; ?></td>
                                <td><?php if($row['status'] == 1) { ?>
                                        <span class="badge badge-pill badge-success badges">Cleared</span>
                                    <?php }else{ ?>
                                        <span class="badge badge-pill badge-danger badges">Uncleared</span>
                                    <?php } ?>
                                </td>
                                <td>
                                    <a id="tagDef" class="btn btn-primary btn-sm ml-auto" href="#tag-sub-deficiency" data-toggle="modal" data-target="#tag-sub-deficiency" data-ent-id="<?= $row['id'] ?>"><i class="fas fa-tag mr-1"></i> </a>
                                    <a id="manageDef" href="<?php echo base_url("RespectiveProfessors/View/".$row['id'].'-'.$row['sub_id'])?>" class="btn btn-secondary btn-sm" ><i class="fas fa-eye"></i></a>
                                    <a id="clearStud" onclick="clearStud(this)" data-href="#" data-stud-name="<?= $row['student_name']; ?>" class="btn btn-success btn-sm <?php echo ($row['status'] == 1) ? 'disabled' : '' ; ?>"><i class="fas fa-check"></i></a>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
    </div>
</div>


<!------ MODAL FOR TAGGING DEFICIENCY INDIVIDUALLY ------>
<div class="modal fade" id="tag-sub-deficiency">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-color">
                <h5 class="modal-title">Tag Deficiency</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="<?= base_url('RespectiveProfessors/TagDef'); ?>">
                <div class="modal-body">
                    <div class="container-fluid">
                        <input type="text" name="entID" hidden required>
                        <div class="form-group required">
                            <div class="form-group">
                                <label for="exampleFormControlTextarea1">Enter Deficiency</label>
                                <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" name="Deficiency"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-group">
                                <label for="exampleFormControlTextarea2">Note for Deficienty (if Any)</label>
                                <textarea class="form-control" id="exampleFormControlTextarea2" rows="3" name="Note"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-group">
                                <input type="checkbox" id="submittable" name="submittable" value="1">
                                <label for="submittable"> Can be submitted Online</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Tag</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!------ MODAL FOR MULTI TAGGING DEFICIENCY INDIVIDUALLY ------>
<div class="modal fade" id="multiTag-subDeficiency">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-color">
                <h5 class="modal-title">Tag Deficiency</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="<?= base_url('RespectiveProfessors/MultiTagging'); ?>">
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="form-group required">
                            <div class="form-group">
                                <select class="form-control selectpicker light-border" data-actions-box="true" data-live-search="true" multiple title="Choose" name="SelectedStudent[]">
                                    <?php foreach($rProf_entries as $ent) { ?>
                                        <option value="<?= $ent['id']; ?>"><?= $ent['student_name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group required">
                            <div class="form-group">
                                <label for="exampleFormControlTextarea1">Enter Deficiency</label>
                                <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" name="Deficiency"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-group">
                                <label for="exampleFormControlTextarea2">Note for Deficienty (if Any)</label>
                                <textarea class="form-control" id="exampleFormControlTextarea2" rows="3" name="Note"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-group">
                                <input type="checkbox" id="submittable" name="submittable" value="1">
                                <label for="submittable"> Can be submitted Online</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Tag</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>


<?= $this->section("script"); ?>
<script>
    $(document).ready(function() {
        $('#ManageDeficiencyGradTable').DataTable();
    } );
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

<script> //For Managing Courses Modal
    $('#tag-sub-deficiency').on('show.bs.modal', function(e) {
        var entry_id = $(e.relatedTarget).data('ent-id');

        $(e.currentTarget).find('input[name="entID"]').val(entry_id);
    });
</script>

<?= $this->endSection(); ?>
