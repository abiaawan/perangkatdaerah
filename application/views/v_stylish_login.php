<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perangkat Daerah - <?= $title ?></title>
    <link rel="shortcut icon" href="<?= base_url("assets") ?>/static/images/logo/logo.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="<?= base_url("assets") ?>/extensions/sweetalert2/sweetalert2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?= base_url("assets") ?>/custom_login.css">
    <style>

    </style>
</head>
<body class="p-sm-3 p-0 p-md-4">
    <div class="login-container px-sm-3 px-3 px-md-4">
        <div id="messageContainer"></div>

        <div class="logo-container">
            <img src="<?= base_url("assets") ?>/static/images/logo/logo.png" alt="Brand Logo">
            <h3>Perangkat Daerah</h3>
        </div>
        <form action="<?= base_url("login/send_auth") ?>" method="POST">
            <input type="hidden" name="hushbot" value="1">
            <div class="mb-3">
                <label for="username" class="form-label"><i class="bi bi-person-fill"></i> Username</label>
                <input type="text" class="form-control shadow-none" name="username" id="username" placeholder="Username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label"><i class="bi bi-key-fill"></i> Password</label>
                <div class="input-group password-input-group">
                    <input type="password" class="form-control shadow-none" name="password" id="password" placeholder="Password" required>
                    <span class="input-group-text" id="togglePassword">
                        <i class="bi bi-eye-slash-fill"></i>
                    </span>
                </div>
            </div>
            <div class="mb-4">
                <label for="tahun" class="form-label"><i class="bi bi-calendar-fill"></i> Pilih Tahun</label>
                <select class="form-select shadow-none" name="tahun" id="tahun" required>
                    <?php for ($i=date("Y")-1; $i <= 2024; $i++) { ?>
                        <option value="<?= $i ?>" <?= $i==date("Y")-1 ? "selected" : "" ?>><?= $i ?></option>
                    <?php } ?>
                    <option value="2025">2025</option>
                    <option value="2026">2026</option>
                </select>
            </div>
            <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-box-arrow-in-right" class="g-recaptcha" 
                    data-sitekey="reCAPTCHA_site_key" 
                    data-callback='onSubmit' 
                    data-action='submit'></i> Masuk</button>
                </div>
            </form>
            <div class="text-center mt-5 text-lg fs-5">
                <p><a class="fw-bold text-decoration-none" href="<?= base_url("tipelogi") ?>"><i class="bi bi-map"></i> Lihat Data Tipelogi</a></p>
            </div>
        </div>
        <script src="https://www.google.com/recaptcha/api.js"></script>
        <script>
            function onSubmit(token) {
                document.getElementById("demo-form").submit();
            }
        </script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="<?= base_url("assets") ?>/extensions/sweetalert2/sweetalert2.min.js" defer></script>
        <script src="https://www.google.com/recaptcha/api.js?render=<?= $this->config->item("recaptcha_site_key") ?>"></script>
        <script type="text/javascript">

            $( document ).ready(function() {
                grecaptcha.ready(function() {
                    grecaptcha.execute('<?= $this->config->item("recaptcha_site_key") ?>', {action: 'login'})
                    .then(function(token) {
                      document.getElementById('g-recaptcha-response').value = token;
                  });
                });
                $('#togglePassword').on('click', function() {
                    const passwordField = $('#password');
                    const passwordFieldType = passwordField.attr('type');
                    if (passwordFieldType === 'password') {
                        passwordField.attr('type', 'text');
                        $(this).find('i').removeClass('bi-eye-slash-fill').addClass('bi-eye-fill');
                    } else {
                        passwordField.attr('type', 'password');
                        $(this).find('i').removeClass('bi-eye-fill').addClass('bi-eye-slash-fill');
                    }
                });
                $('form').on('submit', function(e) {
                    if (!$(this).data('submitted')) {
                        $(this).data('submitted', true);
                        $(this).find("button").addClass('disabled');
                    }
                    else {
                        e.preventDefault();
                    }
                });
                $("input[name='hushbot']").val("67b279476cef18ce0be52bb1f7945d46bdd9e0ba1698a893caf470e7409ad62f");
                <?php if (isset($_SESSION['error']) ? $_SESSION['error'] : '') { ?>
                    const Toast = Swal.mixin({
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                          toast.addEventListener("mouseenter", Swal.stopTimer);
                          toast.addEventListener("mouseleave", Swal.resumeTimer);
                      },
                  });
                    Toast.fire({
                        icon: "error",
                        title: "<?=$_SESSION['error'] ?>",
                    });
                    <?php unset($_SESSION['error']);
                } ?>

                <?php if (isset($_SESSION['success']) ? $_SESSION['success'] : '') { ?>
                    const Toast = Swal.mixin({
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                          toast.addEventListener("mouseenter", Swal.stopTimer);
                          toast.addEventListener("mouseleave", Swal.resumeTimer);
                      },
                  });
                    Toast.fire({
                        icon: "success",
                        title: "<?=$_SESSION['success'] ?>",
                    });
                    <?php unset($_SESSION['success']);
                } ?>
            });
        </script>
    </body>
    </html>
