<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><?= $title ?></h3>
                <p class="text-subtitle text-muted">Management user</p>
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
                <div class="col-12 d-flex justify-content-end mt-3" id="submit-container">
                    <button type="button" id="add-btn" class="btn btn-primary me-1 mb-1"><i class="bi bi-plus-lg"></i> Add User</button>
                </div>
                <table class="table table-striped" id="table1">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Provinsi</th>
                            <th>Kabupaten</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $k => $v) { ?>
                            <tr>
                                <td><?= $v->username ?></td>
                                <td><?= $v->role ?></td>
                                <td><?= $v->nama_provinsi ?></td>
                                <td><?= $v->nama_kabupaten ?></td>
                                <td><button type="button" data-id="<?= $v->id ?>" class="btn btn-primary btn-edit btn-sm me-1 mb-1"><i class="bi bi-pencil-square"></i> Edit</button> <button type="button" data-id="<?= $v->id ?>" class="btn btn-danger btn-delete btn-sm me-1 mb-1"><i class="bi bi-trash"></i> Hapus</button></td>
                            </tr>
                        <?php } ?>

                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal fade" id="modal-add" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-xl"
            role="document">
            <div class="modal-content">
                <form class="form form-horizontal" action="<?= base_url("user/send") ?>" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalCenterTitle">Add User
                        </h5>
                    </div>
                    <div class="modal-body" id="total-skor-container">
                        <input type="hidden" id="mode" name="mode" value="add">
                        <input type="hidden" id="id" name="id" value="">
                        <div class="row">
                            <div class="col-md-4">
                                <label class=" mt-2" for="name">Nama</label>
                            </div>
                            <div class="col-md-8">
                                <div class="my-1">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person h5"></i></span>
                                        <input type="text" class="form-control" placeholder="Name" id="name" name="name" value="" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class=" mt-2" for="username">Username</label>
                            </div>
                            <div class="col-md-8">
                                <div class="my-1">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person h5"></i></span>
                                        <input type="text" class="form-control" placeholder="Username" id="username" name="username" value="" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class=" mt-2" for="password">Password</label>
                            </div>
                            <div class="col-md-8">
                                <div class="my-1">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-lock h5"></i></span>
                                        <input type="password" class="form-control" placeholder="Password" id="password" name="password" value="" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class=" mt-2" for="email">Email</label>
                            </div>
                            <div class="col-md-8">
                                <div class="my-1">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-envelope-at h5"></i></span>
                                        <input type="email" class="form-control" placeholder="Email" id="email" name="email" value="" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class=" mt-2" for="role">Role</label>
                            </div>
                            <div class="col-md-8">
                                <div class="my-1">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person-rolodex h5"></i></span>
                                        <select class="choices form-select" id="role" name="role">
                                            <option value="" selected>(Pilih Role)</option>
                                            <option value="admin">Admin</option>
                                            <option value="provinsi">Provinsi</option>
                                            <option value="kabupaten">Kabupaten</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 prov-container d-none">
                                <label class=" mt-2" for="provinsi">Provinsi</label>
                            </div>
                            <div class="col-md-8 prov-container d-none">
                                <div class="my-1">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-map h5"></i></span>
                                        <select class="choices form-select" id="provinsi" name="provinsi">
                                            <option value="" selected>(Pilih Provinsi)</option>
                                            <?php foreach ($provinsi as $k => $v) { ?>
                                                <option value="<?= $v->kode_provinsi ?>"><?= $v->nama_provinsi ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 kab-container d-none">
                                <label class=" mt-2" for="kabupaten">Kabupaten</label>
                            </div>
                            <div class="col-md-8 kab-container d-none">
                                <div class="my-1">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-map h5"></i></span>
                                        <select class="choices form-select" id="kabupaten" name="kabupaten">
                                            <option value="" selected>(Pilih Kabupaten)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class=" mt-2" for="nip">NIP</label>
                            </div>
                            <div class="col-md-8">
                                <div class="my-1">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-file-earmark-binary h5"></i></span>
                                        <input type="number" class="form-control" placeholder="NIP" id="nip" name="nip" value="" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class=" mt-2" for="jabatan">Jabatan</label>
                            </div>
                            <div class="col-md-8">
                                <div class="my-1">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-award h5"></i></span>
                                        <input type="text" class="form-control" placeholder="Jabatan" id="jabatan" name="jabatan" value="" required>
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
        $('form').on('submit', function(e) {
            if (!$(this).data('submitted')) {
              $(this).data('submitted', true);
              $(this).find("button").addClass('disabled');
          }
          else {
              e.preventDefault();
          }
      });

        const prov_select = $('#provinsi')[0];
        const kab_select = $('#kabupaten')[0];
        const role_select = $('#role')[0];
        const $prov_select = $('#provinsi');
        const $kab_select = $('#kabupaten');
        const $role_select = $('#role');
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
        $("#table1").DataTable();
        $(document).on('click', '#add-btn', function(e) {
            $("#mode").val("add");
            $('#password').prop("required", true);
            $('#username').prop("disabled", false);
            $("#password").attr("placeholder", "Password");
            $("#id").val("");
            $('#modal-add').modal('show');
        });
        $(document).on('click', '.btn-delete', function(e) {
            var idu = $(this).data("id");
            Swal.fire({
                title: 'Yakin ingin menghapus user ini?',
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
                    window.location.href = '<?= base_url("user/delete/") ?>'+idu;
                }
            })
        });

        $(document).on('click', '.btn-edit', function(e) {
            $("#mode").val("edit");
            $('#password').prop("required", false);
            $('#username').prop("disabled", true);
            $("#password").attr("placeholder", "Kosongkan bila tidak ingin mengganti password!");
            $.ajax({
                type: "GET",
                url: "<?= base_url("user/load_user") ?>",
                data: {
                    id: $(this).data("id"),
                },
                dataType: "json",
                contentType: false,
                success: function(data)
                {
                    $("#id").val(data.id);
                    $("#name").val(data.name);
                    $("#username").val(data.username);
                    $("#email").val(data.email);
                    change_select_state(data.role);
                    choices3.setChoiceByValue(data.role);
                    choices.setChoiceByValue(data.kode_provinsi);
                    if(data.role == "kabupaten"){
                        var idkab = data.kode_kabupaten;
                        $.ajax({
                            type: "GET",
                            url: "<?= base_url("user/load_kabupaten") ?>",
                            data: {
                                id: data.kode_provinsi,
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
                                choices2.setChoiceByValue(idkab);
                            }
                        });
                    }

                    $("#nip").val(data.nip);
                    $("#jabatan").val(data.jabatan);
                    $('#modal-add').modal('show');
                }
            });
        });

        function change_select_state(elem)
        {
            if(elem == "provinsi"){
                $('.prov-container').removeClass("d-none");
                $('.kab-container').addClass("d-none");
                $('#provinsi').prop("required", true);
                $('#kabupaten').prop("required", false);
                choices2.setChoiceByValue('');
            } else if(elem == "kabupaten"){
                $('.prov-container').removeClass("d-none");
                $('.kab-container').removeClass("d-none");
                $('#provinsi').prop("required", true);
                $('#kabupaten').prop("required", true);
            } else {
                $('.prov-container').addClass("d-none");
                $('.kab-container').addClass("d-none");
                $('#provinsi').prop("required", false);
                $('#kabupaten').prop("required", false);
                choices.setChoiceByValue('');
                choices2.setChoiceByValue('');
            }
        }
        $(document).on('change', '#role', function(e) {
            change_select_state($(this).val());
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
    });
</script>