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

      <?php if($this->session->userdata("whs_create") == true){
      // || $this->session->userdata("whs_request_atk") == true){
        ?>
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
    <div class="mb-4 card card-body">
      <div class="fw-bold w-100 fs-3"><i class="fa fa-filter me-1"></i>Filter</div><hr class="m-0 p-1">
      <div class="row mb-1">
        <div class="col-4 col-sm-4 col-md-3 col-lg-2 datagrid-title d-flex" style="line-height: 2rem !important">
          <div>Category</div>
          <div class="ms-auto">:</div>
        </div>
        <div class="col-8 col-sm-8 col-md-6 col-lg-4 datagrid-content">
          <select class="form-select filterSelect" id="filterCategory">
            <option value="">-All Category-</option>
            <?php foreach($category as $k => $v){ ?>
              <option value="<?= $v->id_category ?>"><?= $v->category_name ?></option>
            <?php } ?>
          </select>
        </div>
      </div>
      <div class="row mb-1">
        <div class="col-4 col-sm-4 col-md-3 col-lg-2 datagrid-title d-flex" style="line-height: 2rem !important">
          <div>Inventory Item</div>
          <div class="ms-auto">:</div>
        </div>
        <div class="col-8 col-sm-8 col-md-6 col-lg-4 datagrid-content">
          <select class="form-select filterSelect" id="filterInventory">
            <option value="">-All Inventory Item-</option>
            <?php foreach($inventory as $k => $v){ ?>
              <option value="<?= $v->id_inventory_item ?>"><?= $v->inventory_item_name ?></option>
            <?php } ?>
          </select>
        </div>
      </div>
      <div class="row mb-1">
        <div class="col-4 col-sm-4 col-md-3 col-lg-2 datagrid-title d-flex" style="line-height: 2rem !important">
          <div>Unit</div>
          <div class="ms-auto">:</div>
        </div>
        <div class="col-8 col-sm-8 col-md-6 col-lg-4 datagrid-content">
          <select class="form-select filterSelect" id="filterUnit">
            <option value="">-All Unit-</option>
            <?php foreach($unit as $k => $v){ ?>
              <option value="<?= $v->id_unit ?>"><?= $v->unit_name ?></option>
            <?php } ?>
          </select>
        </div>
      </div>
    </div>
    <div class="row row-cards">
      <div class="col-lg-12">
        <div class="card">
          <div class="table-responsive">
            <table class="table table-vcenter card-table table-striped table-hover w-100 table-bordered" id="mytable">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Product Name</th>
                  <th>Category</th>
                  <th>Unit</th>
                  <th>Qty</th>
                  <th>Created</th>
                  <th>Description</th>
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
        <form action="<?= base_url("atk/add_stock_in") ?>" method="POST" enctype="multipart/form-data">
          <div class="modal-header">
            <h5 class="modal-title"><span id="modal-title-text"></span> <?= $title ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" class="form-control" name="id" required>
            <input type="hidden" class="form-control" name="mode" required>
            <div class="mb-1">
              <label class="form-label">Product Name</label>
              <input type="text" class="form-control" name="product_name" placeholder="Product name" required>
            </div>
            <div class="mb-1">
              <div class="form-label">Inventory Item</div>
              <select class="form-select" name="id_inventory_item" required>
                <option value="">-Select Inventory Item-</option>
                <?php foreach($inventory as $k => $v) { ?>
                  <option value="<?= $v->id_inventory_item ?>"><?= $v->inventory_item_name ?></option>
                <?php } ?>
              </select>
            </div>
            <div class="mb-1">
              <label class="form-label">Category</label>
              <input type="text" class="form-control" id="category_name" required readonly>
            </div>
            <div class="mb-1">
              <label class="form-label">Unit</label>
              <input type="text" class="form-control" id="unit_name" required readonly>
            </div>
            <?php $defaultPhoto = base_url("assets/static/empty-image.png"); ?>
            <div class="mb-1">
              <label class="form-label">Image</label>
              <div class="text-center"><img id="imgPreview" style="max-height: 120px" src="<?= $defaultPhoto ?>"></div>
              <input type="file" class="form-control" name="img" accept=".jpg,.jpeg,.png">
            </div>
            <div class="mb-1">
              <label class="form-label">Price</label>
              <input type="text" class="form-control rupiah" placeholder="Price" required maxlength="25">
              <input type="hidden" class="form-control" name="price" required>
            </div>
            <div class="mb-1">
              <label class="form-label">Description</label>
              <textarea type="text" class="form-control" name="description" placeholder="Category description"></textarea>
            </div>
            <div class="mb-1">
              <div class="form-label">Location</div>
              <select class="form-select" name="id_location" required>
                <option value="">-Select Location-</option>
                <?php foreach($location as $k => $v) { ?>
                  <option value="<?= $v->id_location ?>"><?= $v->location_name ?></option>
                <?php } ?>
              </select>
            </div>
            <div class="mb-1">
              <label class="form-label">Quantity</label>
              <input type="number" class="form-control" name="quantity" placeholder="Quantity" value="1" max="1000" min="1" required>
            </div>
            <div class="mb-1">
              <label class="form-label">Serial Number</label>
              <div id="serial-number-div" class="m-0 p-0">
                <input type="text" class="form-control serial-number" name="serial_number" placeholder="Serial Number" required>
              </div>
            </div>
            <div class="mb-1">
              <label class="form-label">Created Date</label>
              <input step="any" type="datetime-local" class="form-control" name="created_date" placeholder="Date" value="<?= date("Y-m-d H:i:s") ?>" required>
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
    $("#filterCategory").on("change", function() {
      var id = $(this).val();
      if(id == ""){
        id = 0;
      }
      $.ajax({
        url: "<?= base_url('atk/get_inventory_item_filter/') ?>"+id,
        cache: false,
        type: "GET",
        success: function(response){
          $("#filterInventory").html(response);
        },
        error: function(xhr) {
          alert("Failed to load data!");
        }
      });
    });
    $('input[name=quantity]').on('keypress', function(e){
      return e.metaKey || e.which <= 0 || e.which == 8 || /[0-9]/.test(String.fromCharCode(e.which));
    });
    $("input[name=img]").on("change", function() {
      if (this.files && this.files[0]) {
        var fileSize = this.files[0].size / 1024 / 1024;
        if(fileSize > 10){
          alert("File terlalu besar untuk diupload!");
          $(this).val(''); 
        }else{
          var reader = new FileReader();
          reader.onload = function(e) {
            $('#imgPreview').attr('src', e.target.result);
          }
          reader.readAsDataURL(this.files[0]);
        }
      } else {
        alert('Jenis file tidak didukung!');
        $('#imgPreview').attr('src', '<?= $defaultPhoto ?>');
      }
    });
    $(document).on('change', 'select[name=id_inventory_item]', function(){
      var id = $(this).val();
      if(id == ""){
        $('#category_name').val("");
        $('#unit_name').val("");
        return false;
      }
      $.ajax({
        url: "<?= base_url('atk/load_inventory_item/') ?>"+id,
        cache: false,
        type: "GET",
        success: function(response){
          $('#category_name').val(response.category_name);
          $('#unit_name').val(response.unit_name);
        },
        error: function(xhr) {
          alert("Failed to load data!");
        }
      });
    });
    $(document).on('keyup', '.rupiah', function(){
      $(this).val(formatRupiah($(this).val(), 'Rp. '));
      $("input[name=price]").val(formatRupiah($(this).val(), 'Rp. ').replaceAll(".","").replaceAll(" ","").replaceAll("Rp",""));
    });
    function formatRupiah(angka, prefix)
    {
      var number_string = angka.replace(/[^,\d]/g, '').toString(),
      split    = number_string.split(','),
      sisa     = split[0].length % 3,
      rupiah     = split[0].substr(0, sisa),
      ribuan     = split[0].substr(sisa).match(/\d{3}/gi);
      if (ribuan) {
        separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
      }
      rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
      return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
    }
    $(".filterSelect").on("change", function() {
      table.draw();
    });
    var table = $('#mytable').DataTable({
      "processing": true,
      "serverSide": true,
      "scrollX": true,
      "ajax": {
        complete: function(data) {
          console.log(data);
        },
        "url": "<?= base_url('atk/load_stock_in_dt') ?>",
        "dataType": "json",
        "type": "POST",
        data: function(d){
          d.filterCategory = $('#filterCategory').val();
          d.filterUnit = $('#filterUnit').val();
          d.filterInventory = $('#filterInventory').val();
        }
      },
      columns: [
        {name:'num'},
        {name:'product_name'},
        {name:'category_name'},
        {name:'unit_name'},
        {name:'quantity'},
        {name:'created_date'},
        {name:'description'},
        {name:'act'},
        ],
      columnDefs: [{
        "orderable": false,
        "searchable": false,
        "targets": [0,7],
      },{
        "className": "text-nowrap",
        "targets": [7],
      },{
        "className": "text-center",
        "targets": [0],
      }],
      "order": [
        [5, 'desc']
        ]
    });
    $("#modal-edit").on('show.bs.modal', function(e) {
      $('#imgPreview').attr('src', '<?= $defaultPhoto ?>');
      $('input[name=img]').val("");
      $('input[name=id]').val("");
      $('input[name=product_name]').val("");
      $('select[name=id_inventory_item]').val("");
      $('select[name=id_location]').val("");
      $('#category_name').val("");
      $('#unit_name').val("");
      $('input[name=price]').val("");
      $('.rupiah').val("");
      $('textarea[name=description]').val("");
      $('input[name=serial_number]').val("");

      $('input[name=quantity]').val(1);
      $('input[name=created_date]').val("<?= date("Y-m-d H:i:s") ?>");

      var btn = $(e.relatedTarget);
      if(btn.data("mode") == "Add"){
        $('#modal-title-text').text("Add");
        $('input[name=id]').prop("required", false);
        // $('input[name=img]').prop("required", true);
        $('input[name=mode]').val("Add");
      }
      if(btn.data("mode") == "Edit"){
        $('#modal-title-text').text("Edit");
        $('input[name=id]').prop("required", true);
        // $('input[name=img]').prop("required", false);
        $('input[name=mode]').val("Edit");
        $.ajax({
          url: "<?= base_url('atk/load_stock_in/') ?>"+btn.data("id"),
          cache: false,
          type: "GET",
          success: function(response) {
            $('input[name=id]').val(response.id_stock_in);
            $('input[name=product_name]').val(response.product_name);
            $('select[name=id_inventory_item]').val(response.id_inventory_item);
            $('select[name=id_location]').val(response.id_location);
            $('#category_name').val(response.category_name);
            $('#unit_name').val(response.unit_name);
            $('input[name=price]').val(response.price);
            $('.rupiah').val(formatRupiah(response.price, 'Rp. '));
            $('textarea[name=description]').val(response.description);
            $('input[name=quantity]').val(response.quantity);
            $('input[name=serial_number]').val(response.serial_number);
            $('input[name=created_date]').val(response.created_date);
            $.ajax({
              url: "<?= base_url('atk/load_stock/') ?>"+response.id_stock_in,
              cache: false,
              type: "GET",
              success: function(response2){
                response2.forEach(
                  function myFunction(item, index) {
                    if(item.img == ""){
                      $('#imgPreview').attr('src', '<?= $defaultPhoto ?>');
                    }else{
                      $('#imgPreview').attr('src', '<?= base_url("public/atk/") ?>'+item.img);
                    }
                    return false;
                  });
              }
            });
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
          window.location = "<?= base_url("atk/delete_stock_in/") ?>"+id;
        }
      });
    });

  });
</script>