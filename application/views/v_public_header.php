<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perangkat Daerah - <?= $title ?></title>
    
    
    
    
    <link rel="shortcut icon" href="<?= base_url("assets") ?>/static/images/logo/logo.ico" type="image/x-icon">
    <link rel="stylesheet" href="<?= base_url("assets") ?>/compiled/css/app.css">
    <link rel="stylesheet" href="<?= base_url("assets") ?>/compiled/css/app-dark.css">
    <link href="<?= base_url("assets") ?>/extensions/sweetalert2/sweetalert2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?= base_url("assets") ?>/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css">


    <link rel="stylesheet" href="./<?= base_url("assets") ?>/compiled/css/table-datatable-jquery.css">
    <script src="<?= base_url("assets") ?>/extensions/jquery/jquery.min.js"></script>
    <link rel="stylesheet" href="<?= base_url("assets") ?>/extensions/leaflet/leaflet.css" />
    <script src="<?= base_url("assets") ?>/extensions/leaflet/leaflet.js"></script>
    <link rel="stylesheet" href="<?= base_url("assets") ?>/extensions/choices.js/public/assets/styles/choices.css">
    <link rel="stylesheet" href="<?= base_url("assets") ?>/custom.css">
</head>

<body>
    <script src="<?= base_url("assets") ?>/static/js/initTheme.js"></script>
    <div id="app">
        <div id="sidebar">
            <div class="sidebar-wrapper active">
                <div class="sidebar-header position-relative">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="logo">
                            <a href="<?= base_url("") ?>"><img src="<?= base_url("assets") ?>/static/images/logo/logo.png" alt="Logo" srcset=""></a>
                        </div>
                        <div class="theme-toggle d-flex gap-2  align-items-center mt-2">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
                            role="img" class="iconify iconify--system-uicons" width="20" height="20"
                            preserveAspectRatio="xMidYMid meet" viewBox="0 0 21 21">
                            <g fill="none" fill-rule="evenodd" stroke="currentColor" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path
                            d="M10.5 14.5c2.219 0 4-1.763 4-3.982a4.003 4.003 0 0 0-4-4.018c-2.219 0-4 1.781-4 4c0 2.219 1.781 4 4 4zM4.136 4.136L5.55 5.55m9.9 9.9l1.414 1.414M1.5 10.5h2m14 0h2M4.135 16.863L5.55 15.45m9.899-9.9l1.414-1.415M10.5 19.5v-2m0-14v-2"
                            opacity=".3"></path>
                            <g transform="translate(-210 -1)">
                                <path d="M220.5 2.5v2m6.5.5l-1.5 1.5"></path>
                                <circle cx="220.5" cy="11.5" r="4"></circle>
                                <path d="m214 5l1.5 1.5m5 14v-2m6.5-.5l-1.5-1.5M214 18l1.5-1.5m-4-5h2m14 0h2"></path>
                            </g>
                        </g>
                    </svg>
                    <div class="form-check form-switch fs-6">
                        <input class="form-check-input  me-0" type="checkbox" id="toggle-dark" style="cursor: pointer">
                        <label class="form-check-label"></label>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
                    role="img" class="iconify iconify--mdi" width="20" height="20" preserveAspectRatio="xMidYMid meet"
                    viewBox="0 0 24 24">
                    <path fill="currentColor"
                    d="m17.75 4.09l-2.53 1.94l.91 3.06l-2.63-1.81l-2.63 1.81l.91-3.06l-2.53-1.94L12.44 4l1.06-3l1.06 3l3.19.09m3.5 6.91l-1.64 1.25l.59 1.98l-1.7-1.17l-1.7 1.17l.59-1.98L15.75 11l2.06-.05L18.5 9l.69 1.95l2.06.05m-2.28 4.95c.83-.08 1.72 1.1 1.19 1.85c-.32.45-.66.87-1.08 1.27C15.17 23 8.84 23 4.94 19.07c-3.91-3.9-3.91-10.24 0-14.14c.4-.4.82-.76 1.27-1.08c.75-.53 1.93.36 1.85 1.19c-.27 2.86.69 5.83 2.89 8.02a9.96 9.96 0 0 0 8.02 2.89m-1.64 2.02a12.08 12.08 0 0 1-7.8-3.47c-2.17-2.19-3.33-5-3.49-7.82c-2.81 3.14-2.7 7.96.31 10.98c3.02 3.01 7.84 3.12 10.98.31Z">
                </path>
            </svg>
        </div>
        <div class="sidebar-toggler  x">
            <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
        </div>
    </div>
</div>
<div class="sidebar-menu">
    <ul class="menu">
        <li class="sidebar-item active">
            <a href="<?= base_url("") ?>" class='sidebar-link'>
                <i class="icon-mid bi bi-box-arrow-left me-2"></i>
                <span>Keluar</span>
            </a>
        </li>
    </ul>
</div>
</div>
</div>
<div id="main" class='layout-navbar navbar-fixed'>
    <header class="p-0 m-0">
        <nav class="navbar navbar-expand navbar-light navbar-top" style="height: 20px !important;">
            <div class="container-fluid">
                <a href="#" class="burger-btn d-block">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </div>
        </nav>
    </header>
    <div id="main-content" class="pt-0">
        <?= $content ?>
    </div>
    <footer>
        <div class="footer clearfix mb-0 text-muted">
            <div class="float-start">
                <!-- <p>2025 &copy; APPCOMPANY</p> -->
            </div>
        <!-- <div class="float-end">
            <p>Crafted with <span class="text-danger"><i class="bi bi-heart-fill icon-mid"></i></span>
                by <a href="https://saugi.me">Saugi</a></p>
            </div>
        </div> -->
    </footer>
</div>
</div>
<script src="<?= base_url("assets") ?>/static/js/components/dark.js"></script>
<script src="<?= base_url("assets") ?>/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>

<script src="<?= base_url("assets") ?>/compiled/js/app.js"></script>
<script src="<?= base_url("assets") ?>/extensions/sweetalert2/sweetalert2.min.js" defer></script>
<script src="<?= base_url("assets") ?>/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?= base_url("assets") ?>/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>

<script src="<?= base_url("assets") ?>/extensions/choices.js/public/assets/scripts/choices.js"></script>
<script type="text/javascript">
    $( document ).ready(function() {
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