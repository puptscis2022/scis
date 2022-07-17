<li class="nav-item">
    <a href="<?php echo base_url("Dashboard")?>" class="nav-link">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>Dashboard</p>
    </a>
</li>

<li class="nav-item">
    <a href="<?php echo base_url("Notifications")?>" class="nav-link">
        <i class="nav-icon fas fa-bell"></i>
        <p>
            Notification
            <span class="badge badge-info right"><?= $user_notifications['total_notif'] ?></span>
        </p>
    </a>
</li>

<li class="nav-header">SYSTEM</li>
<?php if( session()->get("superAdmin_access")) { ?>
<li class="nav-item">
    <a href="#" class="nav-link">
        <i class="nav-icon fas fa-users"></i>
        <p>
            User Management
            <i class="fas fa-angle-left right"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="<?php echo base_url("UserManagement/UsersList")?>" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Users</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?php echo base_url("UserManagement/verifyUsers")?>" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Verify User</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?php echo base_url("RoleManagement")?>" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Roles and Permissions</p>
            </a>
        </li>
    </ul>
</li>
<?php } ?>
<li class="nav-item">
    <a href="#" class="nav-link">
        <i class="nav-icon fas fa-cogs"></i>
        <p>
            Maintenance
            <i class="fas fa-angle-left right"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">

<?php if( session()->get("superAdmin_access")) { ?>
        <li class="nav-item">
            <a href="<?php echo base_url("Maintenance/ClearanceFields")?>" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Manage Clearance Fields</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?php echo base_url("Maintenance/Organizations")?>" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Manage Organizations</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?php echo base_url("Maintenance/Courses")?>" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Manage Courses</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?php echo base_url("Maintenance/Positions")?>" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Manage Positions</p>
            </a>
        </li>
    <?php }else{ ?>
        <li class="nav-item">
            <a href="<?php echo base_url("Maintenance/Requirements")?>" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Manage Requirements</p>
            </a>
        </li>
    <?php } ?>
        <!-- <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>
                    Manage Prerequisites
                    <i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <?php if(!empty($user_fields)) {  ?>
                    <?php foreach($user_fields as $field) { ?>
                        <li class="nav-item">
                            <a href="<?= base_url('PreRequisites/list/'.$field['field_id'])?>" class="nav-link">
                                <i class="far fa-dot-circle nav-icon"></i>
                                <p><?= $field['field_name'] ?></p>
                            </a>
                        </li>
                    <?php } ?>
                <?php } else { ?>
                    No Clearance Field Assigned
                <?php } ?>
            </ul>
        </li> -->
    </ul>
</li>

<?php if( session()->get("superAdmin_access")) { ?>
<li class="nav-item">
    <a href="<?php echo base_url("ActivityLogs")?>" class="nav-link">
        <i class="nav-icon fas fa-user-clock"></i>
        <p>Activity Logs</p>
    </a>
</li>
<?php }else{ ?>


    <li class="nav-header">STUDENT CLEARANCE</li>
<?php if(!$activeClearance) { ?>
    <li class="nav-item">
        <a href="<?php echo base_url("ClearanceManagement/InitiateClearancePage")?>" class="nav-link">
            <i class="nav-icon far fa-calendar-alt"></i>
            <p>Initiate Clearance</p>
        </a>
    </li>

    <li class="nav-item">
        <a href="#" class="nav-link <?php if(empty($user_fields)) {  ?> alert-mess-blacklist <?php } ?>">
            <i class="fas fa-user-slash nav-icon"></i>
            <p>
                Blacklist
                <?php if(!empty($user_fields)) {  ?>
                    <i class="fas fa-angle-left right"></i>
                <?php } ?>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <?php if(!empty($user_fields)) {  ?>
                <?php foreach($user_fields as $field) { ?>
                    <li class="nav-item">
                        <a href="<?= base_url('BlackList/list/'.$field['field_id']); ?>" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p><?= $field['field_name'] ?></p>
                        </a>
                    </li>
                <?php } ?>
            <?php } ?>
        </ul>
    </li>
<?php } else { ?>
    <li class="nav-item">
        <a href="#" class="nav-link <?php if(empty($user_fields)) {  ?> alert-mess-manage-def <?php } ?>">
            <i class="nav-icon fas fa-user-tag"></i>
            <p>
                Manage Deficiencies
                <?php if(!empty($user_fields)) {  ?>
                    <i class="fas fa-angle-left right"></i>
                <?php } ?>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <?php if(!empty($user_fields)) {  ?>
                <?php foreach($user_fields as $field) { ?>
                    <li class="nav-item">
                        <a href="<?= base_url('ClearanceManagement/Deficiencies/'.$field['field_id']); ?>" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p><?= $field['field_name'] ?></p>
                        </a>
                    </li>
                <?php } ?>
            <?php } ?>
        </ul>
    </li>

    <li class="nav-item">
        <a href="#" class="nav-link <?php if(empty($user_fields)) {  ?> alert-mess-submissions <?php } ?>">
            <i class="nav-icon fas fa-archive"></i>
            <p>
                Submissions
                <?php if(!empty($user_fields)) {  ?>
                    <i class="fas fa-angle-left right"></i>
                <?php } ?>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <?php if(!empty($user_fields)) {  ?>
                <?php foreach($user_fields as $field) { ?>
                    <li class="nav-item">
                        <a href="<?= base_url('Submissions/List/'.$field['field_id']); ?>" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p><?= $field['field_name'] ?></p>
                        </a>
                    </li>
                <?php } ?>
            <?php } ?>
        </ul>
    </li>
    <li class="nav-item">
        <a href="<?php echo base_url('ClearanceManagement/ClearanceCompletion/'.$clearanceData['current_period'])?>" class="nav-link">
            <i class="nav-icon far fa-calendar-alt"></i>
            <p>Clearance Completion</p> <!-- or finalize clearance? Clearance Forms? -->
        </a>
    </li>
<?php } ?>

<li class="nav-item">
    <a href="<?= base_url('ClearanceManagement/CreateReportPage'); ?>" class="nav-link">
        <i class="nav-icon far fa-copy"></i>
        <p>Generate Reports</p>
    </a>
</li>



<li class="nav-item">
    <a href="#" class="nav-link">
        <i class="nav-icon fas fa-graduation-cap"></i>
        <p>Graduation Clearance</p>
    </a>
</li>

<li class="nav-item">
    <a href="<?php echo base_url('ClearanceManagement/Periods')?>" class="nav-link">
        <i class="nav-icon fas fa-history"></i>
        <p>Clearance History</p> <!-- or finalize clearance? Clearance Forms? -->
    </a>
</li>
<?php }  ?>


