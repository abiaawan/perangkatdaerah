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
        <form action="<?= base_url("data/add_location") ?>" method="POST" enctype="multipart/form-data">
          <div class="modal-header">
            <h5 class="modal-title"><span id="modal-title-text"></span> <?= $title ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" class="form-control" name="id" required>
            <input type="hidden" class="form-control" name="mode" required>
            <div class="mb-1">
              <label class="form-label">Name</label>
              <input type="text" class="form-control" name="location_name" placeholder="Location name" required>
            </div>
            <div class="mb-1">
              <label class="form-label">Description</label>
              <textarea type="text" class="form-control" name="description" placeholder="Location description"></textarea>
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
        "url": "<?= base_url('data/load_location_dt') ?>",
        "dataType": "json",
        "type": "POST",
      },
      columns: [
        {name:'num'},
        {name:'location_name'},
        {name:'description'},
        {name:'updated_date'},
        {name:'act'},
        ],
      columnDefs: [{
        "orderable": false,
        "searchable": false,
        "targets": [0,4],
      },{
        "className": "text-nowrap",
        "targets": [4],
      },{
        "className": "text-center",
        "targets": [0],
      }],
      "order": [
        [3, 'desc']
        ]
    });
    $("#modal-edit").on('show.bs.modal', function(e) {
      $('input[name=id]').val("");
      $('input[name=location_name]').val("");
      $('textarea[name=description]').val("");
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
          url: "<?= base_url('data/load_location/') ?>"+btn.data("id"),
          cache: false,
          type: "GET",
          success: function(response) {

            $('input[name=id]').val(response.id_location);
            $('input[name=location_name]').val(response.location_name);
            $('textarea[name=description]').val(response.description);
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
          window.location = "<?= base_url("data/delete_location/") ?>"+id;
        }
      });
    });

  });
</script>