<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <div class="col">
        <!-- Page pre-title -->
        <div class="page-pretitle">

        </div>
        <h2 class="page-title">
          <?= $title ?>
        </h2>
      </div>
      <?php if($this->session->userdata("whs_create") == true){ ?>
        <div class="col-auto ms-auto d-print-none">
          <div class="btn-list">
            <a href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#modal-edit" data-mode="Add">
              <i class="fa-solid fa-plus me-1"></i>
              Add <?= $title ?>
            </a>
            <a href="#" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#modal-edit" data-mode="Add" aria-label="Create new report">
              <i class="fa-solid fa-plus"></i>
            </a>
          </div>
        </div>
      <?php } ?>
    </div>
  </div>
</div>
<!-- Page body -->
<div class="page-body">
  <div class="container-xl">
    <div class="row row-cards">
      <div class="col-lg-12">
        <div class="card">
          <div class="table-responsive">
            <table class="table table-vcenter card-table table-striped table-hover w-100 table-bordered" id="mytable">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Name</th>
                  <th>Username</th>
                  <th>Email</th>
                  <th>NIP</th>
                  <th>Role</th>
                  <th>Unit Kerja</th>
                  <th>Alamat</th>
                  <th>Jabatan</th>
                  <th>Last Update</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>

              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal modal-blur fade" id="modal-edit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <div class="modal-content">
        <form action="<?= base_url("user/add_user") ?>" method="POST" enctype="multipart/form-data">
          <div class="modal-header">
            <h5 class="modal-title"><span id="modal-title-text"></span> <?= $title ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" class="form-control" name="id" required>
            <input type="hidden" class="form-control" name="mode" required>
            <div class="mb-1">
              <label class="form-label">Name</label>
              <input type="text" class="form-control" name="name" placeholder="Name" required>
            </div>
            <div class="mb-1">
              <label class="form-label">Username</label>
              <input type="text" class="form-control" name="username" placeholder="Username" required>
            </div>
            <div class="mb-1">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" name="email" placeholder="Email" required>
            </div>
            <div class="mb-1">
              <label class="form-label">NIP</label>
              <input type="number" class="form-control" name="nip" placeholder="NIP" required>
            </div>
            <div class="mb-1">
              <label class="form-label">Password</label>
              <input type="password" class="form-control" name="password" placeholder="Password" required>
            </div>
            <div class="mb-1">
              <div class="form-label">Role</div>
              <select class="form-select" name="id_role" required>
                <option value="">-Select Role-</option>
                <?php foreach($role as $k => $v) { ?>
                  <option value="<?= $v->id_role ?>"><?= $v->role_name ?></option>
                <?php } ?>
              </select>
            </div>
            <div class="mb-1">
              <div class="form-label">Unit Kerja</div>
              <select class="form-select" name="id_unit_kerja" required>
                <option value="">-Select Unit Kerja-</option>
                <?php foreach($unit_kerja as $k => $v) { ?>
                  <option value="<?= $v->id_unit_kerja ?>"><?= $v->unit_kerja_name ?></option>
                <?php } ?>
              </select>
            </div>
            <div class="mb-1">
              <label class="form-label">Alamat</label>
              <textarea type="text" class="form-control" name="alamat" placeholder="Alamat"></textarea>
            </div>
            <div class="mb-1">
              <label class="form-label">Jabatan</label>
              <input type="text" class="form-control" name="jabatan" placeholder="Jabatan">
            </div>
          </div>
          <div class="modal-footer">
            <a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">
              Cancel
            </a>
            <button class="btn btn-primary ms-auto" type="submit">
              <i class="fa-solid fa-paper-plane me-1"></i>
              Submit
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  $(document).ready(function(){
    $('input[name=nip]').on('keypress', function(e){
      return e.metaKey || e.which <= 0 || e.which == 8 || /[0-9]/.test(String.fromCharCode(e.which));
    });
    var table = $('#mytable').DataTable({
      "processing": true,
      "serverSide": true,
      "scrollX": true,
      "ajax": {
        complete: function(data) {
          console.log(data);
        },
        "url": "<?= base_url('user/load_user_dt') ?>",
        "dataType": "json",
        "type": "POST",
      },
      columns: [
        {name:'num'},
        {name:'name'},
        {name:'username'},
        {name:'email'},
        {name:'nip'},
        {name:'nama_role'},
        {name:'nama_unit_kerja'},
        {name:'alamat'},
        {name:'jabatan'},
        {name:'updated_date'},
        {name:'act'},
        ],
      columnDefs: [{
        "orderable": false,
        "searchable": false,
        "targets": [0,10],
      },{
        "className": "text-nowrap",
        "targets": [10],
      },{
        "className": "text-center",
        "targets": [0],
      }],
      "order": [
        [9, 'desc']
        ]
    });
    $("#modal-edit").on('show.bs.modal', function(e) {

      $('input[name=id]').val("");
      $('input[name=name]').val("");
      $('input[name=email]').val("");
      $('input[name=nip]').val("");
      $('input[name=username]').val("");
      $('input[name=password]').val("");
      $('select[name=id_category]').val("");
      $('select[name=id_unit]').val("");
      $('input[name=jabatan]').val("");
      $('textarea[name=alamat]').val("");
      var btn = $(e.relatedTarget);
      if(btn.data("mode") == "Add"){
        $('#modal-title-text').text("Add");
        $('input[name=password]').attr("placeholder", "Password");
        $('input[name=id]').prop("required", false);
        $('input[name=password]').prop("required", true);
        $('input[name=mode]').val("Add");
      }
      if(btn.data("mode") == "Edit"){
        $('#modal-title-text').text("Edit");
        $('input[name=id]').prop("required", true);
        $('input[name=password]').prop("required", false);
        $('input[name=password]').attr("placeholder", "Empty this field to ignore password changes");
        $('input[name=mode]').val("Edit");
        $.ajax({
          url: "<?= base_url('user/load_user/') ?>"+btn.data("id"),
          cache: false,
          type: "GET",
          success: function(response) {

            $('input[name=id]').val(response.id_user);
            $('input[name=name]').val(response.name);
            $('input[name=username]').val(response.username);
            $('input[name=email]').val(response.email);
            $('input[name=nip]').val(response.nip);
            $('select[name=id_role]').val(response.id_role);
            $('select[name=id_unit_kerja]').val(response.id_unit_kerja);
            $('input[name=jabatan]').val(response.jabatan);
            $('textarea[name=alamat]').val(response.alamat);
          },
          error: function(xhr) {
            alert("Failed to load data!");
          }
        });
      }
      $(".form-label").removeClass("required");
      $("select,input,textarea").filter("[required]").each( function(i, v) {
        $(this).prev(".form-label").addClass("required");
      });

    });
    $(document).on('click', '.btn-delete', function(){
      var id = $(this).data("id");
      Swal.fire({
        title: "Are you sure to delete?",
        text: "This action cannot be undone!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        confirmButtonText: "Yes"
      }).then((result) => {
        if (result.isConfirmed) {
          window.location = "<?= base_url("user/delete_user/") ?>"+id;
        }
      });
    });

  });
</script>