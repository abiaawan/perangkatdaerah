<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$naming = "";
if($this->session->userdata('whs_role') == "provinsi"){
    $naming = ucwords(strtolower($this->session->userdata('whs_nama_provinsi')));
}elseif($this->session->userdata('whs_role') == "kabupaten"){
    $naming = ucwords(strtolower($this->session->userdata('whs_nama_kabupaten')));
}else{
    $naming = ucwords($this->session->userdata('whs_role'));
}
?>
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><?= $title ?></h3>
                <p class="text-subtitle text-muted">Masukan data umum untuk melengkapi informasi <?= $naming ?>.</p>
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
            <div class="card-content">
                <div class="card-body">
                    <form class="form form-horizontal" action="<?= base_url("informasi/send") ?>" method="POST">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Nama <?= $this->session->userdata('whs_role') == "provinsi" ? "Provinsi" : "Kabupaten/Kota" ?></label>
                                </div>
                                <div class="col-md-8">
                                    <div class="my-1">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-geo-alt h5"></i></span>

                                            <input type="text" readonly disabled class="form-control" placeholder="" value="<?= $naming ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <h6>Informasi Umum</h6>
                                </div>
                                <div class="col-md-4">
                                    <label for="jml-penduduk">Jumlah Penduduk</label>
                                </div>
                                <div class="col-md-8">
                                    <div class="my-1">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-people h5"></i></span>
                                            <input type="number" class="form-control" placeholder="Jumlah Penduduk" id="jml-penduduk" name="penduduk" value="<?= $form["penduduk"] ?>" required>
                                            <span class="input-group-text" id="basic-addon2">jiwa</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label for="luas">Luas Wilayah</label>
                                </div>
                                <div class="col-md-8">
                                    <div class="my-1">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-map h5"></i></span>
                                            <input type="number" class="form-control" placeholder="Luas Wilayah" id="luas" name="luas"  value="<?= $form["luas"] ?>" required>
                                            <span class="input-group-text" id="basic-addon2">km²</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label for="kepadatan">Kepadatan</label>
                                </div>
                                <div class="col-md-8">
                                    <div class="my-1">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-people h5"></i></span>
                                            <input type="text" class="form-control" placeholder="kepadatan" id="kepadatan" name="kepadatan"  value="<?= $form["kepadatan"] ?>" disabled>
                                            <span class="input-group-text" id="basic-addon2">jiwa/km²</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label for="apbd">APBD</label>
                                </div>
                                <div class="col-md-8">
                                    <div class="my-1">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-cash h5"></i></span>
                                            <input type="hidden" name="apbd" value="<?= $form["apbd"] ?>">
                                            <input type="text" class="form-control rupiah" placeholder="APBD" id="apbd"  value="" required>
                                            <span class="input-group-text" id="basic-addon2">,00</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary me-1 mb-1">Submit</button>
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
        $('form').on('submit', function(e) {
            if (!$(this).data('submitted')) {
              $(this).data('submitted', true);
              $(this).find("button").addClass('disabled');
          }
          else {
              e.preventDefault();
          }
      });
        $("#apbd").val(formatRupiah($("input[name=apbd]").val(), 'Rp. '));
        $(document).on('keyup', '.rupiah', function(){
          $(this).val(formatRupiah($(this).val(), 'Rp. '));
          $("input[name=apbd]").val(formatRupiah($(this).val(), 'Rp. ').replaceAll(".","").replaceAll(" ","").replaceAll("Rp",""));
      });

        $(document).on('keyup', '#luas, #jml-penduduk', function(){
            if($("#jml-penduduk").val() > 0 && $("#luas").val() > 0){
                $("#kepadatan").val(Math.round(($("#jml-penduduk").val()/$("#luas").val())*10)/10);
            }
            
        });
        function formatRupiah(angka, prefix)
        {
          var number_string = angka.replace(/[^,\d]/g, '').toString(),
          split    = number_string.split(','),
          sisa     = split[0].length % 3,
          rupiah     = split[0].substr(0, sisa),
          ribuan     = split[0].substr(sisa).match(/\d{3}/gi);
          if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
    }
});
</script>
