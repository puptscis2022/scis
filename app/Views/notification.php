<?= $this->extend("layouts/user_account_layout"); ?>

<?= $this->section("sidebar_menu"); ?>
    <?= $this->include('test_menu') ?>
<?= $this->endSection(); ?>

<?= $this->section("page-title"); ?>
    Notification
<?= $this->endSection(); ?>

<?= $this->section("breadcrumb"); ?>
    <li class="breadcrumb-item"><a href="#"><i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li class="breadcrumb-item active">Notification</li>
<?= $this->endSection(); ?>

<?= $this->section("content"); ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0">
                    <?php if(empty($Notifications)) { ?>
                        <h6 class="text-center p-3">You have no notifications yet.</h6>
                    <?php } else { ?>
                        <div class="table-responsive mailbox-messages">
                            <table class="table table-hover">
                                <tbody>
                                    <?php
                                        $count = 1;
                                        foreach($Notifications as $notif) {
                                    ?>
                                        <tr href="#modal-notif1" data-toggle="modal" data-target="#modal-notif1" data-subject-det1="<?= $notif['subject']?>" data-sender-name1="<?= $notif['sender_name']?>" data-sender-role1="<?= $notif['sender_role']?>" data-date-created1="<?= $notif['created']?>" data-sender-mess1="<?= $notif['message']?>" style="cursor: pointer;">
                                            <td class="mailbox-name"><?= $notif['sender_name']?></td>
                                            <td class="mailbox-subject"><b><?= $notif['subject']?></b></td>
                                            <td class="mailbox-date"><?= $notif['ago']?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <!------ MODAL FOR VIEWING NOTIFICATION ------>
    <div class="modal fade" id="modal-notif1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-color">
                    <h5 class="modal-title" id="notifSubject1"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mailbox-read-info">
                        <span class="mailbox-read-time float-right" id="createdDate1"></span>
                        <span class="mailbox-read-time float-left">From:</span> <br>
                        <h6>
                            <strong id="senderName1"></strong> <br>
                            <small id="senderRole1"></small>
                        </h6>

                    </div>
                    <div class="mailbox-read-message">
                        <p id="senderMessage1"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection(); ?>



