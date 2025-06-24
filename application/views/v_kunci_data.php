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
                        <label class=" mt-2" for="role">Data Pengisian</label>
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
                <div class="col-12 d-flex justify-content-end mt-3 mb-3">
                    <button type="button" id="view-table-btn" class="btn btn-primary me-1 mb-1"><i class="bi bi-search"></i> Cari</button>
                </div>
                <div id="table-container">
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
        const $role_select = $('#role');
        const $prov_select = $('#provinsi');
        const $kab_select = $('#kabupaten');
        var choices_tahun = new Choices($('#tahun')[0], {
            removeItemButton: true,
            itemSelectText: "",
        });
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
        $(document).on('change', '#role', function(e) {

            choices.setChoiceByValue('');
            choices2.setChoiceByValue('');
            if($("#role").val() == "kabupaten"){
                $("#kab-container").removeClass("d-none");
            }else{
                $("#kab-container").addClass("d-none");
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
        function check_validation()
        {
            if($("#provinsi").val() == ""){
                return false;
            }
            if($("#role").val() == "kabupaten" && $("#kabupaten").val() == ""){
                return false;
            }
            return true;
        }
        $(document).on('click', '#view-table-btn', function(e) {
            if(check_validation() == false)
            {
                alert("lengkapi pilihan terlebih dahulu!");
                return;
            }
            var upElem = $(this);
            var formData = new FormData();
            formData.append('role', $("#role").val());
            formData.append('tahun', $("#tahun").val());
            formData.append('provinsi', $("#provinsi").val());
            formData.append('kabupaten', $("#kabupaten").val());
            $.ajax({
                type: "POST",
                url: "<?= base_url("kunci/get_kunci_table") ?>",
                data: formData,
                processData: false,
                contentType: false,
                success: function(data)
                {
                    $("#table-container").html(data);
                }
            });
        });
        
        $(document).on('click', '.unlock-btn', function(e) {
            var upElem = $(this);
            Swal.fire({
                title: 'Yakin ingin membuka kunci?',
                text: "Data harus disubmit kembali untuk mendapatkan skor!",
                showDenyButton: true,
                confirmButtonText: 'Ya',
                denyButtonText: 'Tidak',
                customClass: {
                    actions: 'my-actions',
                    confirmButton: 'order-1',
                    denyButton: 'order-2',
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData();
                    formData.append('var', upElem.data("var"));
                    formData.append('id_badan', upElem.data("badan"));
                    formData.append('role', upElem.data("role"));
                    formData.append('tahun', upElem.data("tahun"));
                    formData.append('provinsi', upElem.data("prov"));
                    formData.append('kabupaten', upElem.data("kab"));
                    formData.append('soal', upElem.data("soal"));
                    $.ajax({
                        type: "POST",
                        url: "<?= base_url("kunci/buka_kunci") ?>",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(data)
                        {
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
                                title: "Data sudah bisa diisi kembali!",
                            });
                            if(upElem.data("soal") == "umum"){
                                $(".btn-umum").remove();
                            }else{
                                upElem.remove();
                            }
                        }
                    });
                }
            })



        });
    });

</script>