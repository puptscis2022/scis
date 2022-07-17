<?= $this->extend("layouts/user_account_layout"); ?>

<?= $this->section("sidebar_menu"); ?>
<?= $this->include('test_menu') ?>
<?= $this->endSection(); ?>

<?= $this->section("page-title"); ?>
Clearance Form
<?= $this->endSection(); ?>

<?= $this->section("breadcrumb"); ?>
<li class="breadcrumb-item"><a href="#"><i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</a></li>
<li class="breadcrumb-item active">Clearance Form</li>
<?= $this->endSection(); ?>

<?= $this->section("content"); ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center" style="color:#800000;">
                <h5 class="font-weight-bold m-0">
                    Clearance Form
                </h5>
                <form class="form ml-auto" method="POST" action="<?= base_url('GenerateReports/FormReport')?>" target="_blank">
                    <input type="text" name="form_id" value="<?= $formID  ?>" hidden>
                    <button type="submit" class="btn btn-sm btn-success float-right ml-2"><i class="fas fa-print"></i> Generate Form</button>
                </form>
            </div>
            <div class="card-body px-5">
                <div class="table-responsive mb-3">
                    <table class="table table-bordered mt-2 w-100">
                        <thead>
                        <tr>
                            <th scope="col" style="width:10%">No.</th>
                            <th scope="col" style="width:25%">Clearance Field</th>
                            <th scope="col" style="width:20%">Position</th>
                            <th scope="col" style="width:20%">Clearance Officer</th>
                            <th scope="col" style="width:15%">Status</th>
                            <th scope="col" style="width:10%">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $count = 1;
                        foreach($Entries as $ent) {
                            ?>
                            <?php if($ent['clearance_field'] != "Director's Office") { ?>
                                <tr>
                                    <td><?= $count++; ?></td>
                                    <td><?= $ent['clearance_field']; ?></td>
                                    <td><?= $ent['pos'] ?></td>
                                    <td><?= $ent['clearance_officer']; ?></td>
                                    <td>
                                        <?php if($ent['status'] == 1) { ?>
                                            <span class="badge badge-success badges">Cleared</span>
                                        <?php } else { ?>
                                            <span class="badge badge-warning badges">Pending</span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php if($Requirements) { ?>
                                            <a href="<?= base_url('Clearance/Requirements/'.$ent['id']) ?>" class="btn btn-primary btn-sm" title="View Requirements">View </a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div class="container">
                    <div class="row justify-content-around">
                        <div class="col-md-4">
                            <div class="card shadow-none border d-flex flex-fill">
                                <div class="card-body p-3">
                                    <div class="row">
                                        <div class="col-md-12 my-auto text-center">
                                            <h2 class="lead mb-0"><b>Dr. Marissa B. Ferrer</b></h2>
                                            <h6 class="text-muted mt-0">Director</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="footer border-top">
                                    <div class="row justify-content-around">
                                        <div class="col-md-6">
                                            <?php if($director_sign) { ?>
                                                <div class="text-center text-success p-1" >
                                                    <i class="fas fa-check mr-1"></i> Approved
                                                </div>
                                            <?php } else { ?>
                                                <div class="text-center text-danger p-1">
                                                    <i class="fas fa-clock mr-1"></i> Pending
                                                </div>
                                            <?php } ?>

                                        </div>

                                        <div class="col-md-6">
                                            <div class="text-center p-1">
                                                <?php if($Requirements) { ?>
                                                    <a href="<?= base_url('Clearance/Requirements/'.$doEntryID) ?>"><i class="fas fa-eye mr-1"></i>View Details</a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card shadow-none border d-flex flex-fill">
                                <div class="card-body p-3">
                                    <div class="row">
                                        <div class="col-md-12 my-auto text-center">
                                            <h2 class="lead mb-0"><b>Prof. Mhel P. Garcia</b></h2>
                                            <h6 class="text-muted mt-0">Registrar</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="footer border-top">
                                    <div class="row justify-content-around">
                                        <div class="col-md-6">
                                            <?php if($registrar_sign) { ?>
                                                <div class="text-center text-success p-1">
                                                    <i class="fas fa-check mr-1"></i> Received
                                                </div>
                                            <?php } else { ?>
                                                <div class="text-center text-danger p-1">
                                                    <i class="fas fa-clock mr-1"></i> Pending
                                                </div>
                                            <?php } ?>

                                        </div>

                                        <!--<div class="col-md-6">
                                            <div class="text-center p-1">
                                                <a href="#">
                                                    <i class="fas fa-eye mr-1"></i> View Details
                                                </a>
                                            </div>
                                        </div>-->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <!---
                        <div class="col-md-6 col-sm-6 ">
                            <div class="p-5 text-center">
                                <h6 class="font-weight-bold mb-0">Marissa B. Ferrer</h6>
                                <h6 class="font-weight-normal text-sm">Director's Office</h6>

                                <?php if($director_sign) { ?>
                                    <span class="badge badge-success badges">Approved</span>
                                <?php } else { ?>
                                    <span class="badge badge-danger badges">Pending</span>
                                <?php } ?>
                                <?php if($Requirements) { ?>
                                    <a href="<?= base_url('Clearance/Requirements/'.$doEntryID) ?>" class="btn btn-primary btn-sm" title="View Requirements"><i class="fas fa-eye"></i> </a>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <div class="p-5 text-center">
                                <h6 class="font-weight-bold mb-0">Mhel Garcia</h6>
                                <h6 class="font-weight-normal text-sm">Registrar's Office</h6>

                                <?php if($registrar_sign) { ?>
                                    <span class="badge badge-success badges">Received</span>
                                <?php } else { ?>
                                    <span class="badge badge-danger badges">Pending</span>
                                <?php } ?>
                            </div>
                        </div>
                        --->
            </div>
        </div>
    </div>
</div>
</div>

<?= $this->endSection(); ?>