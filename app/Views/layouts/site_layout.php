<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $page_title; ?></title>

    <link rel="icon" type="image/png" href="<?= base_url('assets/img/PUPLogo.png') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/font-awesome/css/all.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/select2/css/select2.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/select2/css/select2-bootstrap4.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/sweetalert2-theme-bootstrap4/bootstrap-4.min.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css" integrity="sha256-mmgLkCYLUQbXn0B1SRqzHar6dCnv9oZFPEC1g1cwlkk=" crossorigin="anonymous" />
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@100;300;400&family=Mate+SC&family=Open+Sans:wght@300;400;600&family=Playfair+Display&family=Roboto:wght@100;400;500&family=Source+Sans+Pro:wght@200;300;400;600&display=swap" rel="stylesheet">

    <style type="text/css">
        body{
            background-image: url("<?= base_url('assets/img/bg-image.png') ?>");
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
        .required:after {
            content:" *";
            color: red;
        }
        nav {
            background-color: #800000;
            top:0;
        }
        footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            text-align: center;
        }
        .short-site-title {
            display: none;
        }
        a > .card:hover{
            background-color: #800000;
            color: #fff;
        }
        a > .card:hover > i {
            color: #fff;
        }
        .card > i {
            color: #800000;
            font-size: 6em;
        }

        #page-container{
            position: relative;
            min-height: 100vh;
        }
        #student{
            display:none;
        }

        @media (max-width: 768px) {
            .navbar {
                min-height: 57px;
                max-height: 57px;
            }
            .site-logo {
                width: 30px;
                height: 30px;
            }
            .site-title {
                display: none;
            }
            .short-site-title {
                display: block;
            }
        }
    </style>
</head>
<body>

    <div id="page-container">
        <nav class="navbar shadow-sm">
            <a href="<?= base_url() ?>" style="text-decoration: none;">
                <div class="nav-brand d-flex align-items-center">
                    <img class="ms-5 site-logo" src="<?= base_url('assets/img/PUPLogo.png') ?>" width="60px" height="60px" alt="pup_logo">
                    <span class="text-white my-2 ms-4 align-middle site-title">
                        <h5 class="mb-0 mt-1" style="font-family: 'Mate SC'; color: white;">Polytechnic University of the Philippines - Taguig Branch </h5>
                        <h1 class="fs-3" style="font-family: 'Roboto'; color: white;">Student Clearance Information System</h1>
                    </span>
                    <span class="navbar-brand text-white ms-3 align-middle short-site-title">
                        PUPT SCIS
                    </span>
                </div>
            </a>
        </nav>

        <div id="content">
                <?= $this->renderSection("content"); ?>
        </div>

        <footer class="border-top" style="background-color: #fdfdfd;">
            <p class="text-center text-muted m-2 py-2" style="font-size: 12px; font-family: 'Roboto';"> Copyright &copy; 2021 <a href="<?= base_url() ?>">PUPT SCIS</a>. All rights reserved. </p>
        </footer>
    </div>
    
    <script src="<?= base_url('assets/jquery/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/sweetalert2/sweetalert2.min.js') ?> "></script>
    <script src="<?= base_url('assets/select2/js/select2.min.js') ?>"></script>
    <script src="<?= base_url('assets/bootstrap-validate-2.2.0/bootstrap-validate.js') ?>"></script>



    <?= $this->renderSection("script"); ?>

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
        const togglePassword = document.querySelector('#togglePassword');
        const toggleConfirmPassword = document.querySelector('#toggleConfirmPassword');
        const password = document.querySelector('#password');
        const confirmpass = document.querySelector('#confirmpass');
        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });

        toggleConfirmPassword.addEventListener('click', function (e) {
            const type = confirmpass.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmpass.setAttribute('type', type);
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
        $(document).ready(function() {
            <?php if(session()->getFlashdata('success_email')){
            $messages = session()->get('success_email');
            foreach($messages as $mess) { ?>
            var message = <?php echo json_encode($mess); ?>;
            <?php } ?>

            Swal.fire({
                icon: 'success',
                title: message,
                text: 'We have successfully sent you the reset link. Kindly check your email. If it doesn\'t arrive soon, check your spam folder or request a new reset link.',
                showConfirmButton: true
            })
            <?php } ?>
        });
    </script>

    <script>
        $(document).ready(function() {
            <?php if(session()->getFlashdata('success_register')){
            $messages = session()->get('success_register');
            foreach($messages as $mess) { ?>
            var message = <?php echo json_encode($mess); ?>;
            <?php } ?>

            Swal.fire({
                icon: 'success',
                title: message,
                text: 'The admin is verifying your account registration. You will receive an email once your account registration has been approved.',
                showConfirmButton: true
            })
            <?php } ?>
        });
    </script>
</body>
</html>