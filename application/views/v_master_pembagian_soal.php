<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><?= $title ?></h3>
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
            </div>
            <div class="card-body">
                <div class="col-md-4 mb-0">
                    <h6>Pilih Tahun</h6>
                    <div class="form-group">
                        <select class="choices form-select" id="year">
                            <?php for ($i=2024; $i <= date("Y"); $i++) { ?>
                                <option value="<?= $i ?>" <?= $i == $year ? "selected" : "" ?>><?= $i ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-12 d-flex justify-content-end mt-3">
                    <?php if ($year > 2024) { ?>
                        <button type="button" id="copy-btn" class="btn btn-primary me-1 mb-1"><i class="bi bi-copy"></i> Salin data dari tahun <?= $year-1 ?></button>
                    <?php } ?>
                    <button type="button" id="add-btn" class="btn btn-primary me-1 mb-1"><i class="bi bi-plus-lg"></i> Add Soal</button>
                </div>
                <div>
                    <table id="table2" class="table table-striped table-bordered w-100">
                        <thead>
                            <tr>
                                <th class="text-center">NO</th>
                                <th class="text-center">KODE SOAL</th>
                                <th class="text-center">TIPE SOAL</th>
                                <th class="text-center">TIPE DAERAH</th>
                                <th class="text-center">PERANGKAT DAERAH</th>
                                <th class="text-center">BADAN/DINAS</th>
                                <th class="text-center">DATE UPDATED</th>
                                <th class="text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data as $k => $v) {
                                $naming = "";
                                $parent = "";
                                $parentOri = "";
                                if($v->nama_badan){
                                    $naming = "{$v->kode_badan}. {$v->nama_badan}";
                                }
                                if($v->parent){
                                    $parent = "<small><small><small>{$v->kode_parent}. {$v->parent}</small></small></small><br>";
                                    $parentOri = "{$v->kode_parent}. {$v->parent} ";
                                }
                                ?>
                                <tr>
                                    <td class="text-center"></td>
                                    <td><?= $v->kode_soal ?></td>
                                    <td><?= $v->tipe_soal ?></td>
                                    <td><?= $v->tipe_daerah ?></td>
                                    <td><?= $v->tipe_variable ?></td>
                                    <td style="white-space: nowrap;overflow: hidden; text-overflow: ellipsis; max-width: 300px;" title="<?= $parentOri.$naming ?>"><?= $parent.$naming ?></td>
                                    <td style="white-space: nowrap;"><?= $v->updated_date ?></td>
                                    <td style="white-space: nowrap;">
                                        <button type="button" data-id="<?= $v->id ?>" class="btn btn-primary btn-edit btn-sm me-1 mb-1"><i class="bi bi-pencil-square"></i> Edit</button> 
                                        <button type="button" data-id="<?= $v->id ?>" class="btn btn-danger btn-delete btn-sm me-1 mb-1"><i class="bi bi-trash"></i> Hapus</button>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-add" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-xl"
            role="document">
            <div class="modal-content">
                <form class="form form-horizontal" action="<?= base_url("master/send_pembagian_soal") ?>" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalCenterTitle">Add Soal
                        </h5>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="mode" name="mode" value="add" required>
                        <input type="hidden" id="id" name="id" value="">
                        <div class="row">
                            <!-- Kode Soal -->
                            <div class="col-md-4">
                                <label class="mt-2" for="kode_soal">Kode Soal</label>
                            </div>
                            <div class="col-md-8">
                                <div class="my-1">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-file-earmark-code h5"></i></span>
                                        <input type="text" class="form-control" placeholder="Masukkan Kode Soal" id="kode_soal" name="kode_soal" value="" required maxlength="30">
                                    </div>
                                </div>
                            </div>


                            <!-- Tipe Soal -->
                            <div class="col-md-4">
                                <label class="mt-2" for="tipe_soal">Tipe Soal</label>
                            </div>
                            <div class="col-md-8">
                                <div class="my-1">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-card-list h5"></i></span>
                                        <select class="choices form-select" id="tipe_soal" name="tipe_soal" required>
                                            <option value="" selected>(Pilih Tipe Soal)</option>
                                            <option value="umum">Umum</option>
                                            <option value="teknis">Teknis</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Tipe Daerah -->
                            <div class="col-md-4">
                                <label class="mt-2" for="tipe_daerah">Tipe Daerah</label>
                            </div>
                            <div class="col-md-8">
                                <div class="my-1">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-geo-alt h5"></i></span>
                                        <select class="choices form-select" id="tipe_daerah" name="tipe_daerah" required>
                                            <option value="" selected>(Pilih Tipe Daerah)</option>
                                            <option value="provinsi">Provinsi</option>
                                            <option value="kabupaten">Kabupaten</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Tipe Variable -->
                            <div id="var-container" class="row m-0 p-0 d-none">
                                <div class="col-md-4">
                                    <label class="mt-2" for="tipe_variable">Perangkat Daerah</label>
                                </div>
                                <div class="col-md-8">
                                    <div class="my-1">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-tag h5"></i></span>
                                            <select class="choices form-select" id="tipe_variable" name="tipe_variable" required>
                                                <option value="" selected>(Pilih Tipe Variabel)</option>
                                                <option value="sekda">Sekda</option>
                                                <option value="sekdprd">Sekdprd</option>
                                                <option value="inspektorat">Inspektorat</option>
                                                <option value="dinas">Dinas</option>
                                                <option value="badan">Badan</option>
                                                <option value="kecamatan">Kecamatan</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <!-- ID Badan (asumsi integer) -->
                            <div id="badan-container" class="row m-0 p-0 d-none">
                                <div class="col-md-4">
                                    <label class="mt-2" for="id_badan">Badan / Dinas</label>
                                </div>
                                <div class="col-md-8">
                                    <div class="my-1">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-building h5"></i></span>
                                            <select class="choices form-select" id="id_badan" name="id_badan" required>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tahun -->
                            <div class="col-md-4">
                                <label class="mt-2" for="tahun">Tahun</label>
                            </div>
                            <div class="col-md-8">
                                <div class="my-1">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-calendar h5"></i></span>
                                        <input type="number" class="form-control" placeholder="Tahun (Contoh: 2024)" id="tahun" name="tahun" value="<?= $year ?>" readonly required min="1900" max="2300">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary ms-1" data-bs-dismiss="modal">
                            <i class="bx bx-check d-block d-sm-none"></i>
                            <span class="d-none d-sm-block"><i class="bi bi-arrow-counterclockwise"></i> Batal</span>
                        </button>
                        <button type="submit" class="btn btn-primary ms-1">
                            <i class="bx bx-check d-block d-sm-none"></i>
                            <span class="d-none d-sm-block"><i class="bi bi-send"></i> Submit</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
</div>

<script type="text/javascript">
    $( document ).ready(function() {
        const soal_select = $('#tipe_soal')[0];
        const daerah_select = $('#tipe_daerah')[0];
        const year_select = $('#year')[0];
        const var_select = $('#tipe_variable')[0];
        const badan_select = $('#id_badan')[0];
        var choices = new Choices(soal_select, {
            removeItemButton: true,
            itemSelectText: "",
            shouldSort: false,
        });
        var choices2 = new Choices(daerah_select, {
            removeItemButton: true,
            itemSelectText: "",
            shouldSort: false,
        });
        var choices3 = new Choices(year_select, {
            removeItemButton: true,
            itemSelectText: "",
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
        $(document).on('change', '#tipe_variable', function(e) {
            change_select_state2($(this).val());
        });
        $(document).on('change', '#tipe_soal', function(e) {
            change_select_state($(this).val());
        });
        function change_select_state(elem)
        {
            if(elem == "umum"){
                $('#var-container').addClass("d-none");
                $('#badan-container').addClass("d-none");
                $('#tipe_variable').prop("required", false);
                $('#id_badan').prop("required", false);
                choices4.setChoiceByValue('');
                choices5.setChoiceByValue('');
            } else {
                $('#var-container').removeClass("d-none");
                $('#tipe_variable').prop("required", true);
                choices4.setChoiceByValue('');
                choices5.setChoiceByValue('');
            }
        }
        function change_select_state2(elem)
        {
            if(elem == "dinas" || elem == "badan"){
                if($("#tipe_daerah").val() == ""){
                    alert("Pilih tipe daerah terlebih dahulu!");
                    choices4.setChoiceByValue('');
                    return;
                }
                $('#badan-container').removeClass("d-none");
                $('#id_badan').prop("required", true);
                choices5.setChoiceByValue('');
                $.ajax({
                    type: "GET",
                    url: "<?= base_url("tipelogi/get_badan") ?>",
                    data: {
                        type: $("#tipe_variable").val(),
                        daerah: $("#tipe_daerah").val(),
                        tahun: "<?= $year ?>",
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
            } else {
                $('#badan-container').addClass("d-none");
                $('#id_badan').prop("required", false);
                choices5.setChoiceByValue('');
            }
        }
        $(document).on('change', '#year', function(e) {
            window.location.replace("<?= base_url("master/pembagian_soal/") ?>"+$(this).val());
        });
        $(document).on('click', '#add-btn', function(e) {
            $("#mode").val("add");
            $('#var-container').addClass("d-none");
            $('#badan-container').addClass("d-none");
            $("#id").val("");
            $("#kode_soal").val("");
            $('#id').prop("required", false);
            choices.setChoiceByValue("");
            choices2.setChoiceByValue("");
            choices5.setChoiceByValue("");
            choices4.setChoiceByValue("");
            $('#modal-add').modal('show');
        });
        $(document).on('click', '.btn-edit', function(e) {
            $("#mode").val("edit");
            $('#id').prop("required", true);
            $('#var-container').addClass("d-none");
            $('#badan-container').addClass("d-none");
            $.ajax({
                type: "GET",
                url: "<?= base_url("master/load_pembagian_soal") ?>",
                data: {
                    id: $(this).data("id"),
                },
                dataType: "json",
                contentType: false,
                success: function(data)
                {
                    $("#id").val(data.id);
                    $("#kode_soal").val(data.kode_soal);
                    choices.setChoiceByValue(data.tipe_soal);
                    choices2.setChoiceByValue(data.tipe_daerah);
                    if(data.tipe_soal == "teknis"){
                        $('#var-container').removeClass("d-none");
                    }
                    if(data.tipe_variable == "dinas" || data.tipe_variable == "badan"){
                        var idbadan = data.id_badan;
                        $.ajax({
                            type: "GET",
                            url: "<?= base_url("tipelogi/get_badan") ?>",
                            data: {
                                type: data.tipe_variable,
                                daerah: data.tipe_daerah,
                                tahun: "<?= $year ?>",
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
                                choices5.setChoiceByValue(idbadan);
                            }
                        });
                        $('#badan-container').removeClass("d-none");
                    }
                    choices4.setChoiceByValue(data.tipe_variable);
                    $('#modal-add').modal('show');
                }
            });
        });
        $(document).on('click', '#copy-btn', function(e) {
            Swal.fire({
                title: 'Yakin ingin menyalin data dari tahun <?= ($year-1) ?>?',
                text: 'Semua data dari tahun <?= ($year) ?> akan hilang dan digantikan dengan data dari tahun <?= ($year-1) ?>! Hanya lakukan sekali sebelum penginputan data di tahun <?= ($year) ?> dimulai! Pastikan Master Data untuk Dinas dan Badan di tahun <?= ($year) ?> sudah terisi!',
                showDenyButton: true,
                confirmButtonText: 'Tidak',
                denyButtonText: 'Ya',
                customClass: {
                    actions: 'my-actions',
                    confirmButton: 'order-2',
                    denyButton: 'order-1',
                },
            }).then((result) => {
                if (result.isDenied) {
                    window.location.href = '<?= base_url("master/salin_pembagian_soal/").$year ?>';
                }
            })
        });
        $(document).on('click', '.btn-delete', function(e) {
            var idu = $(this).data("id");
            Swal.fire({
                title: 'Yakin ingin menghapus data ini?',
                showDenyButton: true,
                confirmButtonText: 'Tidak',
                denyButtonText: 'Ya',
                customClass: {
                    actions: 'my-actions',
                    confirmButton: 'order-2',
                    denyButton: 'order-1',
                },
            }).then((result) => {
                if (result.isDenied) {
                    window.location.href = '<?= base_url("master/delete_pembagian_soal/") ?>'+idu;
                }
            })
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
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "scrollX": true,
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
