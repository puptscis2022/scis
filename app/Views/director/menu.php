<li class="nav-item">
    <a href="<?= base_url('Dashboard')?>" class="nav-link">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>Dashboard</p>
    </a>
</li>

<li class="nav-item">
    <a href="<?= base_url('Notifications')?>" class="nav-link">
        <i class="nav-icon fas fa-bell"></i>
        <p>
            Notification
            <span class="badge badge-info right"><?= $user_notifications['total_notif'] ?></span>
        </p>

    </a>
</li>

<!-- <li class="nav-header">SYSTEM</li>
<li class="nav-item">
    <a href="#" class="nav-link">
        <i class="nav-icon fas fa-cogs"></i>
        <p>
            Maintenance
            <i class="fas fa-angle-left right"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="<?= base_url('Maintenance/Requirements')?>" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Manage Requirements</p>
            </a>
        </li>
    </ul>
</li> -->

<li class="nav-header">STUDENT CLEARANCE</li>
<?php if($activeClearance) { ?>
    <li class="nav-item">
        <a href="<?php echo base_url('ClearanceManagement/ClearanceCompletion/'.$clearanceData['current_period'])?>" class="nav-link">
            <i class="nav-icon far fa-calendar-alt"></i>
            <p>Clearance Approval</p>
        </a>
    </li>
<?php } ?>
<!-- <li class="nav-item">
    <a href="<?= base_url('ClearanceManagement/CreateReportPage') ?>" class="nav-link">
        <i class="nav-icon far fa-copy"></i>
        <p>Generate Reports</p>
    </a>
</li>
<li class="nav-item">
    <a href="<?php echo base_url('ClearanceManagement/Periods')?>" class="nav-link">
        <i class="nav-icon far fa-calendar-alt"></i>
        <p>Clearance History</p>
    </a>
</li> -->


