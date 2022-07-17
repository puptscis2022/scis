<?= $this->extend("layouts/user_account_layout"); ?>

<?= $this->section("sidebar_menu"); ?>
    <?= $this->include('test_menu'); ?>
<?= $this->endSection(); ?>

<?= $this->section("page-title"); ?>
    Generate Report
<?= $this->endSection(); ?>

<?= $this->section("breadcrumb"); ?>
    <li class="breadcrumb-item"><a href="#"><i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li class="breadcrumb-item active">Generate Report</li>
<?= $this->endSection(); ?>

<?= $this->section("content"); ?>
    <div class="row">
        <div class="col-12">
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
            <form class="form" method="POST" action="<?= base_url('GenerateReports/Generate')?>" target="_blank">
                <div class="card">
                    <div class="card-body">
                        <div class="row p-3">
                            <div class="col-12">
                                <h5 class="font-weight-bold mb-0">Select Report</h5>
                                <p class="font-weight-light">Select available report to generate</p>
                                <hr>
                                <div class="form-row">
                                    <div class="col-md-6 mb-3">
                                        <select id="selectedReport" class="form-control select2bs4" onchange="generateReport()" name="selectedReport" required>
                                            <option></option>
                                            <?php if($ViewClearanceEntries) { ?>
                                                <option value="clearanceFieldStatus">Field Status Report</option>
                                            <?php } ?>
                                            <?php if($ViewClearanceForms && !session()->get('Student_access')) { ?>
                                                <option value="clearanceFormsStatus">Forms Status Report</option>
                                                <option value="clearanceForms">Clearance Forms</option>
                                            <?php } ?>
                                            <?php if(session()->get('admin_access')) { ?>
                                                <option value="graduationClearances">Graduation Clearances</option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <fieldset id="clearanceStatusFilter" class="mt-3">
                                    <h5 class="font-weight-bold mb-0">Report Options</h5>
                                    <p class="font-weight-light">Customize the report in this section</p>
                                    <hr>
                                    <div class="form-row">
                                        <div class="col-md-4 mb-3" id="clearancePeriod-set">
                                            <label for="clearancePeriod">Clearance Period</label>
                                            <select id="clearancePeriod" class="form-control select2bs4" name="clearancePeriod">
                                                <option></option>
                                                <?php foreach($periods as $per) { ?>
                                                    <option value="<?= $per->id ?>"> S.Y. <?= $per->year ?> | <?php if($per->semester == 0)
                                                        {
                                                            echo "Graduate";
                                                        } else if($per->semester == 1)
                                                        {
                                                            echo "1st Sem";
                                                        }else if($per->semester == 2)
                                                        {
                                                            echo "2nd Sem";
                                                        }else if($per->semester == 3)
                                                        {
                                                            echo "Summer";
                                                        }
                                                        ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3" id="schoolYear-set">
                                            <label for="scYear">School Year</label>
                                            <select id="scYear" class="form-control select2bs4" name="scYear">
                                                <option></option>
                                                <option value="all">All</option>
                                                <?php foreach($scYears as $year) { ?>
                                                    <option value="<?= $year['id'] ?>"><?= $year['school_year'] ?></option> 
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3" id="clearanceField-set">
                                            <label for="clearanceField">Clearance Field</label>
                                            <select id="clearanceField" class="form-control select2bs4" name="clearanceField">
                                                <option></option>
                                                <?php foreach($user_fields as $field) { ?>
                                                    <option value="<?= $field['field_id']  ?>"><?= $field['field_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label for="clearanceStatus">Clearance Status</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="clearanceStatus" id="allStatus" value="all" >
                                                <label class="form-check-label" for="allStatus">
                                                    All
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="clearanceStatus" id="Cleared" value="1">
                                                <label class="form-check-label" for="Cleared">
                                                    Cleared
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="clearanceStatus" id="Uncleared" value="0">
                                                <label class="form-check-label" for="Uncleared">
                                                    Uncleared
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="col-md-10 mb-3">
                                            <label for="Course">Course</label>
                                            <select id="Course" class="form-control select2bs4" name="Course" required>
                                                <option></option>
                                                <option value="all">All Course</option>
                                                <?php foreach($courses as $course){ ?>
                                                    <option value="<?= $course['id'] ?>"><?= $course['course_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-md-2 mb-3" id="YearLevel-set">
                                            <label>Year level</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="YearLevel" id="allYearLevel" value="all" >
                                                <label class="form-check-label" for="allYearLevel">
                                                    All
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="YearLevel" id="1stYear" value="1">
                                                <label class="form-check-label" for="1stYear">
                                                   1st Year
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="YearLevel" id="2ndYear" value="2">
                                                <label class="form-check-label" for="2ndYear">
                                                    2nd Year
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="YearLevel" id="3rdYear" value="3">
                                                <label class="form-check-label" for="3rdYear">
                                                    3rd Year
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="YearLevel" id="4thYear" value="4">
                                                <label class="form-check-label" for="4thYear">
                                                    4th Year
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="YearLevel" id="5thYear" value="5">
                                                <label class="form-check-label" for="5thYear">
                                                    5th Year
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success float-right ml-2"><i class="fas fa-print"></i> Generate</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?= $this->endSection(); ?>

