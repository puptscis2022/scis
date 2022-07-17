    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center" style="color:#800000;">
                    <h4 class="card-title font-weight-bold m-0">
                        <i class="fas fa-chart-bar mr-2"></i>
                        Clearance Status
                    </h4>
                    <form class="form ml-auto" method="POST" action="<?= base_url('GenerateReports/FormReport')?>" target="_blank">
                        <input type="text" name="form_id" value="<?= $formID  ?>" hidden>
                        <button type="submit" class="btn btn-sm btn-success float-right ml-2"><i class="fas fa-print"></i> Generate Form</button>
                    </form>
                </div>

                <div class="row px-4 pt-4">
                    <div class="col-lg-4 col-md-6">
                        <div class="card" style="background-color:#800000;!important;">
                            <div class="card-body p-4">
                                <div class="row text-white">
                                    <div class="col-3 my-auto">
                                        <i class="fa fa-file-signature fa-3x"></i>
                                    </div>
                                    <div class="col-9 text-right">
                                        <h1 class="font-weight-bold"><?= $cleared; ?></h1>
                                        <h6 class="font-weight-bold">Cleared Fields</h6>
                                    </div>
                                </div>
                            </div>
                            <a href="<?= base_url('Clearance/Form/'.$periodID); ?>">
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
                                        <h1 class="font-weight-bold"><?= $uncleared; ?></h1>
                                        <h6 class="font-weight-bold">Uncleared Fields</h6>
                                    </div>
                                </div>
                            </div>
                            <a href="<?= base_url('Clearance/Form/'.$periodID); ?>">
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
                                        <h1 class="font-weight-bold"><?= $deficiencies;?></h1>
                                        <h6 class="font-weight-bold">Deficiencies</h6>
                                    </div>
                                </div>
                            </div>
                            <a href="<?= base_url('Clearance/Form/'.$periodID); ?>">
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