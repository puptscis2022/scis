<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $page_title; ?></title>

    <link rel="icon" type="image/png" href="<?= base_url('assets/img/PUPLogo.png') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/dropify/css/dropify.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/select2/css/select2.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/select2/css/select2-bootstrap4.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/adminlte.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/font-awesome/css/all.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/OverlayScrollbars.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/fancybox/jquery.fancybox.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/datatables/dataTables.bootstrap4.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/datatables/Responsive-2.2.9/css/responsive.bootstrap4.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap-select/css/bootstrap-select.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/sweetalert2-theme-bootstrap4/bootstrap-4.min.css') ?>">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css" integrity="sha256-mmgLkCYLUQbXn0B1SRqzHar6dCnv9oZFPEC1g1cwlkk=" crossorigin="anonymous" />
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@100;300;400&family=Mate+SC&family=Open+Sans:wght@300;400;600&family=Playfair+Display&family=Roboto:wght@100;400;500&family=Source+Sans+Pro:wght@200;300;400;600&display=swap" rel="stylesheet">

    <style type="text/css">
        #selectStudent, #selectYearLevel, #officeField, #forStudentfields, #editStudentfields, #StudentOrg, #forSemestral, #forOnlineSubmission,#forOfflineSubmission, #clearanceStatusFilter{
            display:none;
        }
        .form-control:disabled, .custom-select:disabled {
            background: white;
            color: black;
        }
        .form-group.required label:after {
            content:" *";
            color:red;
        }
        .light-border{
            border: 1px solid #CED4DA!important;
            background-color: #fff!important;
        }
        .badges{
            font-size: 14px;
            font-weight: normal;
        }
        .notif-dropdown{
            max-height: 500px;
            overflow-y: auto;
            overflow-x: hidden;
            background: #fff;
        }
        .notif-dropdown::-webkit-scrollbar {
            width: 4px;
            background: #fff;
        }
        .notif-dropdown::-webkit-scrollbar-thumb {
            background-color:#989898;
            border-radius:5px;
        }
        .swal2-title {
            font-family: "Source Sans Pro",-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol";
        }



        .modal-header-color{
            color:#fff;
            padding:9px 15px;
            border-bottom:1px solid #eee;
            background-color: #800000;
            -webkit-border-top-left-radius: 4px;
            -webkit-border-top-right-radius: 4px;
            -moz-border-radius-topleft: 4px;
            -moz-border-radius-topright: 4px;
            border-top-left-radius: 4px;
            border-top-right-radius: 4px;
        }
        .modal-title{
            font-weight: bold;
        }
        .table{
            width:100%;
            font-size: 15px;
        }
        .table th{
            color: #000;
            background-color: #E9ECEF;
        }

    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed" onload="generateReport(); forAddingReqs(); forEditingReqs(); initiateClearance();">

    <!-- WRAPPER -->
    <div class="wrapper">

        <!-- PRELOADER -->
        <div class="preloader flex-column justify-content-center align-items-center">
            <img src="<?= base_url('assets/img/loading-buffering.gif') ?>" alt="preloader" height="30" width="30">
        </div>

        <!-- NAVBAR -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
            </ul>

            <ul class="navbar-nav ml-auto">
                <!-- NOTIFICATION DROPDOWN -->
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="far fa-bell ml-1"></i>
                        <span class="badge badge-danger navbar-badge"><?= $user_notifications['total_notif'] ?></span>
                    </a>

                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right notif-dropdown">
                        <div class="dropdown-header font-weight-bold p-2" style="background-color: #800000; color: #fff;">
                            Notification
                        </div>
                        <div class="dropdown-divider"></div>
                        <?php if(empty($user_notifications['notifications'])) { ?>
                            <!-- MESSAGE START -->
                            <div class="media">
                                <div class="media-body">
                                    <p class="text-sm text-center p-2">You have no notifications yet.</p>
                                </div>
                            </div>
                            <!-- MESSAGE END -->
                        <?php } else {

                        $count = 0;
                        foreach($user_notifications['notifications'] as $notif) {
                            ?>
                            <a href="#modal-notif" class="dropdown-item" data-toggle="modal" data-target="#modal-notif" data-subject-det="<?= $notif['subject']?>" data-sender-name="<?= $notif['sender_name']?>" data-sender-role="<?= $notif['sender_role']?>" data-date-created="<?= $notif['created']?>" data-sender-mess="<?= $notif['message']?>">
                                <!-- MESSAGE START -->
                                <div class="media">
                                    <div class="media-body">
                                        <h4 class="dropdown-item-title font-weight-bold">
                                            <?= $notif['subject']?>
                                        </h4>
                                        <p class="text-sm"><?= $notif['sender_name']?></p>
                                        <p class="text-sm text-muted mt-1"><i class="far fa-clock mr-1"></i><?= $notif['ago']?></p>
                                    </div>
                                </div>
                                <!-- MESSAGE END -->
                            </a>
                            <div class="dropdown-divider"></div>
                            <?php
                            $count++;
                            if( $count == 3)
                            {
                                break;
                            }
                        } ?>
                            <a href="<?php echo base_url() ?>/Notifications" class="dropdown-item dropdown-footer">See All Notification</a>
                        <?php } ?>
                    </div>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-decoration-none" href="#" id="navbarDarkDropdownMenuLink" role="button" data-toggle="dropdown" aria-expanded="false">
                        <?= $Name; ?>
                        <img src="<?= base_url($profilePic) ?>" id="profile-pic" width="30" height="30" class="rounded-circle align-center ml-2">
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-light" aria-labelledby="navbarDarkDropdownMenuLink">
                        <li><a class="dropdown-item" href="<?= base_url('ProfileManagement') ?>"><i class="fas fa-address-card fa-fw mr-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('ProfileManagement/changePassPage') ?>"><i class="fas fa-key fa-fw mr-2"></i>Change Password</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('home/logout') ?>"><i class="fas fa-sign-out-alt fa-fw mr-2"></i>Logout</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
        <!-- /.NAVBAR -->

        <!------ MODAL FOR VIEWING NOTIFICATION ------>
        <div class="modal fade" id="modal-notif">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header modal-header-color">
                        <h5 class="modal-title font-weight-bold" id="notifSubject"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mailbox-read-info">
                            <span class="mailbox-read-time float-right" id="createdDate"></span>
                            <span class="mailbox-read-time float-left">From:</span>
                            <br>
                            <h6>
                                <strong id="senderName"></strong> <br>
                                <small id="senderRole"></small>
                            </h6>
                        </div>
                        <div class="mailbox-read-message">
                            <p id="senderMessage"></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.MODAL -->

        <!-- MAIN SIDEBAR CONTAINER -->
        <aside class="main-sidebar sidebar-dark-primary " style="background-color: #800000!important;">
            <!-- SITE LOGO -->
            <a href="<?= base_url('Dashboard') ?>" class="brand-link text-decoration-none" style="background-color: #800000!important;">
                <img src="<?= base_url('assets/img/PUPLogo.png') ?>" class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-normal" style="color: #fff!important;">PUPT SCIS</span>
            </a>

            <!-- SIDEBAR -->
            <div class="sidebar">
                <!-- SIDEBAR USER IMAGE-->
                <div class="user-panel mt-3 d-flex">
                    <div class="mt-2 image">
                        <img src="<?= base_url($profilePic) ?>" class="img-circle elevation-2" style="width: 40px; height: 40px;" alt="User Image">
                    </div>
                    <div class="info ml-1" style="line-height: normal;">
                        <!-- USER NAME -->
                        <a href="#" class="text-decoration-none text-wrap" style="color:#fff!important; "><?= $Name; ?></a>
                        <!-- USER TITLE -->
                        <p class="text-sm mt-2" style="font-size: 14px; color:#C2C7D0;"><?= session()->get('title'); ?></p>
                    </div>
                </div>

                <!-- SIDEBAR MENU -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <?= $this->renderSection("sidebar_menu"); ?>
                    </ul>
                </nav>
                <!-- /.SIDEBAR MENU -->
            </div>
            <!-- /.SIDEBAR -->
        </aside>

        <!-- CONTENT WRAPPER -->
        <div class="content-wrapper">
            <!-- CONTENT HEADER -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-1">
                        <div class="col-md-6">
                            <h3 class="font-weight-bold text-nowrap" style="color: #800000;"><?= $this->renderSection("page-title"); ?></h3>
                        </div>
                        <div class="col-md-6">
                            <ol class="breadcrumb float-md-right text-nowrap">
                                <?= $this->renderSection("breadcrumb"); ?>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <!-- MAIN CONTENT -->
            <section class="content">
                <div class="container-fluid">
                    <?= $this->renderSection("content"); ?>
                </div>
            </section>
            <!-- /.MAIN CONTENT -->
        </div>
        <!-- /.CONTENT WRAPPER -->

        <footer class="main-footer" style="font-size: 12px;">
            <div class="float-right d-none d-sm-block">
                <b>Version</b> 1.0.0
            </div>
            <strong>Copyright &copy; 2021 <a href="https://adminlte.io">PUPT SCIS</a>.</strong> All rights reserved.
        </footer>

        <!-- MODAL FOR CLEARANCE FIELDS MENU-->
        <?php if(!session()->get('Professor_access')){ ?>
        <!-- Manage Requirement-->
        <div class="modal fade" data-backdrop="static" id="manage-req-choose-field">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header modal-header-color">
                        <h5 class="modal-title">Manage Requirements</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <h5 class="font-weight-bolder">Select Clearance Field</h5>
                        <div class="list-group">
                            <?php foreach($user_fields as $field) { ?>
                            <a href="<?= base_url('Requirements/list/'.$field['field_id'].'-'.$field['position_id']);?>" class="list-group-item list-group-item-action" aria-current="true">
                                <strong><?= $field['field_name'] ?></strong>
                                <span class="font-weight-normal badge badge-info badge-pill ml-1">
                                    <?php echo $field['positions_name']?>
                                </span>
                            </a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Manage Prerequisite-->
        <div class="modal fade" data-backdrop="static" id="manage-prereq-choose-field">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header modal-header-color">
                        <h5 class="modal-title">Manage Prerequisite</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <h5 class="font-weight-bolder">Select Clearance Field</h5>
                        <div class="list-group">
                            <?php foreach($user_fields as $field) { ?>
                                <a href="<?= base_url('PreRequisites/list/'.$field['field_id'].'-'.$field['position_id'])?>" class="list-group-item list-group-item-action" aria-current="true">
                                    <strong><?= $field['field_name'] ?></strong>
                                    <span class="font-weight-normal badge badge-info badge-pill ml-1">
                                        <?php echo $field['positions_name']?>
                                    </span>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Manage Deficiency-Semestral-->
        <?php if(!empty($user_fields)) {  ?>
        <div class="modal fade" data-backdrop="static" id="manage-def-sem-choose-field">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header modal-header-color">
                        <h5 class="modal-title">Manage Deficiency (Semestral Clearance)</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <h5 class="font-weight-bolder">Select Clearance Field</h5>
                        <div class="list-group">
                            <?php foreach($user_fields as $field) { ?>
                                <a href="<?= base_url('Deficiencies/List/'.$field['field_id'].'-'.$field['position_id']); ?>" class="list-group-item list-group-item-action" aria-current="true">
                                    <strong><?= $field['field_name'] ?></strong>
                                    <span class="font-weight-normal badge badge-info badge-pill ml-1">
                                        <?php echo $field['positions_name']?>
                                    </span>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
        <!-- Submission-Semestral-->
        <?php if(!empty($user_fields)) {  ?>
        <div class="modal fade" data-backdrop="static" id="submissions-sem-choose-field">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header modal-header-color">
                        <h5 class="modal-title">Submissions (Semestral Clearance)</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <h5 class="font-weight-bolder">Select Clearance Field</h5>
                        <div class="list-group">
                            <?php foreach($user_fields as $field) { ?>
                                <a href="<?= base_url('Submissions/List/'.$field['field_id'].'-'.$field['position_id']); ?>" class="list-group-item list-group-item-action" aria-current="true">
                                    <strong><?= $field['field_name'] ?></strong>
                                    <span class="font-weight-normal badge badge-info badge-pill ml-1">
                                        <?php echo $field['positions_name']?>
                                    </span>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
        <!-- Manage Deficiency-Graduation-->
        <?php if(!empty($user_gradFields)) {  ?>
        <div class="modal fade" data-backdrop="static" id="manage-def-grad-choose-field">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header modal-header-color">
                        <h5 class="modal-title">Manage Deficiency (Graduation Clearance)</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <h5 class="font-weight-bolder">Select Clearance Field</h5>
                        <div class="list-group">
                            <?php foreach($user_gradFields as $field) { ?>
                                <a href="<?= base_url('Deficiencies/List/'.$field['field_id'].'-'.$field['position_id'].'-1'); ?>" class="list-group-item list-group-item-action" aria-current="true">
                                    <strong><?= $field['field_name'] ?></strong>
                                    <span class="font-weight-normal badge badge-info badge-pill ml-1">
                                        <?php echo $field['positions_name']?>
                                    </span>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
        <!-- Submission-Graduation-->
        <?php if(!empty($user_gradFields)) {  ?>
        <div class="modal fade" data-backdrop="static" id="submissions-grad-choose-field">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header modal-header-color">
                        <h5 class="modal-title">Submissions (Graduation Clearance)</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <h5 class="font-weight-bolder">Select Clearance Field</h5>
                        <div class="list-group">
                            <?php foreach($user_gradFields as $field) { ?>
                                <a href="<?= base_url('Submissions/List/'.$field['field_id'].'-'.$field['position_id'].'-1'); ?>" class="list-group-item list-group-item-action" aria-current="true">
                                    <strong><?= $field['field_name'] ?></strong>
                                    <span class="font-weight-normal badge badge-info badge-pill ml-1">
                                        <?php echo $field['positions_name']?>
                                    </span>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
        <!--Black List-->
        <?php if(!empty($user_fields)) {  ?>
        <div class="modal fade" data-backdrop="static" id="black-list-choose-field">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header modal-header-color">
                        <h5 class="modal-title">Black List</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <h5 class="font-weight-bolder">Select Clearance Field</h5>
                        <div class="list-group">
                            <?php foreach($user_fields as $field) { ?>
                                <a href="<?= base_url('BlackList/list/'.$field['position_id']); ?>" class="list-group-item list-group-item-action" aria-current="true">
                                    <strong><?= $field['field_name'] ?></strong>
                                    <span class="font-weight-normal badge badge-info badge-pill ml-1">
                                        <?php echo $field['positions_name']?>
                                    </span>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
        <?php } ?>
        <!--Manage Subject Deficiency(Graduation Clearance)-->
        <?php if (!empty($user_subject_resp)) { ?>
            <div class="modal fade" data-backdrop="static" id="manage-def-grad-choose-sub">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header modal-header-color">
                            <h5 class="modal-title">Manage Subject Deficiency</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body p-4">
                            <h5 class="font-weight-bolder">Select Subject</h5>
                            <div class="list-group">
                                <?php foreach($user_subject_resp as $resp) { ?>
                                    <a href="<?= base_url('RespectiveProfessors/List/'.$resp['sub_id']); ?>" class="text-uppercase list-group-item list-group-item-action" aria-current="true">
                                        <?= $resp['sub_name'] ?>
                                    </a>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
    <!-- ./WRAPPER -->


    <script src="<?= base_url('assets/popperjs/popper.min.js') ?>"></script>
    <script src="<?= base_url('assets/tippy.js/tippy-bundle.umd.js') ?>"></script>
    <script src="<?= base_url('assets/jquery/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/adminlte.min.js') ?>"></script>
    <script src="<?= base_url('assets/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/select2/js/select2.min.js') ?>"></script>
    <script src="<?= base_url('assets/dropify/js/dropify.min.js') ?>"></script>
    <script src="<?= base_url('assets/fancybox/jquery.fancybox.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/jquery.overlayScrollbars.min.js') ?>"></script>
    <script src="<?= base_url('assets/chart.js/chart.min.js') ?> "></script>
    <script src="<?= base_url('assets/datatables/jquery.dataTables.min.js') ?> "></script>
    <script src="<?= base_url('assets/datatables/dataTables.bootstrap4.min.js') ?> "></script>
    <script src="<?= base_url('assets/datatables/Responsive-2.2.9/js/dataTables.responsive.min.js') ?> "></script>
    <script src="<?= base_url('assets/datatables/Responsive-2.2.9/js/responsive.bootstrap4.min.js') ?> "></script>
    <script src="<?= base_url('assets/bootstrap-select/js/bootstrap-select.min.js') ?> "></script>
    <script src="<?= base_url('assets/sweetalert2/sweetalert2.min.js') ?> "></script>

    <?= $this->renderSection("script"); ?>
    <script>
        var activeTab = $('#tab-clearance-field-1').html();
        $('#dropdowntext').html(activeTab);

        $('#field-list .dropdown-item').on('click', function(){
            var content = $(this).html();
            $('#dropdowntext').html(content);
        });
    </script>

    <script>
        const ctx = document.getElementById('userChart').getContext('2d');
        const myChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Students', 'Clearance Officers'],
                datasets: [{
                    label: 'Users',
                    data: [122, 109],
                    backgroundColor: [
                        'rgb(228,178,37)',
                        'rgb(128,0,0)'
                    ],
                }]
            }
        });
    </script>

    <!-------------- ALERT MESSAGE FOR UNASSIGNED CLEARANCE OFFICERS (sweetalert2) -------------->
    <script>
        <?php if(!$activeClearance) { ?>
        document.querySelector(".alert-mess-blacklist").addEventListener('click', function(){
            Swal.fire({
                icon: 'warning',
                title: 'You are not assigned to any clearance field',
                showConfirmButton: false,
                timer: 1500
            })
        });

        <?php } else { ?>
        document.querySelector(".alert-mess-manage-def").addEventListener('click', function(){
            Swal.fire({
                icon: 'warning',
                title: 'You are not assigned to any clearance field',
                showConfirmButton: false,
                timer: 1500
            })
        });

        document.querySelector(".alert-mess-submissions").addEventListener('click', function(){
            Swal.fire({
                icon: 'warning',
                title: 'You are not assigned to any clearance field',
                showConfirmButton: false,
                timer: 1500
            })
        });
        <?php } ?>

    </script>

    <script>
        document.querySelector(".alert-mess-grad-manage-def").addEventListener('click', function(){
            Swal.fire({
                icon: 'warning',
                title: 'You are not assigned to any clearance field that is currently active for graduation clearance',
                showConfirmButton: false,
                timer: 1500
            })
        });
    </script>

    <script>
        document.querySelector(".alert-mess-grad-submissions").addEventListener('click', function(){
            Swal.fire({
                icon: 'warning',
                title: 'You are not assigned to any clearance field that is currently active for graduation clearance',
                showConfirmButton: false,
                timer: 1500
            })
        });
    </script>

    <script>
        document.querySelector(".alert-mess-prerequisite").addEventListener('click', function(){
            Swal.fire({
                icon: 'warning',
                title: 'You are not assigned to any clearance field',
                showConfirmButton: false,
                timer: 1500
            })
        });
    </script>

    <script>
        document.querySelector(".alert-mess-req").addEventListener('click', function(){
            Swal.fire({
                icon: 'warning',
                title: 'You are not assigned to any clearance field',
                showConfirmButton: false,
                timer: 1500
            })
        });
    </script>

    <script>
        document.querySelector(".alert-mess-record").addEventListener('click', function(){
            Swal.fire({
                icon: 'warning',
                title: 'You do not have any clearance record',
                showConfirmButton: false,
                timer: 1500
            })
        });
    </script>
    <!------------------------------------------------------------------------------------------->

    <!----------------------------- SUCCESS MESSAGE (sweetalert2) ---------------------------->
    <script>
        $(document).ready(function() {
            <?php if(session()->getFlashdata('success_messages')){
                $messages = session()->get('success_messages');
                foreach($messages as $mess) { ?>
                    var message = <?php echo json_encode($mess); ?>;
            <?php } ?>
                Swal.fire({
                    icon: 'success',
                    title: message,
                    showConfirmButton: false,
                    timer: 2500
                })
            <?php } ?>
        } );
    </script>




    <script>
        tippy('#view', {
            content: 'View',
            followCursor: true,
        });
        tippy('#edit', {
            content: 'Edit',
            followCursor: true,
        });
        tippy('#delete', {
            content: 'Delete',
            followCursor: true,
        });
    </script>

    <script>
        $('.dropify').dropify({
            // custom messages
            messages: {
                'default': 'Drag and drop a file here or click',
                'replace': 'Drag and drop or click to replace',
                'remove': 'Remove',
                'error': 'Sorry, this file is too large'
            },

            // custom template
            tpl: {
                wrap: '<div class="dropify-wrapper"></div>',
                message: '<div class="dropify-message"><span><i class="fas fa-cloud-upload-alt fa-3x"></i></span> <p style="font-size: 14px;">{{ default }}</p></div>',
                preview: '<div class="dropify-preview"><span class="dropify-render"></span><div class="dropify-infos"><div class="dropify-infos-inner"><p class="dropify-infos-message">{{ replace }}</p></div></div></div>',
                filename: '<p class="dropify-filename"><span class="file-icon"></span> <span class="dropify-filename-inner"></span></p>',
                clearButton: '<button type="button" class="dropify-clear">{{ remove }}</button>',
                error: '<p class="dropify-error">{{ error }}</p>'
            }
        });
    </script>


    <script>
        function generateReport(){
            var report = document.getElementById("selectedReport");

            if (report.value){
                document.getElementById("clearanceStatusFilter").style.display="block";

                if(report.value == "clearanceFieldStatus")
                {
                    document.getElementById("schoolYear-set").style.display="none";

                    document.getElementById("clearanceField-set").style.display="block";
                    document.getElementById("clearancePeriod-set").style.display="block";
                    document.getElementById("YearLevel-set").style.display="block";
                }
                else if(report.value == "clearanceFormsStatus" || report.value == "clearanceForms")
                {
                    document.getElementById("clearanceField-set").style.display="none";
                    document.getElementById("schoolYear-set").style.display="none";

                    document.getElementById("clearancePeriod-set").style.display="block";
                    document.getElementById("YearLevel-set").style.display="block";
                }
                else if(report.value == "graduationClearances")
                {
                    document.getElementById("clearanceField-set").style.display="none";
                    document.getElementById("clearancePeriod-set").style.display="none";
                    document.getElementById("YearLevel-set").style.display="none";

                    document.getElementById("schoolYear-set").style.display="block";
                }
            }
            else {
                document.getElementById("clearanceStatusFilter").style.display="none";
            }
        }
    </script>

    <script>
        function addUserForm(){
            var userRole = document.getElementById("UserRole");

            if (userRole.value == "3" || userRole.value == "Student"){
                document.getElementById("forStudentfields").style.display="block";
            }
            else
            {
                document.getElementById("forStudentfields").style.display="none";
            }
        }
    </script>

    <script>
        window.onload = function(){
            var selectedVal = $('#UserRole :selected').text();
            if (selectedVal == "Student"){
                document.getElementById("editStudentfields").style.display="block";
            }
            else {
                document.getElementById("editStudentfields").style.display="none";
            }
        };
        function editUserInfo(){
            var selectedVal = $('#UserRole :selected').text();


            if (selectedVal == "Student"){
                document.getElementById("editStudentfields").style.display="block";
            }
            else
            {
                document.getElementById("editStudentfields").style.display="none";
            }
        }
    </script>

    <script> //For Managing Registrations
        $('#user-verify').on('show.bs.modal', function(e) {
            $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
            var name = $(e.relatedTarget).data('reg-name');
            var str = "" + name;
            $('#acceptName').html(str);
          });

        $('#delete-unverified').on('show.bs.modal', function(e) {
            $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
            var name = $(e.relatedTarget).data('reg-name');
            var str = "" + name;
            $('#rejectName').html(str);
          });
    </script>

    <script>
        function initiateClearance(){
            var clearanceType = document.getElementById("clearanceType");

            // if (clearanceType.value == "1"){
            //     document.getElementById("forSemestral").style.display="block";
            // }
            // else{
            //     document.getElementById("forSemestral").style.display="none";
            // }
        }
    </script>

    <script>
        function forAddingReqs(){
            var submissionType = document.getElementById("SubmissionType");

            if (submissionType.value == "1"){
                document.getElementById("forOnlineSubmission").style.display="block";
                document.getElementById("forOfflineSubmission").style.display="none";
            }
            else if(submissionType.value == "0")
            {
                document.getElementById("forOnlineSubmission").style.display="none";
                document.getElementById("forOfflineSubmission").style.display="block";
            }
            else
            {
                document.getElementById("forOnlineSubmission").style.display="none";
                document.getElementById("forOfflineSubmission").style.display="none";
            }
        }

        function forEditingReqs(){
            var EditSubmissionType = document.getElementById("EditSubmissionType");
            console.log(EditSubmissionType.value);

            if (EditSubmissionType.value === "1"){
                document.getElementById("forEditingFileType").style.display="block";
            }
            else
            {
                document.getElementById("forEditingFileType").style.display="none";
            }

        }

        $('#edit-requirement').on('show.bs.modal', function(e) {
            var req_id = $(e.relatedTarget).data('req-id');
            var req_name = $(e.relatedTarget).data('req-name');
            var field_id = $(e.relatedTarget).data('field-id');
            var sub_id = $(e.relatedTarget).data('sub-id');
            var file_id = $(e.relatedTarget).data('file-id');
            var req_ins = $(e.relatedTarget).data('req-ins');

            $(e.currentTarget).find('input[name="reqID"]').val(req_id);
            $(e.currentTarget).find('input[name="reqName"]').val(req_name);
            $(e.currentTarget).find('select[name="reqFieldID"]').val(field_id);
            $(e.currentTarget).find('select[name="EditSubmissionType"]').val(sub_id);
            $(e.currentTarget).find('select[name="EditFileType"]').val(file_id);
            $(e.currentTarget).find('textarea[name="reqIns"]').val(req_ins);
            $(e.currentTarget).find('input[name="subTypeHolder"]').val(sub_id);

            forEditingReqs(sub_id);
        });

        $('#requirement-delete').on('show.bs.modal', function(e) {
            $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
            var field_name = $(e.relatedTarget).data('delete-name');
            var str = "" + field_name;
            $('#deleteName').html(str);
        });
    </script>

    <script> //For Managing Clearance Field Modal
        $('#edit-clearance-field').on('show.bs.modal', function(e) {
            var field_id = $(e.relatedTarget).data('field-id');
            var field_name = $(e.relatedTarget).data('field-name');
            var field_desc = $(e.relatedTarget).data('field-desc');
            var field_type = $(e.relatedTarget).data('field-type');

            $(e.currentTarget).find('input[name="fieldID"]').val(field_id);
            $(e.currentTarget).find('input[name="fieldName"]').val(field_name);
            $(e.currentTarget).find('textarea[name="fieldDesc"]').val(field_desc);
            $(e.currentTarget).find('select[name="fieldType"]').val(field_type);
        });
    </script>

    <script> //For Managing Positions Modal
        $('#edit-position').on('show.bs.modal', function(e) {
            var pos_id = $(e.relatedTarget).data('position-id');
            var pos_name = $(e.relatedTarget).data('position-name');
            var field_id = $(e.relatedTarget).data('field-id');

            $(e.currentTarget).find('input[name="posID"]').val(pos_id);
            $(e.currentTarget).find('input[name="posName"]').val(pos_name);
            $(e.currentTarget).find('select[name="posFieldID"]').val(field_id);
        });

        $('#assign-officer').on('show.bs.modal', function(e) {
            var pos_id = $(e.relatedTarget).data('position-id');

            $(e.currentTarget).find('input[name="posID"]').val(pos_id);
        });

         $('#assign-officer2').on('show.bs.modal', function(e) {
            var pos_id = $(e.relatedTarget).data('position-id');
            var org_id = $(e.relatedTarget).data('org-id');

            $(e.currentTarget).find('input[name="posID"]').val(pos_id);
            $(e.currentTarget).find('input[name="orgID"]').val(org_id);
        });

        $('#edit-assigned-officer').on('show.bs.modal', function(e) {
            var coPos_id = $(e.relatedTarget).data('pos-id');
            var off_id = $(e.relatedTarget).data('officer-id');

            $(e.currentTarget).find('input[name="coPosID"]').val(coPos_id);
            $(e.currentTarget).find('select[name="clearanceofficer"]').val(off_id);
        });

         $('#edit-assigned-officer2').on('show.bs.modal', function(e) {
            var coPos_id = $(e.relatedTarget).data('pos-id');
            var off_id = $(e.relatedTarget).data('officer-id');
            var co_org_id = $(e.relatedTarget).data('org-id');

            $(e.currentTarget).find('input[name="coPosID"]').val(coPos_id);
            $(e.currentTarget).find('input[name="coOrgID"]').val(co_org_id);
            $(e.currentTarget).find('select[name="clearanceofficer"]').val(off_id);
        });
    </script>

 

    <script> //For Managing Organizations Modal
        $('#edit-organization').on('show.bs.modal', function(e) {
            var org_id = $(e.relatedTarget).data('org-id');
            var org_name = $(e.relatedTarget).data('org-name');
            var org_type_id = $(e.relatedTarget).data('type-id');

            $(e.currentTarget).find('input[name="orgID"]').val(org_id);
            $(e.currentTarget).find('input[name="orgName"]').val(org_name);
            $(e.currentTarget).find('select[name="orgTypeID"]').val(org_type_id);
        });
    </script>

    <script> //For Managing Courses Modal
        $('#edit-course').on('show.bs.modal', function(e) {
            var course_id = $(e.relatedTarget).data('course-id');
            var course_code = $(e.relatedTarget).data('course-code');
            var course_name = $(e.relatedTarget).data('course-name');
            var course_max = $(e.relatedTarget).data('course-max');
            var org_id = $(e.relatedTarget).data('org-id');        

            $(e.currentTarget).find('input[name="courseID"]').val(course_id);
            $(e.currentTarget).find('input[name="courseName"]').val(course_name);
            $(e.currentTarget).find('input[name="courseCode"]').val(course_code);
            $(e.currentTarget).find('select[name="maxYear"]').val(course_max);
            $(e.currentTarget).find('select[name="orgID"]').val(org_id);
        });
    </script>

    <script>
        $('#tag-indiv-deficiency').on('show.bs.modal', function(e) {
            var ent_id = $(e.relatedTarget).data('ent-id');

            $(e.currentTarget).find('input[name="entID"]').val(ent_id);
        });

    </script>

    <script> //For Notifications Modal
        $('#modal-notif1').on('show.bs.modal', function(e) {
            var subject1 = $(e.relatedTarget).data('subject-det1');
            var name1 = $(e.relatedTarget).data('sender-name1');
            var role1 = $(e.relatedTarget).data('sender-role1');
            var created1 = $(e.relatedTarget).data('date-created1');
            var message1 = $(e.relatedTarget).data('sender-mess1');

            var notif_subject1 = "" + subject1;
            $('#notifSubject1').html(notif_subject1);

            var sender_name1 = "" + name1;
            $('#senderName1').html(sender_name1);

            var sender_role1 = "" + role1;
            $('#senderRole1').html(sender_role1);

            var created_date1 = "" + created1;
            $('#createdDate1').html(created_date1);

            var sender_message1 = "" + message1;
            $('#senderMessage1').html(sender_message1);

        });

        $('#modal-notif').on('show.bs.modal', function(e) {
            var subject = $(e.relatedTarget).data('subject-det');
            var name = $(e.relatedTarget).data('sender-name');
            var role = $(e.relatedTarget).data('sender-role');
            var created = $(e.relatedTarget).data('date-created');
            var message = $(e.relatedTarget).data('sender-mess');

            var notif_subject = "" + subject;
            $('#notifSubject').html(notif_subject);

            var sender_name = "" + name;
            $('#senderName').html(sender_name);

            var sender_role = "" + role;
            $('#senderRole').html(sender_role);

            var created_date = "" + created;
            $('#createdDate').html(created_date);

            var sender_message = "" + message;
            $('#senderMessage').html(sender_message);

        });
    </script>

    <!-- For Managing Student's Deficiency -->
    <script>
        $('#def-remove').on('show.bs.modal', function(e) {
            $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
            var field_name = $(e.relatedTarget).data('delete-name');
            var str = "" + field_name;
            $('#deleteName').html(str);
          });
    </script>

    <!-- For Submiting Requirements -->
    <script>
        $('#online-submission').on('show.bs.modal', function(e) {
            var deficiency_id = $(e.relatedTarget).data('def-id');
            var deficiency_name = $(e.relatedTarget).data('def-nm');
            var file_name = $(e.relatedTarget).data('file-type');
            var file_format = $(e.relatedTarget).data('file-format');

            $(e.currentTarget).find('input[name="defID"]').val(deficiency_id);
            $(e.currentTarget).find('input[name="format"]').val(file_format);
            $('#deficiencyName').html(deficiency_name);

            var string = file_name + "(" + file_format + ")";
            $('#fileFormat').html(string);
        });

        $('#personal-submission').on('show.bs.modal', function(e) {
            var deficiency_ins = $(e.relatedTarget).data('def-ins');

            $('#instructions').html(deficiency_ins);

        });
    </script>

    <!-- Reason for Rejection -->
    <script>
        $('#reason-rejection').on('show.bs.modal', function(e) {
            var reason = $(e.relatedTarget).data('reason-rejection');

            $('#reason').html(reason);

        });
    </script>

    <script> //Managing Submitted Requirements
        $('#sub-reject').on('show.bs.modal', function(e) {
            $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));

            var studName = $(e.relatedTarget).data('sub-owner');
            var name = "" + studName;
            $('#studName').html(name);

            var reqName = $(e.relatedTarget).data('sub-requirement');
            var req = "" + reqName;
            $('#reqName').html(req);
          });
    </script>

    <script>//for Black List
        $('#delete-listed-student').on('show.bs.modal', function(e) {
            $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
            var stud_name = $(e.relatedTarget).data('stud-name');
            var str = "" + stud_name;
            $('#studName').html(str);

            var stud_def = $(e.relatedTarget).data('stud-def');
            var str2 = "" + stud_def;
            $('#studDef').html(str2);
          });
    </script>

    <script>//for PreRequisites
        $('#prerequisite-delete').on('show.bs.modal', function(e) {
            $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
            var stud_name = $(e.relatedTarget).data('req-name');
            var str = "" + stud_name;
            $('#reqName').html(str);
          });
    </script>

    <script> //Extend Due Dte of Clearance Period
        $('#extend-due').on('show.bs.modal', function(e) {
            var dueDate = $(e.relatedTarget).data('due-date');
            var pID = $(e.relatedTarget).data('clearance-period');


            $(e.currentTarget).find('input[name="pID"]').val(pID);
            $(e.currentTarget).find('input[name="currentDueDate"]').val(dueDate);
            $(e.currentTarget).find('input[name="clearanceDueDate"]').val(dueDate);
          });
    </script>

    <script> //For Finalizing or marking clearance as complete
        $('#sign-completion').on('show.bs.modal', function(e) {
            $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
            var name = $(e.relatedTarget).data('stud-name');
            var str = "" + name;
            $('#acceptName').html(str);
          });
    </script>
    
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const toggleConfirmPassword = document.querySelector('#toggleConfirmPassword');
        const toggleCurrentPassword = document.querySelector('#toggleCurrentPassword');
        const password = document.querySelector('#password');
        const confirmpass = document.querySelector('#confirmpass');
        const currentpass = document.querySelector('#currentpass');
        togglePassword.addEventListener('click', function (e) {
            // toggle the type attribute
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            // toggle the eye / eye slash icon
            this.classList.toggle('fa-eye-slash');
        });

        toggleConfirmPassword.addEventListener('click', function (e) {
            // toggle the type attribute
            const type = confirmpass.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmpass.setAttribute('type', type);
            // toggle the eye / eye slash icon
            this.classList.toggle('fa-eye-slash');
        });

        toggleCurrentPassword.addEventListener('click', function (e) {
            // toggle the type attribute
            const type = currentpass.getAttribute('type') === 'password' ? 'text' : 'password';
            currentpass.setAttribute('type', type);
            // toggle the eye / eye slash icon
            this.classList.toggle('fa-eye-slash');
        });
    </script>

    <script>
        $('.select2bs4').select2({
            theme: 'bootstrap4',
            minimumResultsForSearch: -1,
            placeholder: 'Select an option',
            width: 'resolve'
        })
    </script>
    <script>
        $('.select2bs4-search').select2({
            theme: 'bootstrap4',
            placeholder: 'Select an option',
            minimumResultsForSearch: 5
        })
    </script>

    <script>
        function displayImage(e) {
            if (e.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e){
                    document.querySelector('#profileDisplay').setAttribute('src', e.target.result);
                }
                reader.readAsDataURL(e.files[0]);
            }
        }
    </script>

    <script>
        function del(element){
            var name = element.dataset.deleteName;
            const href = element.dataset.href;

            Swal.fire({
                title: 'Confirm Action',
                text: 'Are you sure you want to delete '+ name.trim() + '?',
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
</body>
</html>

