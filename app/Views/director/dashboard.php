<?= $this->extend("layouts/user_account_layout"); ?>

<?= $this->section("sidebar_menu"); ?>
    <?= $this->include('director/menu') ?>
<?= $this->endSection(); ?>

<?= $this->section("page-title"); ?>
    Dashboard
<?= $this->endSection(); ?>

<?= $this->section("breadcrumb"); ?>
    <li class="breadcrumb-item active">Dashboard</li>
<?= $this->endSection(); ?>

<?= $this->section("content"); ?>
    <div class="row gx-5">
        <div class="col-lg-4 col-md-6">
            <div class="card">
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
            <div class="card">
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
            <div class="card">
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

                    <div class="row px-4 pt-4">
                        <div class="col-lg-4 col-md-6">
                            <div class="card" style="background-color:#800000;!important;">
                                <div class="card-body p-4">
                                    <div class="row text-white">
                                        <div class="col-3 my-auto">
                                            <i class="fa fa-user-check fa-3x"></i>
                                        </div>
                                        <div class="col-9 text-right">
                                            <h1 class="font-weight-bold"><?= $cleared ?></h1>
                                            <h6 class="font-weight-bold">Approved</h6>
                                        </div>
                                    </div>
                                </div>
                                <a href="<?= base_url('ClearanceManagement/ClearanceCompletion/'.$clearanceData['current_period'].'/1') ?>">
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
                                            <h1 class="font-weight-bold"><?= $completed ?></h1>
                                            <h6 class="font-weight-bold">Candidate for Approval</h6>
                                        </div>
                                    </div>
                                </div>
                                <a href="<?= base_url('ClearanceManagement/ClearanceCompletion/'.$clearanceData['current_period']) ?>">
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
                                            <h1 class="font-weight-bold"><?= $incomplete ?></h1>
                                            <h6 class="font-weight-bold"><p>Incomplete Clearances</p></h6>
                                        </div>
                                    </div>
                                </div>
                                <a href="<?= base_url('ClearanceManagement/ClearanceCompletion/'.$clearanceData['current_period'].'/2') ?>">
                                    <div class="small-box-footer bg-light py-1 px-3">
                                        <span class="float-right font-weight-bold" style="color: #800000;"><i class="ml-2 fa fa-arrow-circle-right"></i></span>
                                        <span class="float-left font-weight-bold" style="color: #800000;">View Details</span>
                                        <div class="clearfix"></div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



















    <!-- ------------------------------------------ -->
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
                            <p class="text-center font-weight-light mb-0">Please wait for the clearance period to be initiated.</p>
                        <?php } else { ?>
                            <p class="text-center font-weight-light mb-0"><?= $clearance_message; ?></p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>

<?= $this->endSection(); ?>
