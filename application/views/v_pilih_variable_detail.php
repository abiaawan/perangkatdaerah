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
    <section class="section">
        <div class="card">
            <div class="card-header">
                <!-- <h5 class="card-title">
                    jQuery Datatable
                </h5> -->
            </div>
            <div class="card-body">
                <?php 
                function find_status($tipe_variable, $status, $kode, $tipeSoal, $tipe_penilaian, $name=null)
                {
                    if($tipe_penilaian == false){
                        return '<button title="View" type="button" data-name="'.$name.'" class="border-0 bg-transparent btn-view-skor"><i class="bi bi-eye-fill bi-lg text-primary"></i></button>';
                    }
                    foreach ($status as $k => $v) {
                        if($tipe_variable == "dinas" || $tipe_variable == "badan"){
                            $id = $v->id_badan;
                        }else{
                            $id = $v->kode_kecamatan;
                        }
                        if($kode == $id && $v->tipe_soal == $tipeSoal){
                            if($v->status == "draft"){
                                return '<i class="bi bi-pencil-fill" title="Drafted"></i><button title="Edit" type="submit" class="border-0 bg-transparent" name="tipe_soal_plus" value="'.$tipeSoal.'_'.$kode.'"><i class="bi bi-pencil-square bi-lg text-primary"></i></button>';
                            }elseif($v->status == "submit"){
                                return '<button title="View" type="submit" class="border-0 bg-transparent" name="tipe_soal_plus" value="'.$tipeSoal.'_'.$kode.'"><i class="bi bi-eye-fill bi-lg text-primary"></i></button>';
                            }
                        }
                    }
                    return '<button title="Edit" type="submit" class="border-0 bg-transparent" name="tipe_soal_plus" value="'.$tipeSoal.'_'.$kode.'"><i class="bi bi-pencil-square bi-lg text-primary"></i></button>';
                }
                ?>
                <form class="form form-horizontal" action="<?= base_url("variable/add/$url") ?>" method="POST">
                    <input type="hidden" name="tipe_variable" value="<?= $tipe_variable ?>">
                    <div class="table-responsive">
                        <table class="table" id="table2">
                            <thead>
                                <tr>
                                    <th class="text-center">NO</th>
                                    <th class="text-center"><?= strtoupper($url) ?></th>
                                    <th class="text-center">VARIABLE UMUM</th>
                                    <th class="text-center">VARIABLE TEKNIS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data_variable as $k => $v) {
                                    $tipe_penilaian = true;
                                    if($tipe_variable == "dinas" || $tipe_variable == "badan"){
                                        $naming = "{$v->kode_badan}. {$v->nama_badan}";
                                        if($v->parent){
                                            $parent = "<small><small><small>{$v->kode_parent}. {$v->parent}</small></small></small><br>";
                                        }else{
                                            $parent = "";
                                        }
                                        if($v->tipe_penilaian == "terisi"){
                                            $tipe_penilaian = false;
                                        }
                                        $code = $v->id_badan;
                                    }else{
                                        $parent = "";
                                        $naming = "{$v->nama_kecamatan}";
                                        $code = $v->kode_kecamatan;
                                    }
                                    ?>
                                    <tr>
                                        <td class="text-center"></td>
                                        <td><?= $parent ?><?= $naming ?></td>
                                        <td class="text-center">
                                            <?= find_status($tipe_variable, $data_status, $code, "umum", $tipe_penilaian, $parent.$naming." ".str_replace("Badan ", "",$subtitle)) ?>
                                        </td>
                                        <td class="text-center">
                                            <?= find_status($tipe_variable, $data_status, $code, "teknis", $tipe_penilaian, $parent.$naming." ".str_replace("Badan ", "",$subtitle)) ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
        <div class="modal fade" id="modal-skor" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable"
            role="document">
            <div class="modal-content">
                <div class="modal-header">

                </div>
                <div class="modal-body">
                    <span id="skor-title"></span> mempunyai Nilai Tipelogi tipe A. dengan Nilai Skor di atas 800
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
        $(document).on('click', '.btn-view-skor', function(e) {
            $("#skor-title").text($(this).data("name"));
            $('#modal-skor').modal('show');
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
        let jquery_datatable = $("#table2").DataTable({
            responsive: true,
            "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                $('td:eq(0)', nRow).html(iDisplayIndexFull +1+".");
            }
        })

        const setTableColor = () => {
            document.querySelectorAll('.dataTables_paginate .pagination').forEach(dt => {
                dt.classList.add('pagination-primary')
            })
        }
        setTableColor()
        jquery_datatable.on('draw', setTableColor)
    });
</script>
