<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="page-heading mb-0">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><?= $title ?></h3>
                <p class="text-subtitle text-muted"></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?= $title ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Daftar Notifikasi</h4>
            </div>
            <div class="card-body">
                <div class="list-group" id="all-notifications-list">
                    <?php

                    function timeAgo2($dateString) {
                        $date = new DateTime($dateString);
                        $now = new DateTime();
                        $seconds = $now->getTimestamp() - $date->getTimestamp();
                        $interval = $seconds / 31536000;
                        if ($interval > 1) {
                            return floor($interval) . " tahun yang lalu";
                        }
                        $interval = $seconds / 2592000;
                        if ($interval > 1) {
                            return floor($interval) . " bulan yang lalu";
                        }
                        $interval = $seconds / 86400;
                        if ($interval > 1) {
                            return floor($interval) . " hari yang lalu";
                        }
                        $interval = $seconds / 3600;
                        if ($interval > 1) {
                            return floor($interval) . " jam yang lalu";
                        }
                        $interval = $seconds / 60;
                        if ($interval > 1) {
                            return floor($interval) . " menit yang lalu";
                        }

                        return "Baru saja";
                    }

                    foreach ($notif as $k => $v) {
                        $time = timeAgo2($v->date);
                        $iconClass = $v->icon ?? 'bi-info-circle';
                        $iconBg = $v->color ?? 'bg-primary';
                        $url = base_url("notification/read/") . $v->id;
                        echo <<<SMF
                        <li class="dropdown-item notification-item">
                        <a class="d-flex align-items-start text-decoration-none" href="{$url}">
                        <div class="notification-icon {$iconBg}"><i class="bi {$iconClass}"></i></div>
                        <div class="notification-text ms-3">
                        <p class="notification-title">{$v->title}</p>
                        <p class="notification-subtitle">{$v->body}</p>
                        <p class="notification-time">{$time}</p>
                        </div>
                        </a>
                        </li>
                        SMF;
                    } ?>
                </div>
            </div>
            <div class="card-footer text-center">
                <button class="btn btn-success btn-sm" id="more-btn"><i class="bi bi-arrow-clockwise"></i> Tampilkan lebih banyak</button>
            </div>
        </div>
    </section>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        var lenNow = 1;
        function loadAllNotifications(len) {
            const url = "<?= base_url('notification/get_more/') ?>"+len; 

            $.ajax({
                type: "GET",
                url: url,
                dataType: "json",
                success: function(notifications) {
                    if (!notifications || notifications.length === 0) return;

                    $.each(notifications, function(index, notification) {
                        const time = timeAgo2(notification.date);
                        const iconClass = notification.icon || 'bi-info-circle';
                        const iconBg = notification.color || 'bg-primary';
                        const url = "<?= base_url("notification/read/") ?>" + notification.id;

                        const notificationHtml = `
                        <li class="dropdown-item notification-item">
                        <a class="d-flex align-items-start text-decoration-none" href="${url}">
                        <div class="notification-icon ${iconBg}"><i class="bi ${iconClass}"></i></div>
                        <div class="notification-text ms-3">
                        <p class="notification-title">${notification.title}</p>
                        <p class="notification-subtitle">${notification.body}</p>
                        <p class="notification-time">${time}</p>
                        </div>
                        </a>
                        </li>`;
                        $('#all-notifications-list').append(notificationHtml);
                    });
                    lenNow += 1;
                },
                error: function() {
                    // $('#all-notifications-list').html('<p class="text-center text-danger">Gagal memuat notifikasi.</p>');
                }
            });
        }
        $(document).on('click', '#more-btn', function(e){
            loadAllNotifications(lenNow);
        });
        
        function timeAgo2(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const seconds = Math.floor((now - date) / 1000);

            let interval = seconds / 31536000;
            if (interval > 1) return Math.floor(interval) + " tahun yang lalu";
            interval = seconds / 2592000;
            if (interval > 1) return Math.floor(interval) + " bulan yang lalu";
            interval = seconds / 86400;
            if (interval > 1) return Math.floor(interval) + " hari yang lalu";
            interval = seconds / 3600;
            if (interval > 1) return Math.floor(interval) + " jam yang lalu";
            interval = seconds / 60;
            if (interval > 1) return Math.floor(interval) + " menit yang lalu";
            return "Baru saja";
        }
    });
</script>
