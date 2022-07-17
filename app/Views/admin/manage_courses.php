<?= $this->extend("layouts/user_account_layout"); ?>

<?= $this->section("sidebar_menu"); ?>
    <?= $this->include('test_menu') ?>
<?= $this->endSection(); ?>

<?= $this->section("page-title"); ?>
    Manage Courses
<?= $this->endSection(); ?>

<?= $this->section("breadcrumb"); ?>
    <li class="breadcrumb-item"><a href="#"><i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li class="breadcrumb-item active">Manage Courses</li>
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
                    <div class="alert alert-danger alert-dismissible">
                        <i class="fas fa-exclamation-triangle mr-1" style="color: #800000;"></i>
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <?php echo $row; ?>
                    </div>
                <?php } ?>
            <?php endif; ?>
            <!---------------------------------------------------------------------->
            <div class="card">
                <div class="card-header d-flex align-items-center" style="color:#800000;">
                    <h5 class="font-weight-bold m-0">
                        Courses
                    </h5>
                    <?php if($AddCourses) { ?>
                        <a class="ml-auto btn btn-primary btn-sm d-inline float-right" data-toggle="modal" data-target="#add-course"><i class="fas fa-plus mr-1"></i> Add Course</a>
                    <?php } ?>
                </div>
                <div class="card-body">
                    <table id="ListOfCoursesTable" class="table dt-responsive display compact" style="width:100%">
                        <thead>
                            <tr>
                                <th width="5%">No.</th>
                                <th width="10%">Code</th>
                                <th width="30%">Course Name</th>
                                <th width="15%">Course Duration</th>
                                <th width="30%">Corresponding Organization</th>
                                <th width="10%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $count = 1;
                                foreach($Courses as $row)
                                {
                            ?>
                                <tr>
                                    <td class="align-middle"><?= $count; ?></td>
                                    <td class="align-middle"><?= $row->abb ?></td>
                                    <td class="align-middle"><?= $row->name; ?></td>
                                    <td class="align-middle"><?= $row->year_levels; ?> <?=($row->year_levels <= '1') ? ' Year' : ' Years'; ?></td>
                                    <td class="align-middle"><?= $row->organization; ?></td>
                                    <td class="align-middle">
                                        <?php if($EditCourses) { ?>
                                            <a href="#edit-course" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#edit-course" data-course-id="<?php echo $row->id; ?>" data-course-code="<?= $row->abb ?>" data-course-name="<?php echo $row->name; ?>" data-course-max="<?php echo $row->year_levels; ?>" data-org-id="<?php echo $row->org_id; ?>" id="edit"><i class="fas fa-pencil-alt"></i> </a>
                                        <?php } ?>
                                        <?php if($DeleteCourses) { ?>
                                            <a class="btn btn-danger btn-sm" onclick="del(this)" data-href="Courses/deleteCourse/<?php echo $row->id; ?>" data-delete-name="<?php echo $row->name ?>" id="delete"><i class="fas fa-trash"></i> </a>
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

    <!------ MODAL FOR ADDING COURSE ------>
    <div class="modal fade" id="add-course">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-color">
                    <h5 class="modal-title">Add Course</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="<?= base_url('Courses/newCourse'); ?>">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="form-group required">
                                <label for="CourseName">Code</label>
                                <input type="text" class="form-control" id="CourseCode" name="courseCode" autocomplete="off" required>
                            </div>
                            <div class="form-group required">
                                <label for="CourseName">Course Name</label>
                                <input type="text" class="form-control" id="CourseName" name="courseName" autocomplete="off" required>
                            </div>
                            <div class="form-group required">
                                <label for="MaxYear">Course Duration</label>
                                <select class="form-control select2bs4" name="maxYear" required>
                                    <option></option>
                                    <option value="1">1 Year</option>
                                    <option value="2">2 Years</option>
                                    <option value="3">3 Years</option>
                                    <option value="4">4 Years</option>
                                    <option value="5">5 Years</option>
                                </select>
                            </div>
                            <div class="form-group required">
                                <label for="CorrespondingOrg">Corresponding Organization</label>
                                <select class="form-control select2bs4" name="orgID" required>
                                    <option></option>
                                    <?php foreach($Organizations as $row) { ?>
                                        <option value="<?= $row['id'] ?>"><?= $row['organization_name']; ?></option>
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

    <!------ MODAL FOR EDITING COURSE ------>
    <div class="modal fade" id="edit-course">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-color">
                    <h5 class="modal-title">Edit Course</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="<?= base_url('Courses/editCourse'); ?>">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <input type="text" class="form-control" name="courseID" value="" hidden required>
                            <div class="form-group required">
                                <label for="CourseName">Code</label>
                                <input type="text" class="form-control" name="courseCode" autocomplete="off" required>
                            </div>
                            <div class="form-group required">
                                <label for="CourseName">Course Name</label>
                                <input type="text" class="form-control" name="courseName" value="" autocomplete="off" required>
                            </div>
                            <div class="form-group required">
                                <label for="MaxYear">Course Duration</label>
                                <select class="custom-select" name="maxYear" required>
                                    <option></option>
                                    <option value="1">1 Year</option>
                                    <option value="2">2 Years</option>
                                    <option value="3">3 Years</option>
                                    <option value="4">4 Years</option>
                                    <option value="5">5 Years</option>
                                </select>
                            </div>
                            <div class="form-group required">
                                <label for="CorrespondingOrg">Corresponding Organization</label>
                                <select class="custom-select" name="orgID" required>
                                    <?php foreach($Organizations as $row) { ?>
                                        <option value="<?= $row['id'] ?>"><?= $row['organization_name']; ?></option>
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
            $('#ListOfCoursesTable').DataTable();
        } );
    </script>
<?= $this->endSection(); ?>


