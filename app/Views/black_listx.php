<?= $this->extend("layouts/user_account_layout"); ?>

<?= $this->section("sidebar_menu"); ?>
<?= $this->include('test_menu'); ?>
<?= $this->endSection(); ?>

<?= $this->section("page-title"); ?>
    Blacklist
<?= $this->endSection(); ?>

<?= $this->section("breadcrumb"); ?>
    <li class="breadcrumb-item"><a href="#"><i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li class="breadcrumb-item active">Blacklist</li>
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
                        <?= $field_name; ?>
                    </h5>
                    <?php if($AddBlackList) { ?>
                        <a class="ml-auto btn btn-primary btn-sm d-inline float-right" data-toggle="modal" data-target="#add-student"><i class="fas fa-user-plus mr-1"></i> Add Student</a>
                    <?php } ?>
                </div>
                <div class="card-body">
                    <table id="BlacklistTable" class="table dt-responsive nowrap" style="width:100%">
                        <thead>
                        <tr>
                            <th>No.</th>
                            <th>Name</th>
                            <th>Course</th>
                            <th>Year & Section</th>
                            <th>Deficiency</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $count = 1;
                        foreach($blacklist as $row) {
                            ?>
                            <tr>
                                <td><?= $count++ ?></td>
                                <?php if($row->studID == 0) { ?>
                                    <td colspan="3" > All Students</td>
                                <?php } else { ?>
                                    <td>
                                        <?= $row->student_name ?>
                                    </td>
                                    <td><?= $row->course ?></td>
                                    <td><?= $row->year ?>-1</td>
                                <?php } ?>
                                <td><?= $row->deficiency ?></td>
                                <td>
                                    <?php if($EditBlackList) { ?>
                                        <!--<a class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#edit-listed-student" id="edit"><i class="fas fa-pencil-alt"></i></a>-->
                                    <?php } ?>
                                    <?php if($DeleteBlackList) { ?>
                                        <a class="btn btn-sm btn-danger" onclick="delBlacklist(this)" data-href="<?= base_url('/BlackList/removeStudent/'.$fieldID.'-'.$row->blID) ?>" data-stud-name="<?= $row->student_name ?>" data-stud-def="<?= $row->deficiency ?>" id="delete"><i class="fas fa-trash"></i></a>
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

    <!------ Modal for Adding Student to the Black List ------>
    <div class="modal fade" id="add-student">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-color">
                    <h5 class="modal-title">Add Student</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="<?= base_url("BlackList/addStudent") ?>">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="form-group">
                                <input type="text" name="fieldID" value="<?= $fieldID ?>" hidden>
                                <label for="course">Select Course</label>
                                <select class="form-control select2bs4-search" id="course" name="course" required>
                                    <option></option> <!--for placeholder-->
                                    <option value="all">All Course</option>
                                    <?php foreach($courses as $c) { ?>
                                        <option value="<?= $c['id'] ?>"><?= $c['course_name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="form-group" id="selectYearLevel" onchange="selectYearLevel()">
                                <label for="yearLevel">Select Year Level</label>
                                <select class="form-control select2bs4-search" id="yearLevel" name="yearLevel" required>
                                    <option></option> <!--for placeholder-->
                                    <option value="all">All Year Level</option>
                                    <option value="1">1st Year</option>
                                    <option value="2">2nd Year</option>
                                    <option value="3">3rd Year</option>
                                    <option value="4">4th Year</option>
                                    <option value="5">5th Year</option>
                                </select>
                            </div>


                            <div class="form-group" id="selectStudent"> <!--Show students based on the selected course & year--->
                                <input type="text" name="fieldID" value="<?= $fieldID ?>" hidden>
                                <label for="ClearanceField">Select Student</label>
                                <select class="form-control select2bs4-search" id="student" name="students" required>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="deficiency">Deficiency</label>
                                <select class="form-control select2bs4-search" name="deficiency" required>
                                    <?php foreach($requirements as $req) { ?>
                                        <option value="<?= $req->id ?>"><?= $req->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!------ MODAL FOR EDITING LISTED STUDENT ------>
    <div class="modal fade" id="edit-listed-student">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-color">
                    <h5 class="modal-title">Edit Student</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="form-group">
                                <input type="text" name="fieldID" value="<?= $fieldID ?>" hidden>
                                <label for="course">Select Course</label>
                                <select class="form-control select2bs4-search" name="course" required>
                                    <option></option> <!--for placeholder-->
                                    <option value="all">All Course</option>
                                    <option value="1">BSIT</option>
                                    <option value="2">DICT</option>
                                </select>
                            </div>

                            <div class="form-group" id="selectYearLevel" onchange="selectYearLevel()">
                                <label for="yearLevel">Select Year Level</label>
                                <select class="form-control select2bs4-search" name="yearLevel" required>
                                    <option></option> <!--for placeholder-->
                                    <option value="all">All Year Level</option>
                                    <option value="1">1st Year</option>
                                    <option value="2">2nd Year</option>
                                </select>
                            </div>

                            <div class="form-group" id="selectStudent"> <!--Show students based on the selected course & year--->
                                <input type="text" name="fieldID" value="<?= $fieldID ?>" hidden>
                                <label for="ClearanceField">Select Student</label>
                                <select class="form-control select2bs4-search" name="students" required>
                                    <option></option> <!--for placeholder-->
                                    <option value="0">All {Course} {Year Level} Students</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="deficiency">Deficiency</label>
                                <select class="form-control select2bs4-search" name="deficiency" required>
                                    <option value="" selected></option>
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
            $('#BlacklistTable').DataTable();
        } );
    </script>

    <script>
        function delBlacklist(element){
            var name = element.dataset.studName;
            var def = element.dataset.studDef;
            const href = element.dataset.href;

            Swal.fire({
                title: 'Confirm Action',
                text: 'Are you sure you want to remove ' + def.trim() + ' of ' + name.trim() + ' from the black list?',
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
        $("#course").change(function(){
            $("#selectYearLevel").show();
        });

        function selectYearLevel(){
            var yearLevel = document.getElementById("yearLevel").value;
            var course = document.getElementById("course").value;

            $.post("<?php echo base_url() ?>/BlackList/studentsList/" + course + "/" + yearLevel);

            console.log("<?php echo base_url() ?>/BlackList/studentsList/" + course + "/" + yearLevel);
            
            document.getElementById("selectStudent").style.display = "block";
        }

    </script>
<?= $this->endSection(); ?>