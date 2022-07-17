<?php
     $MenuAccess = session()->get('menu'); 
     $clearance_periods = session()->get('clearance_periods');
     $this->extend("layouts/user_account_layout");
?>
<li class="nav-item">
    <a href="<?php echo base_url("test_dashboard")?>" class="nav-link">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>Dashboard</p>
    </a>
</li>

<?php if($MenuAccess['UserManagement'] || $MenuAccess['Maintenance'] ||$MenuAccess['ActivityLogs']) { ?>
    <li class="nav-header">SYSTEM</li>
<?php } ?>

<?php if($MenuAccess['UserManagement']) { ?>
    <li class="nav-item">
        <a href="#" class="nav-link">
            <i class="nav-icon fas fa-users"></i>
            <p>
                User Management
                <i class="fas fa-angle-left right"></i>
            </p>
        </a>
        <ul class="nav nav-treeview ">
            <?php if($MenuAccess['UsersList']) { ?>
                <li class="nav-item">
                    <a href="<?php echo base_url("UserManagement/UsersList")?>" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Users</p>
                    </a>
                </li>
            <?php }  ?>
            <?php if($MenuAccess['VerifyUsers']) { ?>
                <li class="nav-item">
                    <a href="<?php echo base_url("UserManagement/verifyUsers")?>" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Verify Registrations</p>
                    </a>
                </li>
            <?php } ?>
            <?php if($MenuAccess['RolesAndPermissions']) { ?>
                <li class="nav-item">
                    <a href="<?php echo base_url("RoleManagement")?>" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Roles and Permissions</p>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </li>
<?php } ?>

<?php if($MenuAccess['Maintenance']) { ?>
    <li class="nav-item">
        <a href="#" class="nav-link">
            <i class="nav-icon fas fa-cogs"></i>
            <p>
                Maintenance
                <i class="fas fa-angle-left right"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">

            <?php if($MenuAccess['ClearanceFields']) { ?>
                <li class="nav-item">
                    <a href="<?php echo base_url("ClearanceFields")?>" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Manage Clearance Fields</p>
                    </a>
                </li>
            <?php } ?>
            <?php if($MenuAccess['Organizations']) { ?>
                <li class="nav-item">
                    <a href="<?php echo base_url("Organizations")?>" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Manage Organizations</p>
                    </a>
                </li>
            <?php } ?>
            <?php if($MenuAccess['Courses']) { ?>
                <li class="nav-item">
                    <a href="<?php echo base_url("Courses")?>" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Manage Courses</p>
                    </a>
                </li>
            <?php } ?>
            <?php if($MenuAccess['Majors']) { ?>
                <li class="nav-item">
                    <a href="<?php echo base_url("Majors")?>" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Manage Majors</p>
                    </a>
                </li>
            <?php } ?>
            <?php if($MenuAccess['Subjects']) { ?>
                <li class="nav-item">
                    <a href="<?php echo base_url("Subjects")?>" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Manage Subjects</p>
                    </a>
                </li>
            <?php } ?>
            <?php if($MenuAccess['Positions']) { ?>
                <li class="nav-item">
                    <a href="<?php echo base_url("Positions")?>" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Manage Positions</p>
                    </a>
                </li>
            <?php } ?>
            <?php if($MenuAccess['Requirements']) { ?>
                <li class="nav-item">
                    <a href="#" class="nav-link <?php if(empty($user_fields)) {  ?> alert-mess-req <?php } ?>" data-toggle="modal" data-target="#manage-req-choose-field">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Manage Requirements</p>
                    </a>

                </li>
            <?php } ?>
            <?php if($MenuAccess['Prerequisites']) { ?>
                <li class="nav-item">
                    <a href="#" class="nav-link <?php if(empty($user_fields)) {  ?> alert-mess-prerequisite <?php } ?>" data-toggle="modal" data-target="#manage-prereq-choose-field">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Manage Prerequisites</p>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </li>
<?php } ?>

<?php if($MenuAccess['ActivityLogs']) { ?>
    <li class="nav-item">
        <a href="<?php echo base_url("ActivityLogs")?>" class="nav-link">
            <i class="nav-icon fas fa-user-clock"></i>
            <p>Activity Logs</p>
        </a>
    </li>
<?php } ?>

<?php if($MenuAccess['StudentClearanceRecords']||$MenuAccess['InitiateClearancePeriod']||$MenuAccess['BlackList']||$MenuAccess['ManageDeficiencies']||$MenuAccess['Submissions']||$MenuAccess['ClearanceFinalization']||$MenuAccess['ClearanceHistory']||$MenuAccess['GraduationClearance']) { ?>
<li class="nav-header">STUDENT CLEARANCE</li>
<?php } ?>
    <?php if($MenuAccess['StudentClearanceRecords']) { ?>
        <li class="nav-item">
            <a href="#" class="nav-link <?php if(empty($clearance_periods)) {  ?>alert-mess-record <?php } ?>">
                <i class="nav-icon fas fa-users"></i>
                <p>
                    Clearance Records
                    <?php if(!empty($clearance_periods)) {  ?>
                        <i class="fas fa-angle-left right"></i>
                    <?php } ?>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <?php if(!empty($clearance_periods)) {  ?>
                    <?php foreach($clearance_periods as $period) { ?>
                        <li class="nav-item">
                            <a href="<?= base_url('Clearance/Form/'.$period['id']); ?>" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p><?= $period['title'] ?></p>
                            </a>
                        </li>
                    <?php } ?>
                <?php } ?>
            </ul>
        </li>
    <?php } ?>

<?php if($MenuAccess['InitiateClearancePeriod']||$MenuAccess['BlackList']||$MenuAccess['ManageDeficiencies']||$MenuAccess['ClearanceFinalization']) { ?>
<li class="nav-item" >
    <a href="#" class="nav-link">
        <i class="nav-icon fas fa-file-contract"></i>
        <p>
            Semestral Clearance
            <i class="fas fa-angle-left right"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <?php if(!$activeClearance) { ?>

            <?php if($MenuAccess['InitiateClearancePeriod']) { ?>
                <li class="nav-item">
                    <a href="<?php echo base_url("ClearancePeriods/Initiate")?>" class="nav-link">
                        <i class="nav-icon far fa-circle"></i>
                        <p>Initiate Clearance</p>
                    </a>
                </li>
            <?php } ?>

            <?php if($MenuAccess['BlackList']) { ?>
                <li class="nav-item">
                    <a href="#" class="nav-link <?php if(empty($user_fields)) {  ?> alert-mess-blacklist <?php } ?>" data-toggle="modal" data-target="#black-list-choose-field">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Black List</p>
                    </a>
                </li>
            <?php } ?>

            <?php } else { ?>

                <?php if($MenuAccess['ManageDeficiencies']) { ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link <?php if(empty($user_fields)) {  ?> alert-mess-manage-def <?php } ?> " data-toggle="modal" data-target="#manage-def-sem-choose-field">
                            <i class="far fa-circle nav-icon"></i>
                            Manage Deficiencies
                        </a>
                    </li>
                <?php } ?>

                <?php if($MenuAccess['Submissions']) { ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link <?php if(empty($user_fields)) {  ?> alert-mess-submissions <?php } ?>"  data-toggle="modal" data-target="#submissions-sem-choose-field">
                            <i class="nav-icon far fa-circle"></i>
                            Submissions
                        </a>
                    </li>
                <?php } ?>
                <?php if($MenuAccess['ClearanceFinalization']) { ?>
                    <li class="nav-item">
                        <a href="<?php echo base_url('Clearance/FormsList/'.$clearanceData)?>" class="nav-link">
                            <i class="nav-icon far fa-circle"></i>
                            <?php echo (session()->get('Director_access')) ? "Clearance Approval" : "Clearance Completion"; ?> <!-- or finalize clearance? Clearance Forms? -->
                        </a>
                    </li>
                <?php } ?>
        <?php } ?>

        <?php if($MenuAccess['ClearanceHistory'] && !session()->get('Student_access')) { ?>
            <li class="nav-item">
                <a href="<?php echo base_url('/ClearancePeriods')?>" class="nav-link">
                    <i class="nav-icon far fa-circle"></i>
                    Clearance History <!-- or finalize clearance? Clearance Forms? -->
                </a>
            </li>
        <?php } ?>
    </ul>
</li>
<?php } ?>

<?php if($MenuAccess['GraduationClearance']) { ?>
    <li class="nav-item">
        <a href="#" class="nav-link">
            <i class="nav-icon fas fa-user-graduate"></i>
            <p>
                Graduation Clearance
                <i class="fas fa-angle-left right"></i>
            </p>
        </a>


        <ul class="nav nav-treeview">
            <?php if($MenuAccess['GraduationApplications']) { ?>
                <li class="nav-item">
                    <a href="<?= base_url('GraduationClearance'); ?>" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        Application
                    </a>
                </li>
            <?php } ?>

            <?php if($MenuAccess['GraduationSubjectDeficiencies']) { ?>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-toggle="modal" data-target="#manage-def-grad-choose-sub">
                        <div class="d-flex">
                            <div><i class="far fa-circle nav-icon"></i></div>
                            <div class="ml-1"><p>Manage Subject Deficiencies</p></div>
                        </div>
                    </a>
                </li>
            <?php } ?>

            <?php if($MenuAccess['ManageDeficiencies']) { ?>
                <li class="nav-item">
                    <a href="#" class="nav-link <?php if(empty($user_gradFields)) {  ?> alert-mess-grad-manage-def <?php } ?> " data-toggle="modal" data-target="#manage-def-grad-choose-field">
                        <i class="far fa-circle nav-icon"></i>
                        Manage Deficiencies
                    </a>
                </li>
            <?php } ?>

            <?php if($MenuAccess['Submissions']) { ?>
                <li class="nav-item">
                    <a href="#" class="nav-link <?php if(empty($user_gradFields)) {  ?> alert-mess-grad-submissions <?php } ?>"  data-toggle="modal" data-target="#submissions-grad-choose-field">
                        <i class="nav-icon far fa-circle"></i>
                        Submissions
                    </a>
                </li>
            <?php } ?>
            <?php if($MenuAccess['ClearanceFinalization']) { ?>
                <li class="nav-item">
                    <a href="<?php echo base_url('Clearance/FormsList/Graduation')?>" class="nav-link">
                        <i class="nav-icon far fa-circle"></i>
                        <?php echo (session()->get('Director_access')) ? "Clearance Approval" : "Clearance Completion"; ?> <!-- or finalize clearance? Clearance Forms? -->
                    </a>
                </li>
            <?php } ?>
        </ul>
    </li>
<?php } ?>
<?php if($MenuAccess['GenerateReports']) { ?>
    <li class="nav-item">
        <a href="<?= base_url('GenerateReports'); ?>" class="nav-link">
            <i class="nav-icon fas fa-print"></i>
            <p>Generate Reports</p>
        </a>
    </li>
<?php } ?>







