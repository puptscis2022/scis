    <div class="row">
        <div class="col-md-12">
            <!-- MAP & BOX PANE -->
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h4 class="card-title font-weight-bold m-0" style="color:#800000;">
                        <i class="fas fa-chart-bar mr-2"></i>
                        Clearance Forms Status
                    </h4>

                    <div class="card-tools ml-auto">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body py-2">
                    <div class="row d-flex justify-content-center w-100">
                        <div class="col-lg-4 col-md-4 col-sm-12 mt-2" style="overflow: hidden">
                            <div class="card" style="background-color:#800000;!important;">
                                <div class="card-body p-4 d-flex text-white">
                                    <div class="col-3 my-auto">
                                        <i class="fa fa-user-check fa-3x"></i>
                                    </div>
                                    <div class="col-9 text-right">
                                        <h1 class="font-weight-bold"><?= $cleared ?></h1>
                                        <h6 class="font-weight-bold"><?php echo (session()->get('Director_access')) ? "Approved" : "Cleared Forms"; ?></h6>
                                    </div>
                                </div>
                                <a href="<?= base_url('Clearance/FormsList/'.$clearanceData.'/1') ?>">
                                    <div class="small-box-footer bg-light py-1 px-3">
                                        <span class="float-right font-weight-bold" style="color: #800000;"><i class="ml-2 fa fa-arrow-circle-right"></i></span>
                                        <span class="float-left font-weight-bold" style="color: #800000;">View Details</span>
                                        <div class="clearfix"></div>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-4 col-sm-12 mt-2">
                            <div class="card" style="background-color:#800000;!important;">
                                <div class="card-body p-4 d-flex text-white">
                                    <div class="col-3 my-auto">
                                        <i class="fa fa-user-times fa-3x"></i>
                                    </div>
                                    <div class="col-9 text-right">
                                        <h1 class="font-weight-bold"><?= $completed ?></h1>
                                        <h6 class="font-weight-bold"><?php echo (session()->get('Director_access')) ? "Candidate for Approval" : "Candidate for Completion"; ?></h6>
                                    </div>
                                </div>
                                <a href="<?= base_url('Clearance/FormsList/'.$clearanceData) ?>">
                                    <div class="small-box-footer bg-light py-1 px-3">
                                        <span class="float-right font-weight-bold" style="color: #800000;"><i class="ml-2 fa fa-arrow-circle-right"></i></span>
                                        <span class="float-left font-weight-bold" style="color: #800000;">View Details</span>
                                        <div class="clearfix"></div>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-4 col-sm-12 mt-2">
                            <div class="card" style="background-color:#800000;!important;">
                                <div class="card-body p-4 text-white d-flex">
                                    <div class="col-3 my-auto">
                                        <i class="fa fa-file-import fa-3x"></i>
                                    </div>
                                    <div class="col-9 text-right">
                                        <h1 class="font-weight-bold"><?= $incomplete ?></h1>
                                        <h6 class="font-weight-bold">Incomplete Clearances</h6>
                                    </div>
                                </div>
                                <a href="<?= base_url('Clearance/FormsList/'.$clearanceData.'/2') ?>">
                                    <div class="small-box-footer bg-light py-1 px-3">
                                        <span class="float-right font-weight-bold" style="color: #800000;"><i class="ml-2 fa fa-arrow-circle-right"></i></span>
                                        <span class="float-left font-weight-bold" style="color: #800000;">View Details</span>
                                        <div class="clearfix"></div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div><!-- /.d-md-flex -->
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>
