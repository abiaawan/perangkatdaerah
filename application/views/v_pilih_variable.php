<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><?= $subtitle ?></h3>
                <!-- <p class="text-subtitle text-muted">Informasi Data Tematik adalah data untuk memberikan wawasan spesifik mengenai suatu topik atau tema dalam konteks geografis.</p> -->
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#"><?= $title ?></a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?= $subtitle ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <?php 
    function find_status($status, $tipeSoal)
    {
        if($status){
            foreach ($status as $k => $v) {
                if($v->tipe_soal == $tipeSoal){
                    if($v->status == "draft"){
                        return '<button type="submit" class="btn btn-primary me-1 mb-1"><i class="bi bi-pencil-fill"></i> Klik disini untuk mengubah variable '.$tipeSoal.'</button>';
                    }elseif($v->status == "submit"){
                        return '<button type="submit" class="btn btn-success me-1 mb-1"><i class="bi bi-eye"></i> Klik disini untuk melihat variable '.$tipeSoal.'</button>';
                    }
                }
            }
        }
        return '<button type="submit" class="btn btn-primary me-1 mb-1"><i class="bi bi-pencil-square"></i> Klik disini untuk mengisi variable '.$tipeSoal.'</button>';

    }
    ?>
    <section class="section">
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <form class="form form-horizontal" action="<?= base_url("variable/add/$url") ?>" method="POST">
                        <div class="form-body">
                            <div class="row">
                                <h5>Variable Umum</h5>
                                <p>Variabel Umum adalah indikator-indikator dasar yang mencerminkan kondisi umum atau skala wilayah tempat Urusan Pemerintahan tersebut diselenggarakan. Variabel ini cenderung bersifat makro dan seringkali berlaku sama atau serupa untuk berbagai jenis Urusan Pemerintahan di suatu daerah.</p>
                                <input type="hidden" name="tipe_soal" value="umum">
                                <input type="hidden" name="tipe_variable" value="<?= $tipe_variable ?>">
                                <div class="col-12 d-flex justify-content-end">
                                    <?= find_status($data_status, "umum") ?>
                                </div>
                            </div>
                        </div>
                    </form>
                    <form class="form form-horizontal mt-3" action="<?= base_url("variable/add/$url") ?>" method="POST">
                        <div class="form-body">
                            <div class="row">
                                <h5>Variable Teknis</h5>
                                <p>Variabel Teknis adalah indikator-indikator yang spesifik berkaitan langsung dengan substansi atau karakteristik teknis dari Urusan Pemerintahan yang bersangkutan. Variabel ini mencerminkan volume, kompleksitas, dan intensitas kegiatan inti dari urusan tersebut.</p>
                                <input type="hidden" name="tipe_soal" value="teknis">
                                <input type="hidden" name="tipe_variable" value="<?= $tipe_variable ?>">
                                <div class="col-12 d-flex justify-content-end">
                                    <?= find_status($data_status, "teknis") ?>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<script type="text/javascript">
    $( document ).ready(function() {
        history.pushState(null, document.title, window.location);
        $('form').on('submit', function(e) {
            if (!$(this).data('submitted')) {
              $(this).data('submitted', true);
              $(this).find("button").addClass('disabled');
          }
          else {
              e.preventDefault();
          }
      });
    });
</script>
