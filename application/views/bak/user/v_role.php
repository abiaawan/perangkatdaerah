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
        <form action="<?= base_url("user/add_role") ?>" method="POST" enctype="multipart/form-data">
          <div class="modal-header">
            <h5 class="modal-title"><span id="modal-title-text"></span> <?= $title ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" class="form-control" name="id" required>
            <input type="hidden" class="form-control" name="mode" required>
            <div class="mb-1">
              <label class="form-label">Name</label>
              <input type="text" class="form-control" name="role_name" placeholder="Role name" required>
            </div>
            <div class="card">
              <div class="card-header p-1">Module</div>
              <div class="card-body">
                <label class="form-check">
                  <input class="form-check-input" type="checkbox" value="1" name="atk">
                  <span class="form-check-label">ATK</span>
                </label>
                <label class="form-check">
                  <input class="form-check-input" type="checkbox" value="1" name="bmn">
                  <span class="form-check-label">BMN</span>
                </label>
              </div>
            </div>
            <div class="card">
              <div class="card-header p-1">Access</div>
              <div class="card-body">
                <label class="form-check d-none">
                  <input class="form-check-input" type="checkbox" value="1" name="read">
                  <span class="form-check-label">Can View</span>
                </label>
                <label class="form-check">
                  <input class="form-check-input" type="checkbox" value="1" name="update">
                  <span class="form-check-label">Can Edit</span>
                </label>
                <label class="form-check">
                  <input class="form-check-input" type="checkbox" value="1" name="delete">
                  <span class="form-check-label">Can Delete</span>
                </label>
                <label class="form-check">
                  <input class="form-check-input" type="checkbox" value="1" name="create">
                  <span class="form-check-label">Can Create</span>
                </label>
                <label class="form-check">
                  <input class="form-check-input" type="checkbox" value="1" name="request_atk">
                  <span class="form-check-label">Can Request ATK</span>
                </label>
                <label class="form-check">
                  <input class="form-check-input" type="checkbox" value="1" name="accept_atk">
                  <span class="form-check-label">Can Approve ATK</span>
                </label>
              </div>
            </div>
            <div class="card">
              <div class="card-header p-1">Menu</div>
              <div class="card-body">
                <label class="form-check">
                  <input class="form-check-input" type="checkbox" value="1" name="stock_summary">
                  <span class="form-check-label">Stock Summary</span>
                </label>
                <label class="form-check">
                  <input class="form-check-input" type="checkbox" value="1" name="master_data">
                  <span class="form-check-label">Master Data</span>
                </label>
                <label class="form-check">
                  <input class="form-check-input" type="checkbox" value="1" name="stock_in">
                  <span class="form-check-label">Stock In</span>
                </label>
                <label class="form-check">
                  <input class="form-check-input" type="checkbox" value="1" name="stock_out">
                  <span class="form-check-label">Stock Out</span>
                </label>
                <label class="form-check">
                  <input class="form-check-input" type="checkbox" value="1" name="report">
                  <span class="form-check-label">Report</span>
                </label>
              </div>
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
        "url": "<?= base_url('user/load_role_dt') ?>",
        "dataType": "json",
        "type": "POST",
      },
      columns: [
        {name:'num'},
        {name:'role_name'},
        {name:'updated_date'},
        {name:'act'},
        ],
      columnDefs: [{
        "orderable": false,
        "searchable": false,
        "targets": [0,3],
      },{
        "className": "text-nowrap",
        "targets": [3],
      },{
        "className": "text-center",
        "targets": [0],
      }],
      "order": [
        [2, 'desc']
        ]
    });
    $("#modal-edit").on('show.bs.modal', function(e) {
      $('input[name=id]').val("");
      $('input[name=role_name]').val("");
      $('input[name=create]').prop("checked", 0);
      $('input[name=read]').prop("checked", 0);
      $('input[name=update]').prop("checked", 0);
      $('input[name=delete]').prop("checked", 0);
      $('input[name=stock_summary]').prop("checked", 0);
      $('input[name=master_data]').prop("checked", 0);
      $('input[name=stock_in]').prop("checked", 0);
      $('input[name=stock_out]').prop("checked", 0);
      $('input[name=report]').prop("checked", 0);
      $('input[name=bmn]').prop("checked", 0);
      $('input[name=atk]').prop("checked", 0);
      $('input[name=request_atk]').prop("checked", 0);
      $('input[name=accept_atk]').prop("checked", 0);

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
          url: "<?= base_url('user/load_role/') ?>"+btn.data("id"),
          cache: false,
          type: "GET",
          success: function(response) {
            $('input[name=id]').val(response.id_role);
            $('input[name=role_name]').val(response.role_name);
            $('input[name=create]').prop("checked", Number(response.create));
            $('input[name=read]').prop("checked", Number(response.read));
            $('input[name=update]').prop("checked", Number(response.update));
            $('input[name=delete]').prop("checked", Number(response.delete));
            $('input[name=stock_summary]').prop("checked", Number(response.stock_summary));
            $('input[name=master_data]').prop("checked", Number(response.master_data));
            $('input[name=stock_in]').prop("checked", Number(response.stock_in));
            $('input[name=stock_out]').prop("checked", Number(response.stock_out));
            $('input[name=report]').prop("checked", Number(response.report));
            $('input[name=bmn]').prop("checked", Number(response.bmn));
            $('input[name=atk]').prop("checked", Number(response.atk));
            $('input[name=request_atk]').prop("checked", Number(response.request_atk));
            $('input[name=accept_atk]').prop("checked", Number(response.accept_atk));
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
          window.location = "<?= base_url("user/delete_role/") ?>"+id;
        }
      });
    });

  });
</script>