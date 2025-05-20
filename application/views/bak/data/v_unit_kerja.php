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
                  <th>Bagian</th>
                  <th>Lantai</th>
                  <th>Description</th>
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
        <form action="<?= base_url("data/add_unit_kerja") ?>" method="POST" enctype="multipart/form-data">
          <div class="modal-header">
            <h5 class="modal-title"><span id="modal-title-text"></span> <?= $title ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" class="form-control" name="id" required>
            <input type="hidden" class="form-control" name="mode" required>
            <div class="mb-1">
              <label class="form-label">Name</label>
              <input type="text" class="form-control" name="unit_kerja_name" placeholder="Unit Kerja name" required>
            </div>
            <div class="mb-1">
              <label class="form-label">Bagian</label>
              <input type="text" class="form-control" name="bagian" placeholder="Bagian" required>
            </div>
            <div class="mb-1">
              <label class="form-label">Lantai</label>
              <input type="text" class="form-control" name="lantai" placeholder="Lantai" required>
            </div>
            <div class="mb-1">
              <label class="form-label">Description</label>
              <textarea type="text" class="form-control" name="description" placeholder="Unit Kerja description"></textarea>
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
    var table = $('#mytable').DataTable({
      "processing": true,
      "serverSide": true,
      "scrollX": true,
      "ajax": {
        complete: function(data) {
          console.log(data);
        },
        "url": "<?= base_url('data/load_unit_kerja_dt') ?>",
        "dataType": "json",
        "type": "POST",
      },
      columns: [
        {name:'num'},
        {name:'unit_kerja_name'},
        {name:'bagian'},
        {name:'lantai'},
        {name:'description'},
        {name:'updated_date'},
        {name:'act'},
        ],
      columnDefs: [{
        "orderable": false,
        "searchable": false,
        "targets": [0,6],
      },{
        "className": "text-nowrap",
        "targets": [6],
      },{
        "className": "text-center",
        "targets": [0],
      }],
      "order": [
        [5, 'desc']
        ]
    });
    $("#modal-edit").on('show.bs.modal', function(e) {
      $('input[name=id]').val("");
      $('input[name=unit_kerja_name]').val("");
      $('textarea[name=description]').val("");
      $('input[name=bagian]').val("");
      $('input[name=lantai]').val("");
      var btn = $(e.relatedTarget);
      if(btn.data("mode") == "Add"){
        $('#modal-title-text').text("Add");
        $('input[name=id]').prop("required", false);
        $('input[name=mode]').val("Add");
      }
      if(btn.data("mode") == "Edit"){
        $('#modal-title-text').text("Edit");
        $('input[name=id]').prop("required", true);
        $('input[name=mode]').val("Edit");
        $.ajax({
          url: "<?= base_url('data/load_unit_kerja/') ?>"+btn.data("id"),
          cache: false,
          type: "GET",
          success: function(response) {

            $('input[name=id]').val(response.id_unit_kerja);
            $('input[name=unit_kerja_name]').val(response.unit_kerja_name);
            $('textarea[name=description]').val(response.description);
            $('input[name=bagian]').val(response.bagian);
            $('input[name=lantai]').val(response.lantai);
          },
          error: function(xhr) {
            alert("Failed to load data!");
          }
        });
      }

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
          window.location = "<?= base_url("data/delete_unit_kerja/") ?>"+id;
        }
      });
    });

  });
</script>