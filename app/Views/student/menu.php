<li class="nav-item">
    <a href="<?= base_url('Dashboard'); ?>" class="nav-link">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>Dashboard</p>
    </a>
</li>

<li class="nav-item">
    <a href="<?= base_url('Notifications'); ?>" class="nav-link">
        <i class="nav-icon fas fa-bell"></i>
        <p>
            Notification
            <span class="badge badge-info right"><?= $user_notifications['total_notif']; ?></span>
        </p>

    </a>
</li>


<li class="nav-header">STUDENT CLEARANCE</li>
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
                    <a href="<?= base_url('ClearanceManagement/ClearanceForm/'.$period['id']); ?>" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p><?= $period['title'] ?></p>
                    </a>
                </li>
            <?php } ?>
        <?php } ?>
    </ul>
</li>

<li class="nav-item">
    <a href="<?= base_url('GraduationClearance'); ?>" class="nav-link">
        <i class="nav-icon fas fa-graduation-cap"></i>
        <p>
            Graduation Clearance
        </p>
    </a>
</li>


