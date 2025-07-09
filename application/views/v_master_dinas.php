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
                    <button type="button" id="add-btn" class="btn btn-primary me-1 mb-1"><i class="bi bi-plus-lg"></i> Add <?= ucwords($tipe_badan) ?></button>
                </div>
                <div>
                    <table id="table2" class="table table-striped table-bordered w-100">
                        <thead>
                            <tr>
                                <th class="text-center">NO</th>
                                <th class="text-center">NAMA  <?= strtoupper($tipe_badan) ?></th>
                                <th class="text-center">TIPE DAERAH</th>
                                <th class="text-center">PENILAIAN</th>
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
                                    <td style="white-space: nowrap;overflow: hidden; text-overflow: ellipsis; max-width: 300px;" title="<?= $parentOri.$naming ?>"><?= $parent.$naming ?></td>
                                    <td><?= $v->tipe_daerah ?></td>
                                    <td><?= $v->tipe_penilaian ?></td>
                                    <td style="white-space: nowrap;"><?= $v->updated_date ?></td>
                                    <td style="white-space: nowrap;">
                                        <button type="button" data-id="<?= $v->id_badan ?>" class="btn btn-primary btn-edit btn-sm me-1 mb-1"><i class="bi bi-pencil-square"></i> Edit</button> 
                                        <button type="button" data-id="<?= $v->id_badan ?>" class="btn btn-danger btn-delete btn-sm me-1 mb-1"><i class="bi bi-trash"></i> Hapus</button>
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
                <form class="form form-horizontal" action="<?= base_url("master/send_$tipe_badan") ?>" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalCenterTitle">Add <?= ucwords($tipe_badan) ?>
                    </h5>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="mode" name="mode" value="add">
                    <input type="hidden" id="id_badan" name="id_badan" value="">
                    <input type="hidden" id="tipe_badan" name="tipe_badan" value="<?= $tipe_badan ?>" required>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="label mt-2" for="kode_parent">Kode Parent</label>
                        </div>
                        <div class="col-md-8">
                            <div class="my-1">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-credit-card-2-front h5"></i></span>
                                    <input type="text" class="form-control" placeholder="Kode Parent" id="kode_parent" name="kode_parent" value="" maxlength="20">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="label mt-2" for="parent">Parent</label>
                        </div>
                        <div class="col-md-8">
                            <div class="my-1">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-building h5"></i></span>
                                    <input type="text" class="form-control" placeholder="Parent" id="parent" name="parent" value="" maxlength="150">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="label mt-2" for="kode_badan">Kode <?= ucwords($tipe_badan) ?></label>
                        </div>
                        <div class="col-md-8">
                            <div class="my-1">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-credit-card-2-front h5"></i></span>
                                    <input type="text" class="form-control" placeholder="Kode Badan" id="kode_badan" name="kode_badan" value="" maxlength="20" required>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="label mt-2" for="nama_badan">Nama <?= ucwords($tipe_badan) ?></label>
                        </div>
                        <div class="col-md-8">
                            <div class="my-1">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-building h5"></i></span>
                                    <input type="text" class="form-control" placeholder="Nama Badan" id="nama_badan" name="nama_badan" value="" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="label mt-2" for="tipe_penilaian">Tipe Penilaian</label>
                        </div>
                        <div class="col-md-8">
                            <div class="my-1">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-patch-check h5"></i></span>
                                    <select class="form-select" id="tipe_penilaian" name="tipe_penilaian" required>
                                        <option value="" selected>(Pilih Tipe Penilaian)</option>
                                        <option value="kosong">Kosong</option>
                                        <option value="terisi">Sudah terisi dengan skor >800</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="label mt-2" for="tipe_daerah">Tipe Daerah</label>
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
                            <label class="label mt-2" for="tahun">Tahun</label>
                        </div>
                        <div class="col-md-8">
                            <div class="my-1">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar-range h5"></i></span>
                                    <input type="number" class="form-control" placeholder="Tahun (YYYY)" id="tahun" name="tahun" value="<?= $year ?>" readonly min="1900" max="2300" required>
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
        const penilaian_select = $('#tipe_penilaian')[0];
        const daerah_select = $('#tipe_daerah')[0];
        const year_select = $('#year')[0];
        const kl_select = $('#kl')[0];
        var choices = new Choices(penilaian_select, {
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
        var choices4 = new Choices(kl_select, {
            removeItemButton: true,
            itemSelectText: "",
        });
        $(document).on('change', '#year', function(e) {
            window.location.replace("<?= base_url("master/$tipe_badan/") ?>"+$(this).val());
        });
        $(document).on('click', '#add-btn', function(e) {
            $("#mode").val("add");
            $("#id_badan").val("");
            $('#id_badan').prop("required", false);
            $("#kode_parent").val("");
            $("#parent").val("");
            $("#kode_badan").val("");
            $("#nama_badan").val("");
            choices.setChoiceByValue("");
            choices2.setChoiceByValue("");
            $('#modal-add').modal('show');
        });
        $(document).on('click', '.btn-edit', function(e) {
            $("#mode").val("edit");
            $('#id_badan').prop("required", true);
            $.ajax({
                type: "GET",
                url: "<?= base_url("master/load_badan") ?>",
                data: {
                    id: $(this).data("id"),
                },
                dataType: "json",
                contentType: false,
                success: function(data)
                {
                    $("#id_badan").val(data.id_badan);
                    $("#kode_parent").val(data.kode_parent);
                    $("#parent").val(data.parent);
                    $("#kode_badan").val(data.kode_badan);
                    $("#nama_badan").val(data.nama_badan);
                    choices.setChoiceByValue(data.tipe_penilaian);
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
                    window.location.href = '<?= base_url("master/salin_$tipe_badan/").$year ?>';
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
                    window.location.href = '<?= base_url("master/delete_$tipe_badan/") ?>'+idu;
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
