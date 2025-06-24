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
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label class=" mt-2" for="role">Data Skor</label>
                    </div>
                    <div class="col-md-8">
                        <div class="my-1">
                            <div class="form-group">
                                <select class="choices form-select w-100" id="role" name="role">
                                    <option value="provinsi" selected>Provinsi</option>
                                    <option value="kabupaten">Kabupaten</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label class=" mt-2" for="tahun">Tahun</label>
                    </div>
                    <div class="col-md-8">
                        <div class="my-1">
                            <div class="form-group">
                                <select class="choices form-select w-100" id="tahun" name="tahun">
                                    <?php for ($i=2024; $i <= date("Y")-1; $i++) { ?>
                                        <option value="<?= $i ?>" <?= $i==date("Y")-1 ? "selected" : "" ?>><?= $i ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label class=" mt-2" for="provinsi">Provinsi</label>
                    </div>
                    <div class="col-md-8">
                        <div class="my-1">
                            <div class="form-group">
                                <select class="choices form-select w-100" id="provinsi" name="provinsi">
                                    <option value="" selected>(Pilih Provinsi)</option>
                                    <?php foreach ($provinsi as $k => $v) { ?>
                                        <option value="<?= $v->kode_provinsi ?>"><?= $v->nama_provinsi ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row d-none" id="kab-container">
                    <div class="col-md-4">
                        <label class=" mt-2" for="kabupaten">Kabupaten</label>
                    </div>
                    <div class="col-md-8">
                        <div class="my-1">
                            <div class="form-group">
                                <select class="choices form-select w-100" id="kabupaten" name="kabupaten">
                                    <option value="" selected>(Pilih Kabupaten)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label class=" mt-2" for="var">Nomenklatur Perangkat Daerah</label>
                    </div>
                    <div class="col-md-8">
                        <div class="my-1">
                            <div class="form-group">
                                <select class="choices form-select w-100" id="var" name="var">
                                    <option value="" selected>(Pilih Nomenklatur Perangkat Daerah)</option>
                                    <option value="sekda">Sekretariat Daerah</option>
                                    <option value="sekdprd">Sekretariat DPRD</option>
                                    <option value="inspektorat">Inspektorat</option>
                                    <option value="dinas">Dinas</option>
                                    <option value="badan">Badan</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row d-none" id="perda-container">
                    <div class="col-md-4">
                    </div>
                    <div class="col-md-8">
                        <div class="my-1">
                            <div class="form-group">
                                <select class="choices form-select w-100" id="badan" name="badan">
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 d-flex justify-content-end mt-3">
                    <button type="button" id="view-skor-btn" class="btn btn-primary me-1 mb-1"><i class="bi bi-eye-fill"></i> View Skor</button>
                </div>
            </div>
        </div>
    </div>
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
                <button type="button" id="dl-skor-btn" class="btn btn-success me-1 mb-1"><i class="bi bi-download"></i> Unduh</button>
                <button type="button" class="btn btn-primary ms-1" data-bs-dismiss="modal">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">OK</span>
                </button>
            </div>
        </div>
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
        const role_select = $('#role')[0];
        const prov_select = $('#provinsi')[0];
        const kab_select = $('#kabupaten')[0];
        const var_select = $('#var')[0];
        const badan_select = $('#badan')[0];
        const $role_select = $('#role');
        const $prov_select = $('#provinsi');
        const $kab_select = $('#kabupaten');
        const $var_select = $('#var');
        const $badan_select = $('#badan');
        var choices = new Choices(prov_select, {
            removeItemButton: true,
            itemSelectText: "",
        });
        var choices2 = new Choices(kab_select, {
            removeItemButton: true,
            itemSelectText: "",
        });
        var choices3 = new Choices(role_select, {
            removeItemButton: true,
            itemSelectText: "",
            shouldSort: false,
        });
        var choices4 = new Choices(var_select, {
            removeItemButton: true,
            itemSelectText: "",
            shouldSort: false,
        });
        var choices5 = new Choices(badan_select, {
            removeItemButton: true,
            itemSelectText: "",
            shouldSort: false,
        });
        $(document).on('change', '#role', function(e) {

            choices.setChoiceByValue('');
            choices2.setChoiceByValue('');
            choices4.setChoiceByValue('');
            choices5.setChoiceByValue('');
            if($("#role").val() == "kabupaten"){
                choices4.setChoices(
                  [
                    { value: 'kecamatan', label: 'Kecamatan', selected: false }
                    ],
                  'value',
                  'label',
                  false
                  );
                $("#kab-container").removeClass("d-none");
            }else{
                $("#kab-container").addClass("d-none");
                choices4.destroy();
                $var_select.find('option[value="kecamatan"]').remove();
                choices4 = new Choices(var_select, {
                    removeItemButton: true,
                    itemSelectText: "",
                    shouldSort: false,
                });
            }
        });
        $(document).on('change', '#provinsi', function(e) {
            if($("#role").val() == "kabupaten"){
                $.ajax({
                    type: "GET",
                    url: "<?= base_url("user/load_kabupaten") ?>",
                    data: {
                        id: $(this).val(),
                    },
                    dataType: "json",
                    contentType: false,
                    success: function(data)
                    {
                        choices2.setChoices(
                            data,
                            'value',
                            'label',
                            true
                            );
                    }
                });
            }
        });

        $(document).on('change', '#var', function(e) {
            if($(this).val() == "dinas" || $(this).val() == "badan" || $(this).val() == "kecamatan"){
                if($(this).val() == "kecamatan"){
                    if($("#kabupaten").val() == ""){
                        alert("Pilih kabupaten/kota terlebih dahulu!");
                        choices4.setChoiceByValue('');
                        return;
                    }
                }
                $("#perda-container").removeClass("d-none");
                $.ajax({
                    type: "GET",
                    url: "<?= base_url("tipelogi/get_badan") ?>",
                    data: {
                        tahun: $("#tahun").val(),
                        type: $(this).val(),
                        daerah: $("#role").val(),
                        kab: $("#kabupaten").val(),
                    },
                    dataType: "json",
                    contentType: false,
                    success: function(data)
                    {
                        choices5.setChoices(
                            data,
                            'value',
                            'label',
                            true
                            );
                    }
                });
            }else{
                $("#perda-container").addClass("d-none");
                choices5.setChoiceByValue('');
            }
        });
        function check_validation()
        {
            if($("#provinsi").val() == ""){
                return false;
            }
            if($("#var").val() == ""){
                return false;
            }
            if($("#role").val() == "kabupaten" && $("#kabupaten").val() == ""){
                return false;
            }
            if(($("#var").val() == "dinas" || $("#var").val() == "badan" || $("#var").val() == "kecamatan") && $("#badan").val() == ""){
                return false;
            }
            return true;
        }
        $(document).on('click', '#view-skor-btn', function(e) {
            if(check_validation() == false)
            {
                alert("lengkapi pilihan terlebih dahulu!");
                return;
            }
            var upElem = $(this);
            var formData = new FormData();
            formData.append('tipe_var', $("#var").val());
            formData.append('id_badan', $("#badan").val());
            formData.append('role', $("#role").val());
            formData.append('tahun', $("#tahun").val());
            formData.append('provinsi', $("#provinsi").val());
            formData.append('kabupaten', $("#kabupaten").val());
            $.ajax({
                type: "POST",
                url: "<?= base_url("variable/view_skor") ?>",
                data: formData,
                processData: false,
                contentType: false,
                success: function(data)
                {
                    if(data == "404"){
                        alert("Soal belum dibuat!");
                    }else if(data == "204"){
                        alert("User belum submit data!");
                    }else if(data == "100"){
                        $("#skor-title").text($("#badan option:selected").text());
                        $('#modal-skor').modal('show');
                    }else{
                        $("#total-skor-container").html(data);
                        $('#modal-total-skor').modal('show');
                    }
                    
                }
            });
        });
        $(document).on('click', '#dl-skor-btn', function(e){
            if(check_validation() == false)
            {
                alert("lengkapi pilihan terlebih dahulu!");
                return;
            }
            var link = document.createElement("a");
            link.download = "";
            link.href = "<?= base_url("variable/download_variable_all/") ?>?tipe_var="+$("#var").val()+"&id_badan="+$("#badan").val()+"&role="+$("#role").val()+"&tahun="+$("#tahun").val()+"&provinsi="+$("#provinsi").val()+"&kabupaten="+$("#kabupaten").val();
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            delete link;
        });
    });

</script>