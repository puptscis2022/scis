<?= $this->extend("layouts/user_account_layout"); ?>

<?= $this->section("sidebar_menu"); ?>
    <?= $this->include('test_menu') ?>
<?= $this->endSection(); ?>

<?= $this->section("page-title"); ?>
    Verify Registrations
<?= $this->endSection(); ?>

<?= $this->section("breadcrumb"); ?>
    <li class="breadcrumb-item"><a href="#"><i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li class="breadcrumb-item active">Verify Registrations</li>
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
                        Registrations
                    </h5>
                    <!--<a onclick="verifyall(this)" data-href="#" class="ml-auto btn btn-success btn-sm d-inline float-right"><i class="fas fa-user-check"></i> Approve All</a>-->

                </div>

                <div class="card-body">
                    <table id="unverifiedUsers" class="table dt-responsive display compact nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th width="10%">No.</th>
                                <th width="20%">Role</th>
                                <th width="50%">Name</th>
                                <th width="20%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $count = 1;
                                foreach($req_reg as $reg) {
                            ?>
                                <tr>
                                    <td><?= $count++ ?></td>
                                    <td><?= $reg->role ?></td>
                                    <td><?= $reg->name ?></td>
                                    <td>
                                        <?php if($AddUsers) { ?>
                                            <a onclick="verify(this)" class="btn-verify btn btn-success btn-sm" data-href="approveRegistration/<?= $reg->id ?>" data-reg-name="<?= $reg->name ?>" id="approve"> <i class="fas fa-check"></i></a>
                                        <?php } ?>
                                        <a href="viewRegistration/<?= $reg->id ?>" class="btn btn-secondary btn-sm" id="view"> <i class="fas fa-eye"></i></a>
                                        <?php if($DeleteRegistrations) { ?>
                                            <a onclick="reject(this)" class="btn btn-danger btn-sm" data-href="rejectRegistration/<?= $reg->id ?>" data-reg-name="<?= $reg->name ?>" id="reject"><i class="fas fa-trash"></i></a>
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
<?= $this->endSection(); ?>

<?= $this->section("script"); ?>
    <script>
        $(document).ready(function() {
            $('#unverifiedUsers').DataTable();
        } );
    </script>

    <script>
        function verifyall(element){
            //const href = element.dataset.href;

            Swal.fire({
                title: 'Confirm Action',
                text: 'Are you sure to approve all registration request?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28A745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes',
                reverseButtons: true

            }).then((result) => {
                if (result.isConfirmed) {
                    //document.location.href = href;
                }
            })
        }

        function verify(element){
            var name = element.dataset.regName;
            const href = element.dataset.href;

            Swal.fire({
                title: 'Confirm Action',
                text: 'Are you sure to approve registration request of '+ name.trim() + '?',
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

        function reject(element){
            var name = element.dataset.regName;
            const href = element.dataset.href;

            Swal.fire({
                title: 'Confirm Action',
                text: 'Are you sure you want to reject registration request of '+ name.trim() + '?',
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
        tippy('#approve', {
            content: 'Approve',
            followCursor: true,
        });
        tippy('#reject', {
            content: 'Reject',
            followCursor: true,
        });
    </script>
<?= $this->endSection(); ?>



