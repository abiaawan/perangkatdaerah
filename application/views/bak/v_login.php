<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!doctype html>
<!--
* Tabler - Premium and Open Source dashboard template with responsive and high quality UI.
* @version 1.0.0-beta19
* @link https://tabler.io
* Copyright 2018-2023 The Tabler Authors
* Copyright 2018-2023 codecalm.net Paweł Kuna
* Licensed under MIT (https://github.com/tabler/tabler/blob/master/LICENSE)
-->
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
  <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
  <title>SIPATENBMN - <?= $title ?></title>
  <!-- CSS files -->
  <link rel="icon" type="image/x-icon" href="<?= base_url("assets/static/logo.png") ?>">
  <link href="<?= base_url("assets/") ?>css/tabler.min.css?1684106062" rel="stylesheet"/>
  <link href="<?= base_url("assets/") ?>css/custom.css?1232233999" rel="stylesheet"/>
  <link href="<?= base_url("assets/") ?>libs/fontawesome/css/all.min.css" rel="stylesheet" />
  <link href="<?= base_url("assets/") ?>libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" />
  <script src="<?= base_url("assets/") ?>js/jquery-3.7.1.min.js"></script>
</head>
<body  class=" d-flex flex-column">
  <div class="page page-center bg-dark" style="background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url('<?= base_url("assets/") ?>static/bg.avif'); background-repeat: no-repeat; background-size: cover;">
    <div class="container container-tight py-4">
      <div class="text-center mb-4 px-5">
        <a href="<?= base_url("login") ?>" class="navbar-brand navbar-brand-autodark"><img src="<?= base_url("assets/") ?>static/logo.png" height="100" alt="" style="object-fit: scale-down !important;"></a>
        <h1 class="h1 text-center mb-4 text-white">SIPATENBMN</h1>
      </div>
      <div class="card card-md">
        <div class="card-body">
          <h2 class="h2 text-center mb-4">Login to your account</h2>
          <form action="<?= base_url("login/send_auth") ?>" method="POST">
            <div class="mb-3">
              <label class="form-label required">Username</label>
              <input type="hidden" name="redirect" value="<?= isset($_GET['redirect']) ? htmlspecialchars($_GET['redirect'], ENT_QUOTES, 'UTF-8') : "" ?>">
              <input type="hidden" name="hushbot" value="1">
              <input type="text" name="username" class="form-control shadow-none" placeholder="Enter username" autocomplete="off" required>
            </div>
            <div class="mb-2">
              <label class="form-label required">
                Password
                <span class="form-label-description">
                  <a tabindex="-1" href="<?= base_url("login/forgot_password") ?>">Forgot password?</a>
                </span>
              </label>
              <div class="input-group input-group-flat shadow-none">
                <input type="password" name="password" class="form-control shadow-none"  placeholder="Enter password"  autocomplete="off" required>
                <span class="input-group-text">
                  <a tabindex="-1" href="javascript:;" id="toggle-password" class="link-primary shadow-none" title="Show password" data-bs-toggle="tooltip">
                    <i class="fa-solid fa-eye-slash"></i>
                  </a>
                </span>
              </div>
            </div>
            <div class="form-footer">
              <button type="submit" class="btn btn-primary w-100">Sign in</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- Libs JS -->
  <!-- Tabler Core -->
  <script src="<?= base_url("assets/") ?>js/tabler.min.js?1684106062" defer></script>
  <script src="<?= base_url("assets/") ?>libs/sweetalert2/sweetalert2.min.js" defer></script>
</body>
</html>
<script type="text/javascript">
	$( document ).ready(function() {
    Swal("asdasdas");
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
    $("#toggle-password").click(function() {
     $(this).find("i").toggleClass("fa-eye fa-eye-slash");
     var input = $("input[name='password']");
     if (input.attr("type") == "password") {
      input.attr("type", "text");
    } else {
      input.attr("type", "password");
    }
  });
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