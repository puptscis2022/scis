<?= $this->extend("layouts/user_account_layout"); ?>

<?= $this->section("sidebar_menu"); ?>
<?= $this->include('test_menu'); ?>
<?= $this->endSection(); ?>

<?= $this->section("page-title"); ?>
Graduation Clearance Application
<?= $this->endSection(); ?>

<?= $this->section("breadcrumb"); ?>
<li class="breadcrumb-item"><a href="#"><i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</a></li>
<li class="breadcrumb-item active">Graduation Clearance Application</li>
<?= $this->endSection(); ?>

<?= $this->section("content"); ?>
<div class="row">
    <div class="col-md-12">
        <!-- MAP & BOX PANE -->
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h5 class="font-weight-bold m-0" style="color:#800000;">
                    <i class="fas fa-info-circle mr-1"></i>
                    List of Applications
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
                                <label for="Status" style="font-size: 14px;">Status</label>
                                <select class="form-control select2bs4" name="status">

                                </select>
                            </div>

                            <div class="col-md-2">
                                <label for="Status" style="font-size: 14px;">School Year</label>
                                <select class="form-control select2bs4" name="status">

                                </select>
                            </div>

                            <div class="col-md-2 d-flex align-items-end">
                                <input type="submit" name="clearFilter" class="btn btn-secondary mr-2" value="Clear">
                                <button type="submit" class="btn btn-success">Apply</button>
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
            <div class="card-body">
                <table id="GradClearanceApplicationsTable" class="table dt-responsive display compact" style="width:100%">
                    <thead>
                    <tr>
                        <th width="5%">No.</th>
                        <th width="15%">Student Number</th>
                        <th width="25%">Name</th>
                        <th width="20%">Course</th>
                        <th width="15%">Status</th>
                        <th width="20%">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if(!empty($forms)) { ?>
                        <?php
                            $count = 1;
                            foreach ($forms as $forms) {
                        ?>
                        <tr>
                            <td class="align-middle"><?= $count ?></td>
                            <td class="align-middle"><?= $forms->student_id ?></td>
                            <td class="align-middle"><?= $forms->student_name ?></td>
                            <td class="align-middle"><?= $forms->course ?></td>
                            <td class="align-middle">
                                <?php if($forms->application_status == 0) { ?>
                                    <span class="badge badge-pill badge-warning badges">Pending</span>
                                <?php } if($forms->application_status == 1) {  ?>
                                    <span class="badge badge-pill badge-success badges">Approved</span>
                                <?php } if($forms->application_status == 2) {  ?>
                                    <span class="badge badge-pill badge-danger badges">Declined</span>
                                <?php } ?>
                            </td>
                            <td class="align-middle">
                                <a class="btn btn-primary btn-sm " href="<?= base_url('GraduationClearance/ViewApplication/'.$forms->grad_form_id) ?>"> View </a>
                                <a class="btn btn-success btn-sm" onclick="approveApp(this)" data-app-owner="<?= $forms->student_name; ?>" data-href="<?= base_url('GraduationClearance/Approve/'.$forms->grad_form_id) ?>"> Approve </a>
                                <a class="btn btn-danger btn-sm " onclick="decline(this)" data-href="<?= base_url('GraduationClearance/Decline/'.$forms->grad_form_id) ?>" data-app-owner="<?= $forms->student_name; ?>"> Decline </a>
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
    $(document).ready(function() {
        $('#GradClearanceApplicationsTable').DataTable();
    } );
</script>

<script>
    function approveApp(element){
        var name = element.dataset.appOwner;
        const href = element.dataset.href;

        Swal.fire({
            title: 'Confirm Action',
            text: 'Are you sure you want to approve ' + name.trim() +'\'s application for graduation clearance?',
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
    function decline(element){
        var name = element.dataset.appOwner;
        const shref = element.dataset.href;

        Swal.fire({
            title: 'Confirm Action',
            text: 'Are you sure you want to decline '+ name.trim() + '\'s application for graduation clearance?',
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
                    html: `<textarea id="reasonDecline" name="reasonDecline" rows="8" cols="45"></textarea>`,
                    confirmButtonText: 'Submit',
                    allowOutsideClick: false,
                    preConfirm: () => {
                        const reasonDecline = Swal.getPopup().querySelector('#reasonDecline').value
                        if (!reasonDecline) {
                            Swal.showValidationMessage(`Please enter the reason for declining the application`)
                        }
                        return { reasonDecline: reasonDecline}
                    }
                }).then((result2) => {
                    var rejectGradForm = document.createElement('form');
                    rejectGradForm.setAttribute("method", "post");
                    rejectGradForm.setAttribute("action", shref);

                    var reason = document.createElement("input");
                    reason.setAttribute("type", "hidden");
                    reason.setAttribute("name", "reason");
                    reason.setAttribute("value", reasonDecline.value);
                    rejectGradForm.appendChild(reason);

                    document.body.appendChild(rejectGradForm);
                    rejectGradForm.submit();
                })
            }
        })
    }
</script>

<?= $this->endSection(); ?>
