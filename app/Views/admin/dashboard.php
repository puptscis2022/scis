<?= $this->extend("layouts/user_account_layout"); ?>

<?= $this->section("sidebar_menu"); ?>
    <?= $this->include('admin/menu') ?>
<?= $this->endSection(); ?>

<?= $this->section("page-title"); ?>
    Dashboard
<?= $this->endSection(); ?>

<?= $this->section("breadcrumb"); ?>
    <li class="breadcrumb-item active"><i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</li>
<?= $this->endSection(); ?>

<?= $this->section("content"); ?>

<?php if(session()->get('superAdmin_access')) { ?>
    <h1 align="text-center">Welcome Crewmates</h1>
<?php } else { ?>

    <!------------------- ALERT ERROR MESSAGE ----------------------------->
    <?php if(session()->get('err_message')):
        $message = session()->get('err_message');
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
    <!------------------------------------------------------------------->
    <div class="row gx-5">
        <div class="col-lg-4 col-md-6">
            <div class="card" style="background-color:#f5f5f5;!important;">
                <div class="card-body py-3 px-4">
                    <div class="row" style="color:#800000;!important;">
                        <div class="col-3 my-auto">
                            <i class="fa fa-users fa-3x"></i>
                        </div>
                        <div class="col-9 text-right">
                            <h1 class="font-weight-bold"><?= $enrolled; ?></h1>
                            <h6 class="font-weight-bold">Enrolled Students</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card" style="background-color:#f5f5f5;!important;">
                <div class="card-body py-3 px-4">
                    <div class="row" style="color:#800000;!important;">
                        <div class="col-3 my-auto">
                            <i class="fa fa-user-friends fa-3x"></i>
                        </div>
                        <div class="col-9 text-right">
                            <h1 class="font-weight-bold"><?= $regulars; ?></h1>
                            <h6 class="font-weight-bold">Regular Students</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card" style="background-color:#f5f5f5;!important;">
                <div class="card-body py-3 px-4">
                    <div class="row" style="color:#800000;!important;">
                        <div class="col-3 my-auto">
                            <i class="fa fa-user-friends fa-3x"></i>
                        </div>
                        <div class="col-9 text-right">
                            <h1 class="font-weight-bold"><?= $irregulars; ?></h1>
                            <h6 class="font-weight-bold">Irregular Students</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if($clearance_period_status) { ?>

        <!------------------ SHOWS ONLY IF THERE'S AN ONGOING CLEARANCE PERIOD --------------------->
        <div class="row">
            <div class="col-md-12">
                <div class="card card-outline">
                    <div class="card-header d-flex align-items-center" style="color:#800000;">
                        <h4 class="card-title font-weight-bold m-0">
                            <i class="fas fa-bullhorn mr-2"></i>
                            Ongoing Clearance Period
                        </h4>
                        <button class="ml-auto btn btn-sm btn-secondary" data-toggle="modal" data-target="#extend-due" data-due-date="<?= $dueDate ?>" data-clearance-period="<?= $periodID ?>"><i class="fas fa-clock mr-2"></i>Extend Due Date</button>
                    </div>

                    <div class="alert alert-light p-0 mb-0" role="alert">
                        <div class="row justify-content-center">
                            <div class="<?php if($clearanceType == "Semestral")  { ?> col-md-3 <?php } else { ?> col-md-4  col-sm-4<?php } ?>">
                                <div class="box-content p-4">
                                    <div class="row justify-content-center">
                                        <i class="fas fa-university" style="font-size: 25px; color: #800000;"></i>
                                    </div>
                                    <div class="row justify-content-center">
                                        <p class="mb-0 text-sm"> School Year</p>
                                    </div>
                                    <div class="row justify-content-center">
                                        <h5 class="font-weight-bold"><?= $school_year; ?></h5>
                                    </div>
                                </div>
                            </div>

                            <div class="<?php if($clearanceType == "Semestral")  { ?> col-md-3 <?php } else { ?> col-md-4 col-sm-4 <?php } ?> justify-content-center" >
                                <div class="box-content p-4" style="background-color: #F5F5F5;">
                                    <div class="row justify-content-center">
                                        <i class="fas fa-file" style="font-size: 25px; color: #800000;"></i>
                                    </div>
                                    <div class="row justify-content-center">
                                        <p class="mb-0 text-sm"> Clearance Type</p>
                                    </div>
                                    <div class="row justify-content-center">
                                        <h5 class="font-weight-bold"><?= $clearanceType; ?></h5>
                                    </div>
                                </div>
                            </div>
                            <?php if($clearanceType == "Semestral")  { ?>
                                <div class="col-md-3 col-sm-4">
                                    <div class="box-content p-4">
                                        <div class="row justify-content-center">
                                            <i class="fas fa-clock" style="font-size: 25px; color: #800000;"></i>
                                        </div>
                                        <div class="row justify-content-center">
                                            <p class="mb-0 text-sm"> Semester</p>
                                        </div>
                                        <div class="row justify-content-center">
                                            <h5 class="font-weight-bold"><?= $semester; ?></h5>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="<?php if($clearanceType == "Semestral")  { ?> col-md-3 <?php } else { ?> col-md-4  col-sm-4<?php } ?>">
                                <div class="box-content p-4" style="background-color: #F5F5F5;">
                                    <div class="row justify-content-center">
                                        <i class="fas fa-calendar" style="font-size: 25px; color: #800000;"></i>
                                    </div>
                                    <div class="row justify-content-center">
                                        <p class="mb-0 text-sm"> Due Date</p>
                                    </div>
                                    <div class="row justify-content-center">
                                        <h5 class="font-weight-bold"><?= $dueDate; ?></h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center" style="color:#800000;">
                        <h4 class="card-title font-weight-bold m-0">
                            <i class="fas fa-file-signature mr-2"></i>
                            Clearance Fields
                        </h4>
                        <button onclick="window.location.href='<?= base_url("ClearanceManagement/CreateReportPage");?>'" class="ml-auto btn btn-sm btn-secondary"><i class="fas fa-print mr-2"></i> Generate Report</button>
                    </div>

                    <div class="card-body p-0 mb-0">
                        <div class="row">
                            <div class="col-md-12">
                                <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                                    <?php
                                    $tabCount = 1;
                                    foreach($coFields as $field)
                                    { ?>
                                        <li class="nav-item">
                                            <a class="font-weight-bold nav-link <?php if($tabCount == 1) { ?>active<?php } ?>" id="clearance-field-tab-1" data-toggle="pill" href="#clearance-field-<?php echo $tabCount; ?>" role="tab" aria-controls="clearance-field-<?php echo $tabCount; ?>" <?php if($tabCount == 1) { ?>aria-selected="true"<?php } else { ?> aria-selected="false" <?php } ?> ><?php echo $field['field_name'] ?></a>
                                        </li>
                                        <?php
                                        $tabCount++;
                                    } ?>
                                </ul>
                            </div>
                        </div>

                        <div class="row mt-2 px-3">
                            <div class="col-md-12">
                                <div class="tab-content" id="custom-tabs-four-tabContent">
                                    <?php
                                    $tabCount = 1;
                                    foreach($coFields as $field)
                                    { ?>
                                        <div class="p-3 tab-pane fade <?php if($tabCount == 1){ ?>show active <?php } ?>" id="clearance-field-<?php echo $tabCount; ?>" role="tabpanel" aria-labelledby="clearance-field-<?php echo $tabCount; ?>-tab">
                                            <?php if($field['clearedCount'] == 0 && $field['unclearedCount'] == 0) { ?>
                                                <div class="row justify-content-center ">
                                                    <i class="fas fa-exclamation-circle" style="font-size: 65px; color: #800000;"></i>
                                                </div>
                                                <div class="row mt-3 justify-content-center">
                                                    <h5 class="font-weight-bold mb-0 text-center">This clearance field is not currently active for this clearance period</h5>
                                                </div>
                                            <?php }else{ ?>
                                                <div class="row">

                                                    <div class="col-lg-4 col-md-6">
                                                        <div class="card" style="background-color:#800000;!important;">
                                                            <div class="card-body p-4">
                                                                <div class="row text-white">
                                                                    <div class="col-3 my-auto">
                                                                        <i class="fa fa-user-check fa-3x"></i>
                                                                    </div>
                                                                    <div class="col-9 text-right">
                                                                        <h1 class="font-weight-bold"><?= $field['clearedCount']; ?></h1>
                                                                        <h6 class="font-weight-bold">Cleared Students</h6>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <a href="<?= base_url('ClearanceManagement/deficiencies/'.$field['field_id'].'/cleared') ?>">
                                                                <div class="small-box-footer bg-light py-1 px-3">
                                                                    <span class="float-right font-weight-bold" style="color: #800000;"><i class="ml-2 fa fa-arrow-circle-right"></i></span>
                                                                    <span class="float-left font-weight-bold" style="color: #800000;">View Details</span>
                                                                    <div class="clearfix"></div>
                                                                </div>
                                                            </a>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6">
                                                        <div class="card" style="background-color:#800000;!important;">
                                                            <div class="card-body p-4">
                                                                <div class="row text-white">
                                                                    <div class="col-3 my-auto">
                                                                        <i class="fa fa-user-times fa-3x"></i>
                                                                    </div>
                                                                    <div class="col-9 text-right">
                                                                        <h1 class="font-weight-bold"><?= $field['unclearedCount']; ?></h1>
                                                                        <h6 class="font-weight-bold">Uncleared Students</h6>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <a href="<?= base_url('ClearanceManagement/deficiencies/'.$field['field_id'].'/uncleared') ?>">
                                                                <div class="small-box-footer bg-light py-1 px-3">
                                                                    <span class="float-right font-weight-bold" style="color: #800000;"><i class="ml-2 fa fa-arrow-circle-right"></i></span>
                                                                    <span class="float-left font-weight-bold" style="color: #800000;">View Details</span>
                                                                    <div class="clearfix"></div>
                                                                </div>
                                                            </a>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4 col-md-6">
                                                        <div class="card" style="background-color:#800000;!important;">
                                                            <div class="card-body p-4">
                                                                <div class="row text-white">
                                                                    <div class="col-3 my-auto">
                                                                        <i class="fa fa-file-import fa-3x"></i>
                                                                    </div>
                                                                    <div class="col-9 text-right">
                                                                        <h1 class="font-weight-bold"><?= $field['subCount']; ?></h1>
                                                                        <h6 class="font-weight-bold">Submissions</h6>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <a href="<?= base_url('Submissions/List/'.$field['field_id'].'') ?>">
                                                                <div class="small-box-footer bg-light py-1 px-3">
                                                                    <span class="float-right font-weight-bold" style="color: #800000;"><i class="ml-2 fa fa-arrow-circle-right"></i></span>
                                                                    <span class="float-left font-weight-bold" style="color: #800000;">View Details</span>
                                                                    <div class="clearfix"></div>
                                                                </div>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <?php
                                        $tabCount++;
                                    } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-----------------------SHOWS ONLY IF THERE'S NO ONGOING CLEARANCE PERIOD----------------------->
    <?php } else { ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card alert alert-dismissible p-5">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <div class="row justify-content-center">
                        <i class="fas fa-bullhorn" style="font-size: 65px; color: #800000; transform: rotate(-20deg);"></i>
                    </div>
                    <div class="row mt-3 justify-content-center">
                        <h5 class="font-weight-bold mb-0 text-center">There is no ongoing clearance period at the moment</h5>
                    </div>
                    <div class="row justify-content-center">
                        <?php if(!$clearance_message) { ?>
                            <p class="text-center font-weight-light mb-0">Click the button below to initiate a new clearance period</p>
                        <?php } else { ?>
                            <p class="text-center font-weight-light mb-0"><?= $clearance_message; ?></p>
                        <?php } ?>
                    </div>
                    <div class="row justify-content-center mb-0">
                        <?php if(!$incoming_clearance) { ?>
                            <button onclick="window.location.href='<?php echo base_url("ClearanceManagement/InitiateClearancePage")?>'" class="btn btn-sm btn-primary mt-3 mb-0">Initiate Clearance</button>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>

    <!-----------------------MODAL FOR EXTENDING DUE DATE----------------------->
    <div class="modal" id="extend-due" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-color">
                    <h5 class="modal-title">Extend Due Date</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="<?= base_url('ClearanceManagement/extendClearancePeriod') ?>">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="form-group required">
                                <label for="dueDate">Due Date</label>
                                <input type="text" name="pID" hidden>
                                <input type="date" class="form-control" name="currentDueDate" autocomplete="off" hidden>
                                <input type="date" class="form-control" id="dueDate" name="clearanceDueDate" autocomplete="off" required>
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
<?php } ?>
<?= $this->endSection(); ?>
