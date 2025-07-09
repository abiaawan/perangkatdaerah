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
        if($status[$tipeSoal]){
            if($status[$tipeSoal]->status == "draft"){
                return '<button type="submit" class="btn btn-primary mb-1"><i class="bi bi-pencil-fill"></i> Klik disini untuk mengubah variable '.$tipeSoal.'</button>';
            }elseif($status[$tipeSoal]->status == "submit"){
                return '<button type="submit" class="btn btn-success mb-1"><i class="bi bi-eye"></i> Klik disini untuk melihat variable '.$tipeSoal.'</button>';
            }
        }
        return '<button type="submit" class="btn btn-primary mb-1"><i class="bi bi-pencil-square"></i> Klik disini untuk mengisi variable '.$tipeSoal.'</button>';
    }
    function find_submitted($status, $tipeSoal)
    {
        if($status[$tipeSoal]){
            if($status[$tipeSoal]->status == "draft"){
                return false;
            }elseif($status[$tipeSoal]->status == "submit"){
                $statusArr = [$status[$tipeSoal]->approval_kementerian,$status[$tipeSoal]->approval_kl,$status[$tipeSoal]->approval_provinsi];
                if(!in_array(0, $statusArr) && !in_array(2, $statusArr) && !in_array(3, $statusArr)){
                    return true;
                }
            }
        }
        return false;
    }
    function find_status_approval($status, $tipeSoal)
    {
        if($status[$tipeSoal]){
            return status_text([$status[$tipeSoal]->approval_kementerian,$status[$tipeSoal]->approval_kl,$status[$tipeSoal]->approval_provinsi],[$status[$tipeSoal]->comment_kementerian,$status[$tipeSoal]->comment_kl,$status[$tipeSoal]->comment_provinsi]);
        }else{
            return status_text([0,0,0],["","",""]);
        }
        return status_text([0,0,0],["","",""]);
    }
    function status_text($arr, $comm)
    {
        $approver = "";
        $html = "";
        $badge = "";
        foreach ($arr as $k => $v) {
            if($v <> 4){
                $rejected = "";
                $rejected2 = "";
                $rejected3 = "";
                if($k == 0){
                    $approver = "Kemendagri";
                }elseif($k == 1){
                    $approver = "K/L Terkait";
                }elseif($k == 2){
                    $approver = "Provinsi";
                }

                if($v == 0){
                    $txt = "Belum Submit";
                    $badge = "secondary";
                    return '<div class="ms-1 me-0 mt-0 mb-1 p-0"><span class="badge bg-'.$badge.'">'.$txt.'</span></div>';
                }elseif($v == 1){
                    $txt = "Approved by";
                    $badge = "success";
                }elseif($v == 2){
                    $rejected = " rejected-span";
                    $rejected2 = ' data-comment="'.htmlspecialchars($comm[$k]).'"';
                    $rejected3 = '<i class="bi bi-info-circle"></i> ';
                    $txt = "Rejected by";
                    $badge = "danger";
                    return '<div class="ms-1 me-0 mt-0 mb-1 p-0'.$rejected.'"'.$rejected2.'><span class="badge bg-'.$badge.'">'.$rejected3.$txt.' '.$approver.'</span></div>';
                }elseif($v == 3){
                    $txt = "Menunggu Approval";
                    $badge = "info";
                }
                $html .= '<div class="ms-1 me-0 mt-0 mb-1 p-0"><span class="badge bg-'.$badge.'">'.$txt.' '.$approver.'</span></div>';
            }
        }
        return $html;
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
                                <div class="col-12 d-flex justify-content-end">
                                    <?= find_status_approval($data_status, "umum") ?>
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
                                <div class="col-12 d-flex justify-content-end">
                                    <?= find_status_approval($data_status, "teknis") ?>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php if(find_submitted($data_status, "umum") && find_submitted($data_status, "teknis")){ ?>
            <div class="col-12 d-flex px-2">
                <span>
                    <span class="h6 me-3">Skor Data Tipelogi <?= $subtitle ?></span>
                    <span class="text-nowrap">
                        <button type="button" id="dl-skor-btn" data-var="<?= $tipe_variable ?>" data-badan="" class="btn btn-success mb-1"><i class="bi bi-download"></i> Unduh</button>
                        <button type="button" id="view-skor-btn" data-var="<?= $tipe_variable ?>" data-badan="" class="btn btn-primary me-1 mb-1"><i class="bi bi-eye-fill"></i> View Skor</button>
                    </span>
                </span>

            </div>
        <?php } ?>
        <div class="modal fade" id="modal-total-skor" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-centered modal-dialog-scrollable"
            role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">View Skor
                    </h5>
                </div>
                <div class="modal-body" id="total-skor-container">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary ms-1" data-bs-dismiss="modal">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">OK</span>
                    </button>
                </div>
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
        $(document).on('click', '#view-skor-btn', function(e) {
            var upElem = $(this);
            var formData = new FormData();
            formData.append('tipe_var', upElem.data("var"));
            formData.append('id_badan', upElem.data("badan"));
            $.ajax({
                type: "POST",
                url: "<?= base_url("variable/view_skor") ?>",
                data: formData,
                processData: false,
                contentType: false,
                success: function(data)
                {
                    $("#total-skor-container").html(data);
                    $('#modal-total-skor').modal('show');
                }
            });
            
        });
        $(document).on( 'click','.rejected-span', function(e) {
            swal.fire({
                title: "Alasan Penolakan",
                text: $(this).data("comment"),
            });
        });
        $(document).on('click', '#dl-skor-btn', function(e){
            var link = document.createElement("a");
            link.download = "";
            link.href = "<?= base_url("variable/download_variable_all/") ?>?tipe_var="+$(this).data("var")+"&id_badan="+$(this).data("badan");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            delete link;
        });
    });
</script>
