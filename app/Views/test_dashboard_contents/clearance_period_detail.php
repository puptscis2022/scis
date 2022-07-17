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
                        <?php if($ExtendClearancePeriod) { ?>
                            <button class="ml-auto btn btn-sm btn-secondary" data-toggle="modal" data-target="#extend-due" data-due-date="<?= $dueDate ?>" data-clearance-period="<?= $periodID ?>"><i class="fas fa-clock mr-2"></i>Extend Due Date</button>
                        <?php } ?>
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

    <?php if($view_clearanceForms && !session()->get("Student_access")) { ?>
        <?= $this->include('test_dashboard_contents/clearance_forms_stat') ?>
    <?php } ?>

    <?php if($view_clearanceEntries && $view_clearanceForms && session()->get("Student_access")) { ?>
        <?= $this->include('test_dashboard_contents/student_clearance_stat') ?>
    <?php } ?>

    <?php if($view_clearanceEntries && (!session()->get("Student_access") || session()->get("ClearanceOfficer_access")) ) { ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center" style="color:#800000;">
                        <h4 class="card-title font-weight-bold m-0">
                            <i class="fas fa-chart-bar mr-2"></i>
                            Clearance Fields Status
                        </h4>
                        <button onclick="window.location.href='<?= base_url("GenerateReports");?>'" class="ml-auto btn btn-sm btn-secondary"><i class="fas fa-print mr-2"></i> Generate Report</button>
                    </div>

                    <div class="card-body">

                        <div class="row">
                            <div class="col-md-12">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item dropdown" style="max-width: 100%;" id="dropdown-list">
                                        <a class="nav-link dropdown-toggle active text-truncate" id="dropdowntext" data-toggle="dropdown" href="#" role="button" aria-expanded="false" style="border-top:3px solid #800000;!important;"></a>
                                        <div class="dropdown-menu dropdown-menu-left" id="field-list">
                                    <?php
                                    $tabCount = 1;
                                    foreach($coFields as $field)
                                    { ?>
                                            <a id="tab-clearance-field-<?php echo $tabCount; ?>" class="dropdown-item py-2" data-toggle="tab" href="#clearance-field-<?php echo $tabCount; ?>" role="tab" aria-controls="clearance-field-<?php echo $tabCount; ?>" <?php if($tabCount == 1) { ?>aria-selected="true"<?php } else { ?> aria-selected="false" <?php } ?> >
                                                <i class="fas fa-info-circle mr-2"></i>
                                                <strong><?php echo $field['field_name'] ?></strong>
                                                <span class="font-weight-normal badge badge-info badge-pill ml-1"><?php echo $field['position_name'] ?></span>

                                            </a>
                                        <?php
                                        $tabCount++;
                                    } ?>
                                        </div>
                                    </li>
                                </ul>

                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-12">
                                <div class="tab-content" id="field-stats">
                                    <?php
                                    $tabCount = 1;
                                    foreach($coFields as $field)
                                    { ?>
                                        <div class="p-2 tab-pane fade <?php if($tabCount == 1){ ?>show active <?php } ?>" id="clearance-field-<?php echo $tabCount; ?>" role="tabpanel" aria-labelledby="clearance-field-<?php echo $tabCount; ?>-tab">
                                            <?php if($field['clearedCount'] == 0 && $field['unclearedCount'] == 0) { ?>
                                                <div class="row justify-content-center ">
                                                    <i class="fas fa-exclamation-circle text-warning" style="font-size: 65px;"></i>
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
                                                            <a href="<?= base_url('Deficiencies/List/'.$field['field_id'].'-'.$field['position_id'].'/cleared') ?>">
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
                                                            <a href="<?= base_url('Deficiencies/List/'.$field['field_id'].'-'.$field['position_id'].'/uncleared') ?>">
                                                                <div class="small-box-footer bg-light py-1 px-3">
                                                                    <span class="float-right font-weight-bold" style="color: #800000;"><i class="ml-2 fa fa-arrow-circle-right"></i></span>
                                                                    <span class="float-left font-weight-bold" style="color: #800000;">View Details</span>
                                                                    <div class="clearfix"></div>
                                                                </div>
                                                            </a>
                                                        </div>
                                                    </div>

                                                    <?php if($view_submissions) { ?>
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
                                                                <a href="<?= base_url('Submissions/List/'.$field['field_id'].'-'.$field['position_id']) ?>">
                                                                    <div class="small-box-footer bg-light py-1 px-3">
                                                                        <span class="float-right font-weight-bold" style="color: #800000;"><i class="ml-2 fa fa-arrow-circle-right"></i></span>
                                                                        <span class="float-left font-weight-bold" style="color: #800000;">View Details</span>
                                                                        <div class="clearfix"></div>
                                                                    </div>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
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
    <?php } ?>
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
                        <?php if(!$clearance_message && session()->get("admin_access")) { ?>
                            <p class="text-center font-weight-light mb-0">Click the button below to initiate a new clearance period</p>
                        <?php } else if(!$clearance_message && !session()->get("admin_access")){ ?>
                            <p class="text-center font-weight-light mb-0">Please wait for the announcement.</p>
                        <?php }else { ?>
                            <p class="text-center font-weight-light mb-0"><?= $clearance_message; ?></p>
                        <?php } ?>
                    </div>
                    <div class="row justify-content-center mb-0">
                        <?php if(!$incoming_clearance && session()->get("admin_access")) { ?>
                            <button onclick="window.location.href='<?php echo base_url("ClearancePeriods/Initiate")?>'" class="btn btn-sm btn-primary mt-3 mb-0">Initiate Clearance</button>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>

