<?= $this->extend("layouts/user_account_layout"); ?>

<?= $this->section("sidebar_menu"); ?>
    <?= $this->include('test_menu'); ?>
<?= $this->endSection(); ?>

<?= $this->section("page-title"); ?>
    Submissions <?php echo ($gradClearance) ? '(Graduation Clearance)': '(Semestral Clearance)' ; ?> 
<?= $this->endSection(); ?>

<?= $this->section("breadcrumb"); ?>
    <li class="breadcrumb-item"><a href="#"><i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li class="breadcrumb-item active">Submissions</li>
<?= $this->endSection(); ?>

<?= $this->section("content"); ?>
    <div class="row">
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
        <div class="col-md-12">
            <!-- MAP & BOX PANE -->
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h5 class="font-weight-bold m-0" style="color:#800000;">
                        <i class="fas fa-info-circle mr-1"></i> <span class="mr-2"><?php echo $Office ?> </span> <span class="font-weight-normal badge badge-info badge-pill"><?php echo $Position ?></span>
                    </h5>

                    <div class="card-tools ml-auto pr-1">
                        <button type="button" class="btn btn-sm btn-outline-dark" data-toggle="collapse" data-target="#filterCollapse" aria-expanded="false">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="collapse" id="filterCollapse">
                    <div class="card-body">
                        <form class="form" method="POST" action="#" >
                            <div class="row d-flex justify-content-center">
                                <div class="<?php echo ($gradClearance) ? 'col-md-12': 'col-md-8'; ?> ">
                                    <label for="Course" style="font-size: 14px;">Course</label>
                                    <select class="form-control select2bs4" title="Select Course" name="course" id="Course">
                                        <option value="all" <?php echo ($courseFil == "all") ? "selected" : ""; ?>>All</option>
                                        <?php foreach($courses as $c) { ?>
                                            <option value="<?= $c['id'] ?>" <?php echo ($courseFil == $c['id']) ? "selected" : ""; ?> ><?= $c['course_name'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <?php if (!$gradClearance) {?>
                                    <div class="col-md-4">
                                        <label for="YearLevel" style="font-size: 14px;">Year</label>
                                        <select class="form-control select2bs4" title="Select Year Level" name="year" id="YearLevel">
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

                            </div><!-- /.d-md-flex -->
                            <div class="row d-flex justify-content-center mt-2">
                                <div class="col-md-8">
                                    <label for="Req" style="font-size: 14px;">Requirement</label>
                                    <select class="form-control select2bs4" title="Select Requirement" name="req" id="RSeq">
                                        <option value="all" <?php echo ($reqFil == "all") ? "selected" : ""; ?>>All</option>
                                        <?php foreach($Requirements as $req) { ?>
                                            <option value="<?= $req['id'] ?>" <?php echo ($reqFil == $req['id']) ? "selected" : ""; ?> ><?= $req['name'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label for="Status" style="font-size: 14px;">Status</label>
                                    <select class="form-control select2bs4" name="status" id="Status">
                                        <option value="0" <?php echo ($statusFil == "0") ? "selected" : ""; ?>> Pending </option>
                                        <option value="1" <?php echo ($statusFil == "1") ? "selected" : ""; ?>> Rejected </option>
                                        <option value="2" <?php echo ($statusFil == "2") ? "selected" : ""; ?>> Accepted </option>
                                    </select>
                                </div>

                                <div class="col-md-2 d-flex align-items-end">
                                    <input type="submit" name="clearFilter" class="btn btn-secondary mr-2 " value="Clear">
                                    <button type="submit" class="btn btn-success "> Apply</button>
                                </div>
                            </div>
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
                <div class="card-body">
                    <table id="submissionsTable" class="table dt-responsive display compact" style="width:100%">
                        <thead>
                            <tr>
                                <th width="12%">Date Submitted</th>
                                <th width="14%">Student Number</th>
                                <th width="20%">Name</th>
                                <th width="11%">Course</th>
                                <th width="11%">Year & Section</th>
                                <th width="20%">Requirement</th>
                                <th width="12%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($Submissions)) { ?>
                                <?php
                                    //$count = 1;
                                    foreach ($Submissions as $sub) {
                                ?>
                                    <tr>
                                        <td class="align-middle"><?php
                                            $submitted = strtotime($sub->submitted_date);
                                            echo date("M-d-Y",$submitted);  ?>
                                        </td>
                                        <td class="align-middle"><?= $sub->studNum ?></td>
                                        <td class="align-middle"><?= $sub->studName ?></td>
                                        <td class="align-middle"><?= $sub->course ?></td>
                                        <td class="align-middle"><?= $sub->year ?>-1</td>
                                        <td class="align-middle"><?= $sub->requirement ?></td>
                                        <td class="align-middle">
                                            <a id="viewSub" data-fancybox href="<?= base_url('uploads/'.$sub->file) ?>" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i></a>
                                            <?php if($ApproveReject) { ?>
                                                <?php if($sub->status == 0 || $sub->status == 1) { ?>
                                                    <button id="acceptSub" class="btn btn-success btn-sm" onclick="acceptSub(this)" data-href="<?= base_url('Submissions/approveSubmission/'.$sub->def_id."-".$sub->sub_id) ?>" data-sub-owner="<?= $sub->studName; ?>" data-sub-requirement="<?= $sub->requirement; ?>"> <i class="fas fa-check"></i> </button>
                                                <?php } if($sub->status == 0) {  ?>
                                                    <button id="rejectSub" class="btn btn-danger btn-sm" onclick="reject(this)" data-href="<?= base_url('Submissions/rejectSubmission/'.$sub->def_id) ?>" data-sub-owner="<?= $sub->studName; ?>" data-sub-requirement="<?= $sub->requirement; ?>"> <i class="fas fa-times"></i></button>
                                                <?php } ?>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

<?= $this->endSection(); ?>


<?= $this->section("script"); ?>
    <script>
        var status = $( "#Status option:selected" ).val();
        if (status == "0") {
            $(document).ready(function() {
                $('#submissionsTable').dataTable( {
                    "language": {
                        "emptyTable": "No Submitted File"
                    }
                } );
            });
        }else if (status == "1"){
            $(document).ready(function() {
                $('#submissionsTable').dataTable( {
                    "language": {
                        "emptyTable": "No Rejected Submission"
                    }
                } );
            });
        }
        else if (status == "2"){
            $(document).ready(function() {
                $('#submissionsTable').dataTable( {
                    "language": {
                        "emptyTable": "No Accepted Submission"
                    }
                } );
            });
        }
    </script>

    <script>
        function acceptSub(element){
            var name = element.dataset.subRequirement;
            var subOwner = element.dataset.subOwner;
            const href = element.dataset.href;

            Swal.fire({
                title: 'Confirm Action',
                text: 'Are you sure you want to accept ' + subOwner.trim() +'\'s submission for the requirement '+ '\'' + name.trim() + '\'?',
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

    <script>
        tippy('#viewSub', {
            content: 'View Submission',
            followCursor: true,
        });
        tippy('#acceptSub', {
            content: 'Accept Submission',
            followCursor: true,
        });
        tippy('#rejectSub', {
            content: 'Reject Submission',
            followCursor: true,
        });
    </script>
<?= $this->endSection(); ?>



