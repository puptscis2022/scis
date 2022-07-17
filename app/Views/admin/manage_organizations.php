<?= $this->extend("layouts/user_account_layout"); ?>

<?= $this->section("sidebar_menu"); ?>
    <?= $this->include('test_menu') ?>
<?= $this->endSection(); ?>

<?= $this->section("page-title"); ?>
    Manage Organizations
<?= $this->endSection(); ?>

<?= $this->section("breadcrumb"); ?>
    <li class="breadcrumb-item"><a href="#"><i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li class="breadcrumb-item active">Manage Organizations</li>
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
                        Organizations
                    </h5>
                    <?php if($AddStudentOrganizations) { ?>
                        <a class="ml-auto btn btn-primary btn-sm d-inline float-right" data-toggle="modal" data-target="#add-organization"><i class="fas fa-plus mr-1"></i> Add Organization</a>
                    <?php } ?>
                </div>
                <div class="card-body">
                    <table id="ListOfOrgsTable" class="table dt-responsive display compact" style="width:100%">
                        <thead>
                            <tr>
                                <th width="10%">No.</th>
                                <th width="60%">Organization Name</th>
                                <th width="20%">Organization Type</th>
                                <th width="10%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $count = 1;
                                foreach($Organizations as $row)
                                {
                            ?>
                                <tr>
                                    <td class="align-middle"><?= $count; ?></td>
                                    <td class="align-middle"><?= $row->name; ?></td>
                                    <td class="align-middle"><?= $row->organization_type; ?></td>
                                    <td class="align-middle">
                                        <?php if($EditStudentOrganizations) { ?>
                                            <a href="#edit-organization" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#edit-organization" data-org-id="<?php echo $row->id; ?>" data-org-name="<?php echo $row->name; ?>" data-type-id="<?php echo $row->type_id; ?>" id="Edit"><i class="fas fa-pencil-alt"></i></a>
                                        <?php } ?>
                                        <?php if($DeleteStudentOrganizations) { ?>
                                            <!-- <a href="" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</a> -->
                                            <a class="btn btn-danger btn-sm" onclick="del(this)" data-href="<?php echo base_url() ?>/Organizations/deleteOrganization/<?php echo $row->id; ?>" data-delete-name="<?php echo $row->name ?>" id="delete"><i class="fas fa-trash"></i></a>
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

    <!------ MODAL FOR ADDING ORGANIZATION ------>
    <div class="modal fade" id="add-organization">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-color">
                    <h5 class="modal-title">Add Organization</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="<?= base_url('Organizations/newOrganization'); ?>">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="form-group required">
                                <label for="OrgName">Organization Name</label>
                                <input type="text" class="form-control" id="OrgName" name="orgName" autocomplete="off" required>
                            </div>
                            <div class="form-group required">
                                <label for="OrgType">Organization Type</label>
                                <select class="form-control select2bs4" name="orgType" required>
                                    <option></option>
                                    <?php foreach($OrganizationTypes as $row) { ?>
                                        <option value="<?= $row['id'] ?>"><?= $row['type']; ?></option>
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

    <!------ MODAL FOR EDITING ORGANIZATION ------>
    <div class="modal fade" id="edit-organization">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-color">
                    <h5 class="modal-title">Edit Organization</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="<?= base_url('Organizations/editOrganization'); ?>">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="form-group required">
                                <input type="text" class="form-control" id="OrganizationID" name="orgID" value="" hidden required>
                                <label for="OrganizationName">Organization Name</label>
                                <input type="text" class="form-control" id="OrganizationName" name="orgName" value="" autocomplete="off" required>
                            </div>
                            <div class="form-group required">
                                <label for="OrgType">Organization Type</label>
                                <select class="custom-select" name="orgTypeID" required>
                                    <?php foreach($OrganizationTypes as $row) { ?>
                                        <option value="<?= $row['id'] ?>"><?= $row['type']; ?></option>
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
            $('#ListOfOrgsTable').DataTable();
        } );
    </script>
<?= $this->endSection(); ?>


