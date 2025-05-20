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
                  <th>Name</th>
                  <th>Category</th>
                  <th>Unit</th>
                  <th>Stock</th>
                  <th>Total Price</th>
                  <th>Alert</th>
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
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><span id="modal-title-text"></span> View Stock</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-4 card card-body">
            <div class="fw-bold w-100 fs-3"><i class="fa fa-filter me-1"></i>Filter</div><hr class="m-0 p-1">
            <div class="row mb-1">
              <div class="col-4 col-sm-4 col-md-3 col-lg-2 datagrid-title d-flex" style="line-height: 2rem !important">
                <div>Location</div>
                <div class="ms-auto">:</div>
              </div>
              <div class="col-8 col-sm-8 col-md-6 col-lg-4 datagrid-content">
                <select class="form-select filterSelect2" id="filterLocation">
                  <option value="">-All Location-</option>
                  <?php foreach($location as $k => $v){ ?>
                    <option value="<?= $v->id_location ?>"><?= $v->location_name ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
          </div>
          <div class="d-flex justify-content-end mb-2">
            <button class="btn btn-primary me-2" id="print-qr"><i class="fa-solid fa-print me-2"></i>Print QR</button>
            <button class="btn btn-primary" id="export-excel"><i class="fa-solid fa-table me-2"></i>Export to excel</button>
          </div>
          <div class="table-responsive">
            <table class="table table-vcenter card-table table-striped table-hover w-100 table-bordered" id="mytable2">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Product Name</th>
                  <th>Serial Number</th>
                  <th>Date Stock In</th>
                  <th>Unit</th>
                  <th>Price</th>
                  <th>Source</th>
                  <th>Warehouse Location</th>
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
  <div class="modal modal-blur fade" id="modal-view" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><span id="modal-title-text"></span> View Item</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <?php $defaultPhoto = base_url("assets/static/empty-image.png"); ?>
          <div class="text-center"><img id="imgPreviewItem" style="max-height: 120px" src="<?= $defaultPhoto ?>"></div>
          <div class="row">
            <div class="col-4 datagrid-title" style="line-height: 1.4rem !important">Product Name</div>
            <div class="col-8 datagrid-content">: <span id="productNameTxt">xxxxxxxxxxxxx</span></div>
          </div>
          <div class="row">
            <div class="col-4 datagrid-title" style="line-height: 1.4rem !important">PIC</div>
            <div class="col-8 datagrid-content">: <span id="picTxt">xxxxxxxxxxxxx</span></div>
          </div>
          <div class="row">
            <div class="col-4 datagrid-title" style="line-height: 1.4rem !important">Category</div>
            <div class="col-8 datagrid-content">: <span id="categoryTxt">xxxxxxxxxxxxx</span></div>
          </div>
          <div class="row">
            <div class="col-4 datagrid-title" style="line-height: 1.4rem !important">Serial Number</div>
            <div class="col-8 datagrid-content">: <span id="serialTxt">xxxxxxxxxxxxx</span></div>
          </div>
          <div class="row">
            <div class="col-4 datagrid-title" style="line-height: 1.4rem !important">Description</div>
            <div class="col-8 datagrid-content">: <span id="descriptionTxt">xxxxxxxxxxxxx</span></div>
          </div>
          <div class="row">
            <div class="col-4 datagrid-title" style="line-height: 1.4rem !important">Date Stock In</div>
            <div class="col-8 datagrid-content">: <span id="dateInTxt">xxxxxxxxxxxxx</span></div>
          </div>
          <div class="row">
            <div class="col-4 datagrid-title" style="line-height: 1.4rem !important">Date Stock Out</div>
            <div class="col-8 datagrid-content">: <span id="dateOutTxt">xxxxxxxxxxxxx</span></div>
          </div>
          <div class="row">
            <div class="col-4 datagrid-title" style="line-height: 1.4rem !important">Location</div>
            <div class="col-8 datagrid-content">: <span id="locationTxt">xxxxxxxxxxxxx</span></div>
          </div>
          <div class="row">
            <div class="col-4 datagrid-title" style="line-height: 1.4rem !important">Status</div>
            <div class="col-8 datagrid-content">: <span id="statusTxt">xxxxxxxxxxxxx</span></div>
          </div>
          <div class="row">
            <div class="col-4 datagrid-title" style="line-height: 1.4rem !important">QR</div>
            <div class="col-8 datagrid-content"><span id="qrTxt">xxxxxxxxxxxxx</span></div>
          </div>
          <div class="d-flex justify-content-end">
            <?php if($this->session->userdata("whs_create") == true){
             // || $this->session->userdata("whs_request_atk") == true
             ?>
             <button class="btn btn-primary" id="addStockBtn"><i class="fa-solid fa-plus me-1"></i> Add Stock</button>
           <?php } ?>
         </div>
       </div>
     </div>
   </div>
 </div>
 <div class="modal modal-blur fade" id="modal-stock" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <form action="<?= base_url("atk/add_stock_in") ?>" method="POST" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title"><span id="modal-title-text"></span> <?= $title ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" class="form-control" name="mode" required value="Add">
          <input type="hidden" class="form-control" name="source" required value="summary">
          <div class="mb-1">
            <label class="form-label">Product Name</label>
            <input type="text" class="form-control" name="product_name" placeholder="Product name" required>
          </div>
          <div class="mb-1">
            <div class="form-label">Inventory Item</div>
            <select class="form-select" id="id_inventory_item" required disabled>
              <option value="">-Select Inventory Item-</option>
              <?php foreach($inventory as $k => $v) { ?>
                <option value="<?= $v->id_inventory_item ?>"><?= $v->inventory_item_name ?></option>
              <?php } ?>
            </select>
            <input type="hidden" class="form-control" name="id_inventory_item" required readonly>
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
            <input type="hidden" name="imgPrev" value="">
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

<div class="section-to-print" style="visibility: hidden;">
  <div>
    <div id="qrimg"></div>
    <div id="qrlabel"  style="padding-left: 5px"></div>
  </div>
  
</div>
<script type="text/javascript">
  $(document).ready(function(){
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
    $(document).on('keyup', '.rupiah', function(){
      $(this).val(formatRupiah($(this).val(), 'Rp. '));
      $("input[name=price]").val(formatRupiah($(this).val(), 'Rp. ').replaceAll(".","").replaceAll(" ","").replaceAll("Rp",""));
    });
    $("#export-excel").on("click", function() {
      window.location = "<?= base_url("export/atk_stock_export/") ?>?inventory=" + $(this).data("id") + "&location=" + $(this).val();;
    });
    $("input[name=img]").on("change", function() {
      if (this.files && this.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
          $('#imgPreview').attr('src', e.target.result);
        }
        reader.readAsDataURL(this.files[0]);
      } else {
        alert('Jenis file tidak didukung!');
        $('#imgPreview').attr('src', '<?= $defaultPhoto ?>');
      }
    });
    $(document).on('change', 'input[name=quantity]', function(){
      if($(this).val() <= 0){
        $(this).val(1);
      }
    });
    function openAddStockModal(btn){
      $.ajax({
        url: "<?= base_url('atk/load_stock_row/') ?>"+btn.data("id"),
        cache: false,
        type: "GET",
        success: function(response){
          $("input[name=product_name]").val(response.product_name);
          $("input[name=id_inventory_item]").val(response.id_inventory_item);
          $("#id_inventory_item").val(response.id_inventory_item);
          $("select[name=id_location]").val(response.id_location);
          $("input[name=imgPrev]").val(response.img);
          $("#category_name").val(response.category_name);
          $("#unit_name").val(response.unit_name);
          $("input[name=price]").val(response.price);
          $('.rupiah').val(formatRupiah(response.price, 'Rp. '));
          $("textarea[name=description]").val(response.description);

          if(response.img == ""){
            $('#imgPreview').attr('src', '<?= $defaultPhoto ?>');
          }else{
            $('#imgPreview').attr('src', '<?= base_url("public/atk/") ?>'+response.img);
          }
          let myModal = new bootstrap.Modal(document.getElementById('modal-stock'), {});
          myModal.show();
        },
        error: function(xhr) {
          alert("Failed to load data!");
        }
      });
    }
    function openViewModal(btn){
      $.ajax({
        url: "<?= base_url('atk/load_stock_row/') ?>"+btn.data("id"),
        cache: false,
        type: "GET",
        success: function(response) {

          if(response.img == ""){
            $('#imgPreviewItem').attr('src', '<?= $defaultPhoto ?>');
          }else{
            $('#imgPreviewItem').attr('src', '<?= base_url("public/atk/") ?>'+response.img);
          }
          $("#productNameTxt").text(response.product_name);
          $("#picTxt").text(response.pic);
          $("#categoryTxt").text(response.category_name);
          $("#serialTxt").text(response.serial_number);
          $("#descriptionTxt").text(response.description);
          $("#dateInTxt").text(response.created_date);
          $("#dateOutTxt").text(response.date_stock_out);
          $("#locationTxt").text(response.location_name);
          $("#statusTxt").text(response.status);
          $("#qrTxt").html(response.qr);
          $("#addStockBtn").data("id", response.id_stock);

          let myModal = new bootstrap.Modal(document.getElementById('modal-view'), {});
          myModal.show();
        },
        error: function(xhr) {
          alert("Failed to load data!");
        }
      });

    }
    $(document).on('click', '#addStockBtn', function(){
      openAddStockModal($(this));
    });
    $(document).on('click', '.view-stock-btn', function(){
      openViewModal($(this));
    });
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
        "url": "<?= base_url('atk/load_stock_summary_dt') ?>",
        "dataType": "json",
        "type": "POST",
        data: function(d){
          d.filterCategory = $('#filterCategory').val();
          d.filterUnit = $('#filterUnit').val();
        }
      },
      columns: [
        {name:'num'},
        {name:'inventory_item_name'},
        {name:'category_name'},
        {name:'unit_name'},
        {name:'stock'},
        {name:'price'},
        {name:'stock_alert'},
        {name:'act'},
        ],
      columnDefs: [{
        "orderable": false,
        "searchable": false,
        "targets": [0,6,7],
      },{
        "className": "text-nowrap",
        "targets": [6],
      },{
        "className": "text-center",
        "targets": [0],
      }],
      "order": [
        [1, 'asc']
        ]
    });
    var table2;
    $(".filterSelect2").on("change", function() {
      table2.draw();
    });
    $("#modal-edit").on('show.bs.modal', function(e) {
      var id = $(e.relatedTarget).data("id");
      $("#export-excel").data("id", id);
      $("#print-qr").data("id", id);
      table2 = $('#mytable2').DataTable({
        "bDestroy": true,
        "processing": true,
        "serverSide": true,
        "scrollX": true,
        "ajax": {
          complete: function(data) {
            console.log(data);
          },
          "url": "<?= base_url('atk/load_stock_list_dt/') ?>",
          "dataType": "json",
          "type": "POST",
          "data": function(d) {
            d.id = id;
            d.filterLocation = $('#filterLocation').val();
          }
        },
        columns: [
          {name:'num'},
          {name:'product_name'},
          {name:'serial_number'},
          {name:'created_date'},
          {name:'unit_name'},
          {name:'price'},
          {name:'stock_out'},
          {name:'location_name'},
          {name:'act'},
          ],
        columnDefs: [{
          "orderable": false,
          "searchable": false,
          "targets": [0,6,8],
        },{
          "className": "text-nowrap",
          "targets": [8],
        },{
          "className": "text-center",
          "targets": [0],
        }],
        "order": [
          [3, 'desc']
          ]
      });

    });
    $("#modal-edit").on('shown.bs.modal', function(e) {
      $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
    });
    $("#print-qr").on("click", function() {
      var btn = $(this);
      $.ajax({
        url: "<?= base_url('atk/load_qr/') ?>"+btn.data("id"),
        cache: false,
        type: "GET",
        dataType: "json",
        success: function(response){
          $("#qrimg").html(response.qr);
          $("#qrlabel").text(response.inventory_item_name);
          $("#qrimg").ready(function() {
            window.print();
          });
        },
        error: function(xhr) {
          alert("Failed to load data!");
        }
      });
      
    });
    

  });
</script>