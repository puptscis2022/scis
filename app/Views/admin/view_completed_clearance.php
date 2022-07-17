<?= $this->extend("layouts/user_account_layout"); ?>

<?= $this->section("sidebar_menu"); ?>
    <?= $this->include('test_menu') ?>
<?= $this->endSection(); ?>

<?= $this->section("page-title"); ?>
    <?php echo ($gradClearance) ? "Graduation " : ""; ?> Clearance <?php echo (session()->get('director_access')) ? "Approval" : "Completion"; ?>
<?= $this->endSection(); ?>

<?= $this->section("breadcrumb"); ?>
    <li class="breadcrumb-item"><a href="/pupt_scis/"><i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li class="breadcrumb-item active"><?php echo ($gradClearance) ? "Graduation " : ""; ?>Clearance <?php echo (session()->get('director_access')) ? "Approval" : "Completion"; ?></li>
<?= $this->endSection(); ?>

<?= $this->section("content"); ?>
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-md-4">
                    <form method="get" action="<?= base_url('Clearance/FormsList/'.$period); ?>">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Filter by</span>
                            </div>
                            <select class="form-control select2bs4" onchange='if(this.value) { this.form.submit(); }' name="status">
                                <option value="0" <?php if(session()->get('statFil') == 0) { echo "selected"; }; ?> >Candidate for
                                    <?php echo (session()->get('director_access')) ? "Approval" : "Completion"; ?></option>
                                <option value="1" <?php if(session()->get('statFil') == 1) { echo "selected"; }; ?> >Completed</option>
                                <option value="2" <?php if(session()->get('statFil') == 2) { echo "selected"; }; ?> >Incomplete</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <table id="clearanceCompletionTable" class="table dt-responsive display compact" style="width:100%">
                        <thead>
                            <tr>
                                <th width="5%">No.</th>
                                <th width="15%">Student Number</th>
                                <th width="30%">Name</th>
                                <th width="15%">Course</th>
                                <th width="15%">Year & Section</th>
                                <th width="20%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $count = 1;
                                foreach($student_list as $stud)
                                {
                            ?>
                                <tr>
                                    <td class="align-middle"><?= $count++ ?></td>
                                    <td class="align-middle"><?= $stud->student_number ?></td>
                                    <td class="align-middle"><?= $stud->student_name ?></td>
                                    <td class="align-middle"><?= $stud->course ?></td>
                                    <td class="align-middle"><?= $stud->year ?> - 1 </td>
                                    <td class="align-middle">
                                        <?php if($ManageDeficiencies && session()->get('statFil') != 1) { ?>
                                            <a class="btn btn-secondary btn-sm" href="<?= base_url('Deficiencies/StudentDeficiencies/'.$stud->entry_info) ?>">Manage</a>
                                        <?php } ?>
                                        <a href="<?= base_url('Clearance/StudentStatus/'.$stud->form_id) ?>" class="btn btn-primary btn-sm" title="View">View</a>
                                        <?php if(session()->get('statFil') == 0) { ?>
                                            <?php if(session()->get('Director_access')) { ?>
                                                <a class="btn btn-success btn-sm" onclick="approve(this)" data-href="<?= base_url('Deficiencies/clearAllDeficiency/'.$stud->entry_id); ?>" data-stud-name="<?= $stud->student_name ?>" id="approve">Approve</a>
                                            <?php } else { ?>
                                                <a class="btn btn-success btn-sm" onclick="signclear(this)" data-href="<?= base_url('Clearance/SignCompletion/'.$stud->form_id); ?>" data-stud-name="<?= $stud->student_name ?>" id="clear">Clear</a>
                                            <?php } ?>
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

    <!------ MODAL FOR CONFIRMING SIGNING OF COMPLETION ------>
    <div class="modal fade" id="sign-completion">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body text-center py-5">
                    <i class="fas fa-user-check mb-4" style="font-size: 100px; color: #ADB5BD;"></i> <br>
                        <?php if(session()->get('Director_access')) { ?>
                            <h4 class="mb-4">Are you sure you want to Sign <b id="acceptName"></b>'s clearance ? ?</h4>
                        <?php } else { ?>
                            <h4 class="mb-4">Are you sure you want to mark <b id="acceptName"></b>'s clearance as complete? ?</h4>
                        <?php } ?>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <a type="button" class="btn btn-success btn-ok" >Yes</a>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection(); ?>

<?= $this->section("script"); ?>
    <script>
        $(document).ready(function() {
            $('#clearanceCompletionTable').DataTable();
        } );
    </script>
    <script>
        tippy('#approve', {
            content: 'Approve',
            followCursor: true,
        });

        tippy('#clear', {
            content: 'Clear',
            followCursor: true,
        });

        tippy('#managedef', {
            content: 'Manage Deficiency',
            followCursor: true,
        });
    </script>
    <script>
        function approve(element){
            var name = element.dataset.studName;
            const href = element.dataset.href;

            Swal.fire({
                title: 'Confirm Action',
                text: 'Are you sure you want to approve the completion of ' + name.trim() + '\'s clearance?',
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

        function signclear(element){
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



