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
  <div class="page page-center bg-dark">
    <div class="container container-tight py-4" style="background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url('<?= base_url("assets/") ?>static/bg.avif'); background-repeat: no-repeat; background-size: cover;">
      <div class="text-center mb-4">
        <a href="<?= base_url("login") ?>" class="navbar-brand navbar-brand-autodark"><img src="<?= base_url("assets/") ?>static/logo.png" height="100" alt="" style="object-fit: scale-down !important;"></a>
        <h1 class="h1 text-center mb-4 text-white">SIPATENBMN</h1>
      </div>
      <form class="card card-md" action="<?= base_url("login/send_reset") ?>" method="POST">
        <div class="card-body">
          <h2 class="card-title text-center mb-4">Reset password</h2>
          <p class="text-muted mb-4">Enter your new password.</p>
          <div class="mb-3">
            <label class="form-label required">New Password</label>
            <input type="password" name="password" class="form-control" placeholder="Enter new password" required>
            <input type="hidden" name="token" required value="<?= $token ?>">
          </div>
          <div class="mb-3">
            <label class="form-label required">Confirm New Password</label>
            <input type="password" name="conf_password" class="form-control" placeholder="Enter the password again" required>
          </div>
          <input type="hidden" name="hushbot" value="1">
          <div class="form-footer">
            <button class="btn btn-primary w-100">
              <!-- Download SVG icon from http://tabler-icons.io/i/mail -->
              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" /><path d="M3 7l9 6l9 -6" /></svg>
              Submit
            </button>
          </div>
        </div>
      </form>
      <div class="text-center text-white mt-3">
        Forget it, <a href="<?= base_url("login") ?>">send me back</a> to the sign in screen.
      </div>
    </div>
  </div>
  <!-- Libs JS -->
  <!-- Tabler Core -->
  <script src="<?= base_url("assets/") ?>js/tabler.min.js?1684106062" defer></script>
  <script src="<?= base_url("assets/") ?>libs/sweetalert2/sweetalert2.min.js" defer></script>

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
</body>
</html>
