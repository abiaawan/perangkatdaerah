<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perangkat Daerah - <?= $title ?></title>
    <link rel="shortcut icon" href="<?= base_url("assets") ?>/static/images/logo/logo.ico" type="image/x-icon">
    <link rel="stylesheet" href="<?= base_url("assets") ?>/compiled/css/app.css">
    <link rel="stylesheet" href="<?= base_url("assets") ?>/compiled/css/app-dark.css">
    <link rel="stylesheet" href="<?= base_url("assets") ?>/custom.css">
    <link rel="stylesheet" href="<?= base_url("assets") ?>/compiled/css/auth.css">
    <link href="<?= base_url("assets") ?>/extensions/sweetalert2/sweetalert2.min.css" rel="stylesheet" />
    <script src="<?= base_url("assets") ?>/extensions/jquery/jquery.min.js"></script>
</head>

<body>
    <script src="<?= base_url("assets") ?>/static/js/initTheme.js"></script>
    <div id="auth">

        <div class="row h-100">
            <div class="col-lg-5 col-12">
                <div id="auth-left">
                    <div class="auth-logo text-center">
                        <a href="index.html"><img src="<?= base_url("assets") ?>/static/images/logo/logo.png" alt="Logo"></a>
                        <p>Perangkat Daerah</p>
                    </div>
                    <p class="auth-subtitle mb-5"></p>
                    <form action="<?= base_url("login/send_auth") ?>" method="POST">
                        <div class="form-group position-relative has-icon-left mb-4">
                          <input type="hidden" name="hushbot" value="1">
                          <input type="text" name="username" class="form-control form-control-xl" placeholder="Username" required>
                          <div class="form-control-icon">
                            <i class="bi bi-person"></i>
                        </div>
                    </div>
                    <div class="form-group position-relative has-icon-left mb-4">
                        <input type="password" name="password" class="form-control form-control-xl" placeholder="Password" required>
                        <div class="form-control-icon">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                    </div>
                    <div class="form-check form-check-lg d-flex align-items-end">
                        <input class="form-check-input me-2" type="checkbox" value="" id="flexCheckDefault">
                        <label class="form-check-label text-gray-600" for="flexCheckDefault">
                            Simpan password
                        </label>
                    </div>
                    <button class="btn btn-primary btn-block btn-lg shadow-lg mt-5"><i class="bi bi-box-arrow-in-right"></i> Masuk</button>
                </form>
                <div class="text-center mt-5 text-lg fs-5">
                    <p><a class="font-bold" href="<?= base_url("tipelogi") ?>"><i class="bi bi-map"></i> Lihat Data Tipelogi</a></p>
                </div>
            </div>
        </div>
        <div class="col-lg-7 d-none d-lg-block">
            <div id="auth-right">

            </div>
        </div>
    </div>

</div>
</body>
<script src="<?= base_url("assets") ?>/extensions/sweetalert2/sweetalert2.min.js" defer></script>
<script type="text/javascript">

    $( document ).ready(function() {
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
</html>