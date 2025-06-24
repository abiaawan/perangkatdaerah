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
                                <th class="text-center">NO SOAL</th>
                                <th class="text-center">SOAL</th>
                                <th class="text-center">JAWABAN A</th>
                                <th class="text-center">JAWABAN B</th>
                                <th class="text-center">JAWABAN C</th>
                                <th class="text-center">JAWABAN D</th>
                                <th class="text-center">JAWABAN E</th>
                                <th class="text-center">SKALA A</th>
                                <th class="text-center">SKALA B</th>
                                <th class="text-center">SKALA C</th>
                                <th class="text-center">SKALA D</th>
                                <th class="text-center">SKALA E</th>
                                <th class="text-center">BOBOT</th>
                                <th class="text-center">DATE UPDATED</th>
                                <th class="text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data as $k => $v) { ?>
                                <tr>
                                    <td class="text-center"></td>
                                    <td><?= $v->kode_soal ?></td>
                                    <td><?= $v->tipe_soal ?></td>
                                    <td><?= $v->tipe_daerah ?></td>
                                    <td><?= $v->no ?></td>
                                    <td style="white-space: nowrap;overflow: hidden; text-overflow: ellipsis; max-width: 300px;" title="<?= $v->soal ?>"><?= $v->soal ?></td>
                                    <td>≤<?= $v->jawaban_a ?></td>
                                    <td><?= $v->jawaban_b ?></td>
                                    <td><?= $v->jawaban_c ?></td>
                                    <td><?= $v->jawaban_d ?></td>
                                    <td>><?= $v->jawaban_e ?></td>
                                    <td><?= $v->skala_a ?></td>
                                    <td><?= $v->skala_b ?></td>
                                    <td><?= $v->skala_c ?></td>
                                    <td><?= $v->skala_d ?></td>
                                    <td><?= $v->skala_e ?></td>
                                    <td><?= $v->bobot ?></td>
                                    <td style="white-space: nowrap;"><?= $v->updated_date ?></td>
                                    <td style="white-space: nowrap;">
                                        <button type="button" data-id="<?= $v->id_soal ?>" class="btn btn-primary btn-edit btn-sm me-1 mb-1"><i class="bi bi-pencil-square"></i> Edit</button> 
                                        <button type="button" data-id="<?= $v->id_soal ?>" class="btn btn-danger btn-delete btn-sm me-1 mb-1"><i class="bi bi-trash"></i> Hapus</button>
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
                <form class="form form-horizontal" action="<?= base_url("master/send_soal") ?>" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalCenterTitle">Add Soal
                        </h5>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="mode" name="mode" value="add" required>
                        <input type="hidden" id="id_soal" name="id_soal" value="">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="mt-2" for="kode_soal">Kode Soal</label>
                            </div>
                            <div class="col-md-8">
                                <div class="my-1">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-tag h5"></i></span>
                                        <input type="text" class="form-control" placeholder="Kode Soal" id="kode_soal" name="kode_soal" value="" maxlength="30" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="mt-2" for="tipe_soal">Tipe Soal</label>
                            </div>
                            <div class="col-md-8">
                                <div class="my-1">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-list-nested h5"></i></span>
                                        <select class="form-select" id="tipe_soal" name="tipe_soal" required>
                                            <option value="" selected>(Pilih Tipe Soal)</option>
                                            <option value="umum">Umum</option>
                                            <option value="teknis">Teknis</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="mt-2" for="tipe_daerah">Tipe Daerah</label>
                            </div>
                            <div class="col-md-8">
                                <div class="my-1">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-geo-alt h5"></i></span>
                                        <select class="form-select" id="tipe_daerah" name="tipe_daerah" required>
                                            <option value="" selected>(Pilih Tipe Daerah)</option>
                                            <option value="provinsi">Provinsi</option>
                                            <option value="kabupaten">Kabupaten</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="mt-2" for="no_soal">No. Soal</label>
                            </div>
                            <div class="col-md-8">
                                <div class="my-1">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-sort-numeric-up h5"></i></span>
                                        <input type="number" class="form-control" placeholder="Nomor Soal" id="no" name="no" value="" required min="1" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="mt-2" for="soal">Soal</label>
                            </div>
                            <div class="col-md-8">
                                <div class="my-1">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-question-circle h5"></i></span>
                                        <textarea class="form-control" placeholder="Soal" id="soal" name="soal" rows="3" maxlength="255" required></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="mt-2" for="jawaban_a">Jawaban A</label>
                            </div>
                            <div class="col-md-8">
                                <div class="my-1">
                                    <div class="input-group">
                                        <span class="input-group-text">≤</span>
                                        <input type="text" class="form-control" placeholder="Jawaban A" id="jawaban_a" name="jawaban_a" value="" maxlength="255" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="mt-2" for="jawaban_b">Jawaban B</label>
                            </div>
                            <div class="col-md-8">
                                <div class="my-1">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Jawaban B" id="jawaban_b" name="jawaban_b" value="" maxlength="127" required>
                                        <span class="input-group-text">-</span>
                                        <input type="text" class="form-control" placeholder="Jawaban B" id="jawaban_b2" name="jawaban_b2" value="" maxlength="127" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="mt-2" for="jawaban_c">Jawaban C</label>
                            </div>
                            <div class="col-md-8">
                                <div class="my-1">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Jawaban C" id="jawaban_c" name="jawaban_c" value="" maxlength="127" required>
                                        <span class="input-group-text">-</span>
                                        <input type="text" class="form-control" placeholder="Jawaban C" id="jawaban_c2" name="jawaban_c2" value="" maxlength="127" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="mt-2" for="jawaban_d">Jawaban D</label>
                            </div>
                            <div class="col-md-8">
                                <div class="my-1">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Jawaban D" id="jawaban_d" name="jawaban_d" value="" maxlength="127" required>
                                        <span class="input-group-text">-</span>
                                        <input type="text" class="form-control" placeholder="Jawaban D" id="jawaban_d2" name="jawaban_d2" value="" maxlength="127" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="mt-2" for="jawaban_e">Jawaban E</label>
                            </div>
                            <div class="col-md-8">
                                <div class="my-1">
                                    <div class="input-group">
                                        <span class="input-group-text">></span>
                                        <input type="text" class="form-control" placeholder="Jawaban E" id="jawaban_e" name="jawaban_e" value="" maxlength="255" required>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-4">
                                <label class="mt-2" for="skala_a">Skala A</label>
                            </div>
                            <div class="col-md-8">
                                <div class="my-1">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-bar-chart h5"></i></span>
                                        <input type="number" class="form-control" placeholder="Skala A" id="skala_a" name="skala_a" value="" min="0" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="mt-2" for="skala_b">Skala B</label>
                            </div>
                            <div class="col-md-8">
                                <div class="my-1">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-bar-chart h5"></i></span>
                                        <input type="number" class="form-control" placeholder="Skala B" id="skala_b" name="skala_b" value="" min="0" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="mt-2" for="skala_c">Skala C</label>
                            </div>
                            <div class="col-md-8">
                                <div class="my-1">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-bar-chart h5"></i></span>
                                        <input type="number" class="form-control" placeholder="Skala C" id="skala_c" name="skala_c" value="" min="0" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="mt-2" for="skala_d">Skala D</label>
                            </div>
                            <div class="col-md-8">
                                <div class="my-1">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-bar-chart h5"></i></span>
                                        <input type="number" class="form-control" placeholder="Skala D" id="skala_d" name="skala_d" value="" min="0" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="mt-2" for="skala_e">Skala E</label>
                            </div>
                            <div class="col-md-8">
                                <div class="my-1">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-bar-chart h5"></i></span>
                                        <input type="number" class="form-control" placeholder="Skala E" id="skala_e" name="skala_e" value="" min="0" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="mt-2" for="bobot">Bobot</label>
                            </div>
                            <div class="col-md-8">
                                <div class="my-1">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-gem h5"></i></span>
                                        <input type="number" class="form-control" placeholder="Bobot Soal" id="bobot" name="bobot" value="" required min="0">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="mt-2" for="tahun">Tahun</label>
                            </div>
                            <div class="col-md-8">
                                <div class="my-1">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-calendar-check h5"></i></span>
                                        <input type="number" class="form-control" placeholder="Tahun" id="tahun" name="tahun" min="1900" max="2300" value="<?= $year ?>" readonly required>
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
        $(document).on('change', '#year', function(e) {
            window.location.replace("<?= base_url("master/soal/") ?>"+$(this).val());
        });
        $(document).on('click', '#add-btn', function(e) {
            $("#mode").val("add");
            $("#id_soal").val("");
            $('#id_soal').prop("required", false);
            $("#kode_soal").val("");
            $("#no").val("");
            $("#soal").val("");
            $("#jawaban_a").val("");
            $("#jawaban_b").val("");
            $("#jawaban_c").val("");
            $("#jawaban_d").val("");
            $("#jawaban_b2").val("");
            $("#jawaban_c2").val("");
            $("#jawaban_d2").val("");
            $("#jawaban_e").val("");
            $("#skala_a").val("");
            $("#skala_b").val("");
            $("#skala_c").val("");
            $("#skala_d").val("");
            $("#skala_e").val("");
            $("#bobot").val("");
            choices.setChoiceByValue("");
            choices2.setChoiceByValue("");
            $('#modal-add').modal('show');
        });
        $(document).on('click', '.btn-edit', function(e) {
            $("#mode").val("edit");
            $('#id_soal').prop("required", true);
            $.ajax({
                type: "GET",
                url: "<?= base_url("master/load_soal") ?>",
                data: {
                    id: $(this).data("id"),
                },
                dataType: "json",
                contentType: false,
                success: function(data)
                {
                    $("#id_soal").val(data.id_soal);
                    $("#kode_soal").val(data.kode_soal);
                    $("#no").val(data.no);
                    $("#soal").val(data.soal);
                    $("#jawaban_a").val(data.jawaban_a);

                    $("#jawaban_b").val(data.jawaban_b.split("-")[0]);
                    $("#jawaban_c").val(data.jawaban_c.split("-")[0]);
                    $("#jawaban_d").val(data.jawaban_d.split("-")[0]);
                    $("#jawaban_b2").val(data.jawaban_b.split("-")[1]);
                    $("#jawaban_c2").val(data.jawaban_c.split("-")[1]);
                    $("#jawaban_d2").val(data.jawaban_d.split("-")[1]);

                    $("#jawaban_e").val(data.jawaban_e);
                    $("#skala_a").val(data.skala_a);
                    $("#skala_b").val(data.skala_b);
                    $("#skala_c").val(data.skala_c);
                    $("#skala_d").val(data.skala_d);
                    $("#skala_e").val(data.skala_e);
                    $("#bobot").val(data.bobot);
                    choices.setChoiceByValue(data.tipe_soal);
                    choices2.setChoiceByValue(data.tipe_daerah);
                    $('#modal-add').modal('show');
                }
            });
        });
        $(document).on('click', '#copy-btn', function(e) {
            Swal.fire({
                title: 'Yakin ingin menyalin data dari tahun <?= ($year-1) ?>?',
                text: 'Semua data dari tahun <?= ($year) ?> akan hilang dan digantikan dengan data dari tahun <?= ($year-1) ?>! Hanya lakukan sekali sebelum penginputan data di tahun <?= ($year) ?> dimulai!',
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
                    window.location.href = '<?= base_url("master/salin_soal/").$year ?>';
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
                    window.location.href = '<?= base_url("master/delete_soal/") ?>'+idu;
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
