<?= $this->extend("layouts/user_account_layout"); ?>

<?= $this->section("sidebar_menu"); ?>
    <?= $this->include('test_menu') ?>
<?= $this->endSection(); ?>

<?= $this->section("page-title"); ?>
    <div class="d-inline-flex"><?= $fieldName ?> <h4 class="d-inline ml-3"><span class="font-weight-normal badge badge-info badge-pill"><?= $positionName ?></span></h4></div>
<?= $this->endSection(); ?>

<?= $this->section("breadcrumb"); ?>
    <li class="breadcrumb-item"><a href="#"><i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li class="breadcrumb-item">Clearance Form</li>
    <li class="breadcrumb-item active"><?= $fieldName ?></li>
<?= $this->endSection(); ?>

<?= $this->section("content"); ?>
    <div class="row">
        <div class="col-12">
            <?php if($preRequisites) { ?>
                <div class="card">
                    <div class="card-header d-flex align-items-center" style="color:#800000;">
                        <h5 class="font-weight-bold m-0">
                            Prerequisites
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="callout callout-info">
                            <h5 class="font-weight-bolder text-info"><i class="fas fa-info-circle"></i> Note:</h5>
                            Clearance to the following offices/departments are prerequisite for your clearance in <?= $fieldName ?>.
                        </div>
                        <table class="table narrow">
                            <thead>
                                <tr>
                                    <th scope="col" style="width:20%">No.</th>
                                    <th scope="col" style="width:30%">Clearance Fields</th>
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
                </div>
            <?php } ?>

            <div class="card">
                <div class="card-header d-flex align-items-center" style="color:#800000;">
                    <h5 class="font-weight-bold m-0">
                        Requirements
                    </h5>
                </div>
                <div class="card-body">
                    <?php if($entry_deficiencies) { ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col" style="width:5%">No.</th>
                                    <th scope="col" style="width:45%">Requirement</th>
                                    <th scope="col" style="width:30%">Status</th>
                                    <th scope="col" style="width:20%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $count = 1; 
                                    foreach($entry_deficiencies as $def) 
                                    {
                                ?>
                                    <tr>
                                        <td class="align-middle"><?= $count++ ?></td>
                                        <td class="align-middle"><?= $def->req_name ?></td>
                                        <td class="align-middle">
                                            <?php if($def->def_status == "1" && ($def->sub_type == 1 && empty($def->submission))) { ?>
                                                <span class="badge badge-danger badges">Rejected</span>
                                                <a href="#reason-rejection" data-toggle="modal" data-target="#reason-rejection" data-reason-rejection="<?= $def->reason ?>" class="d-block text-secondary text-sm align-middle mt-1"><i class="fas fa-info-circle mr-1"></i>View Details</a>

                                            <?php } else if($def->def_status == 0 || ($def->sub_type == 1 && empty($def->submission))) { ?>
                                                <span class="badge badge-warning badges">Pending</span>
                                            <?php } else if($def->def_status == "2") { ?>
                                                <span class="badge badge-success badges">Cleared</span>
                                            <?php } ?>
                                        </td>
                                        <td class="align-middle">
                                            <?php if($def->sub_type == 1 && $def->def_status != 2 && empty($def->submission)) { ?>
                                                <!--SHOW ONLY IF THE REQUIREMENT'S SUBMISSION TYPE IS ONLINE -->
                                                <?php if($SubmitRequirements) { ?>
                                                    <a href="#online-submission" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#online-submission" data-def-id="<?= $def->id ?>" data-def-nm="<?= $def->req_name ?>" data-file-type="<?= $def->file_type_name ?>" data-file-format="<?= $def->file_type_desc ?>" >Submit</a>
                                                <?php } ?>
                                            <?php } else if($def->sub_type == 0 && $def->def_status != 2) { ?>
                                                <!--SHOW ONLY IF THE REQUIREMENT'S SUBMISSION TYPE IS PERSONAL -->
                                                <a href="#personal-submission" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#personal-submission" data-def-ins = "<?= $def->ins ?>">Submit</a>
                                            <?php } else if($def->def_status == 2) { ?>
                                                <!--SHOW ONLY IF THE SUBMISSION OF REQUIREMENT IS SUCCESSFULLY UPLOADED/SUBMITTED -->
                                                <?php if(!empty($def->submission)) { ?>
                                                    <a data-fancybox class="btn btn-sm btn-primary" href="<?= base_url('uploads/'.$def->file_type_name.'s/'.$def->submission) ?>">View</a>
                                                <?php } ?>
                                            <?php } else if(!empty($def->submission)) { ?>
                                                <!--SHOW ONLY IF THE SUBMISSION OF REQUIREMENT IS SUCCESSFULLY UPLOADED/SUBMITTED -->
                                                <?php if($ViewSubmissions) { ?>
                                                    <a data-fancybox class="btn btn-sm btn-primary" href="<?= base_url('uploads/'.$def->file_type_name.'s/'.$def->submission) ?>">View</a>
                                                <?php } ?>
                                                <?php if($SubmitRequirements) { ?>
                                                    <a href="#online-submission" class="btn btn-sm btn-secondary" data-toggle="modal" data-target="#online-submission" data-def-id="<?= $def->id ?>" data-def-nm="<?= $def->req_name ?>" data-file-type="<?= $def->file_type_name ?>" data-file-format="<?= $def->file_type_desc ?>" title="Resubmit">Resubmit</a>
                                                <?php } ?>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    <?php } else { ?>
                        <div class="callout callout-success">
                            <h5 class="font-weight-bolder text-success"><i class="fas fa-info-circle"></i> You are cleared!</h5>
                            You have no deficiency tagged.
                        </div>
                    <?php } ?>
                </div>
                <div class="card-footer">
                    <input type="button" class="btn btn-secondary float-right" value="Back" onclick="history.back()">
                </div>
            </div>
            <!-- <a href="<?php //echo base_url('Clearance/Form/'.$pID)?>" class="btn btn-secondary mb-3"><i class="fas fa-arrow-circle-left mr-2"></i> Back</a> -->
        </div>
    </div>


    <!------ MODAL FOR ONLINE SUBMISSION ------>
    <div class="modal fade" id="online-submission" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form class="form" method="POST" action="<?= base_url('Submissions/submitRequirement'); ?>" enctype="multipart/form-data">
                    <div class="modal-header modal-header-color">
                        <h5 class="modal-title">Upload <b id="deficiencyName"></b></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <input type="text" name="defID" hidden>
                            <input type="text" name="format" hidden>
                            <input type="file" class="dropify" data-max-file-size="30M" data-allowed-file-extensions="pdf png jpg" name="fileReq">
                            <p class="mt-2 mb-0"><span class="text-danger">*</span>File must be in <b id="fileFormat"></b> file format. </p>
                            <p><span class="text-danger">*</span>Maximum file size is 30 MB.</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!------ MODAL FOR PERSONAL SUBMISSION ------>
    <div class="modal fade" id="personal-submission" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header modal-header-color">
                    <h5 class="modal-title">Submission Guideline</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <p><b>This requirement must be submitted personally.</b></p>
                           <p id="instructions"></p>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!------ MODAL - REASON FOR REJECTION ------>
    <div class="modal fade" id="reason-rejection">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header modal-header-color">
                    <h5 class="modal-title">Reason for Rejection</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <p id="reason"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
                </form>
            </div>
        </div>
    </div>

<?= $this->endSection(); ?>
