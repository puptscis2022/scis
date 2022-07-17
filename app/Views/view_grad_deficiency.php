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
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center" style="color:#800000;">
                <h5 class="font-weight-bold m-0">
                    <i class="fas fa-info-circle mr-1"></i> <?= $subName ?>
                </h5>
                <!-- Add Deficienies for Student -->
                <?php //if($TagDeficiencies) { ?>
                    <a class="btn btn-primary btn-sm ml-auto" href="#tag-sub-deficiency" data-toggle="modal" data-target="#tag-sub-deficiency" data-ent-id="<?= $entID ?>"><i class="fas fa-tag mr-1"></i> Tag Deficiency</a>
                <?php //} ?>
            </div>
            <div class="card-body">
                <h5 class="font-weight-bold">Student Information</h5>
                <table class="table table-bordered table-sm nowrap table-responsive-lg">
                    <tbody>
                    <tr>
                        <th width="10%" class="font-weight-bold">Name</th>
                        <td width="50%"><?= $studentInfo->student_name; ?></td>
                        <th width="15%" class="font-weight-bold">Student Number</th>
                        <td width="25%"><?= $studentInfo->student_number; ?></td>
                    </tr>
                    <tr>
                        <th width="10%" class="font-weight-bold">Course</th>
                        <td width="25%"><?= $studentInfo->course; ?></td>
                        <th width="15%" class="font-weight-bold">Year & Section</th>
                        <td width="25%"><?= $studentInfo->year; ?></td>
                    </tr>
                    <tr>
                        <th width="10%" class="font-weight-bold">Student Type</th>
                        <td width="50%"><?= $studentInfo->studType; ?></td>
                        <th width="15%" class="font-weight-bold">Contact Number</th>
                        <td width="25%"><?= $studentInfo->contact; ?></td>
                    </tr>
                    </tbody>
                </table>

                <h5 class="font-weight-bold mt-5">List of Requirements</h5>
                <?php if($list) { ?>
                    <table class="table table-bordered table-responsive-lg">
                        <thead>
                        <tr>
                            <th scope="col">No.</th>
                            <th scope="col">Requirement</th>
                            <th scope="col">Note</th>
                            <th scope="col">Status</th>
                            <th scope="col">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $count = 1;
                        foreach($list as $def) {
                            ?>
                            <tr>
                                <td class="align-middle" width="5%"><?= $count++; ?></td>
                                <td class="align-middle" width="25%"><?= $def['subject_requirement']; ?></td>
                                <td class="align-middle" width="35%"><?= $def['note']; ?></td>
                                <td class="align-middle" width="10%">
                                    <?php if($def['status'] == 0) { ?>
                                        <span class="badge badge-warning badges">Uncleared</span>
                                    <?php } else if($def['status'] == 1){ ?>
                                        <span class="badge badge-success badges">Cleared</span>
                                    <?php } else if($def['status'] == 2){ ?>
                                        <span class="badge badge-danger badges">Submission Rejected</span>
                                    <?php } ?>
                                </td>
                                <td class="align-middle" width="25%">
                                    <?php if($def['status'] == 0 || $def['status'] == 2) { ?>
                                        <a id="clear" class="btn btn-success btn-sm" onclick="clearReq(this)" data-href="<?= base_url("RespectiveProfessors/ClearReq/".$def['id']); ?>" data-req-name="<?php echo $def['subject_requirement']; ?>">Clear</a>
                                    <?php } else if($def['status'] == 1) { ?>
                                        <a id="unclear" class="btn btn-secondary btn-sm" onclick="unclear(this)" data-href="<?= base_url("RespectiveProfessors/UnclearReq/".$def['id']); ?>" data-req-name="<?php echo $def['subject_requirement']; ?>">Unclear</a>
                                    <?php } ?>
                                    <a id="remove" class="btn btn-secondary btn-sm" onclick="remove(this)" data-href="<?= base_url("RespectiveProfessors/DeleteReq/".$def['id']); ?>" data-delete-name="<?php echo $def['subject_requirement']; ?>">Remove</a>

                                    <?php if(!empty($def['submission'])) { ?>
                                        <a id="viewSub" data-fancybox class="btn btn-sm btn-primary" href="<?= base_url('uploads/SubjectSubmissions/'.$def['submission']) ?>">View</a>
                                        <button id="rejectSub" class="btn btn-danger btn-sm" onclick="reject(this)" data-href="<?= base_url('SubjectSubmissions/rejectSubmission/'.$def['id']) ?>" data-sub-owner="<?= $studentInfo->student_name; ?>" data-sub-requirement="<?= $def['subject_requirement']; ?>">Reject</button>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <div class="callout callout-success">
                        <h5 class="font-weight-bolder text-success"><i class="fas fa-info-circle"></i> This student is cleared!</h5>
                        This student has no deficiency tagged.
                    </div>
                <?php } ?>
            </div>
            <div class="card-footer">
                <input type="button" class="btn btn-secondary float-right" value="Back" onclick="history.back()">
            </div>
        </div>
        <!-- /.card -->
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
    tippy('#rejectSub', {
        content: 'Reject Submission',
        followCursor: true,
    });
    tippy('#viewSub', {
        content: 'View Submission',
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

<script> //For Managing Courses Modal
    $('#tag-sub-deficiency').on('show.bs.modal', function(e) {
        var entry_id = $(e.relatedTarget).data('ent-id');

        $(e.currentTarget).find('input[name="entID"]').val(entry_id);
    });
</script>

<script>
        function reject(element){
            var name = element.dataset.subOwner;
            var req = element.dataset.subRequirement;
            const shref = element.dataset.href;

            Swal.fire({
                title: 'Confirm Action',
                text: 'Are you sure you want to reject '+ name.trim() + '\'s submission for ' + req.trim() + '?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes',
                reverseButtons: true

            }).then((result) => {
                if (result.isConfirmed) {
                    swal.fire({
                        title: 'Reason for Rejection',
                        html: `<textarea id="reasonReject" name="reasonReject" rows="8" cols="45"></textarea>`,
                        confirmButtonText: 'Submit',
                        allowOutsideClick: false,
                        preConfirm: () => {
                            const reasonReject = Swal.getPopup().querySelector('#reasonReject').value
                            if (!reasonReject) {
                                Swal.showValidationMessage(`Please enter reason for rejection`)
                            }
                            return { reasonReject: reasonReject}
                        }
                    }).then((result2) => {
                        var rejectForm = document.createElement('form');
                        rejectForm.setAttribute("method", "post");
                        rejectForm.setAttribute("action", shref);

                        var post = document.createElement("input");
                        post.setAttribute("type", "hidden");
                        post.setAttribute("name", "reason");
                        post.setAttribute("value", reasonReject.value);
                        rejectForm.appendChild(post);

                        document.body.appendChild(rejectForm);
                        rejectForm.submit();
                    })
                }
            })
        }
    </script>

<?= $this->endSection(); ?>

