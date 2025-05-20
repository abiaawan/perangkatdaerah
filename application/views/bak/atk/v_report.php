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
      <div class="row mb-1">
        <div class="col-4 col-sm-4 col-md-3 col-lg-2 datagrid-title d-flex" style="line-height: 2rem !important">
          <div>Start Date</div>
          <div class="ms-auto">:</div>
        </div>
        <div class="col-8 col-sm-8 col-md-6 col-lg-4 datagrid-content">
          <input type="date" class="form-control filterSelect" id="filterStartDate" value="<?= date("Y-m-d", strtotime(date("Y-m-d")." -7 days")) ?>">
        </div>
      </div>
      <div class="row mb-1">
        <div class="col-4 col-sm-4 col-md-3 col-lg-2 datagrid-title d-flex" style="line-height: 2rem !important">
          <div>End Date</div>
          <div class="ms-auto">:</div>
        </div>
        <div class="col-8 col-sm-8 col-md-6 col-lg-4 datagrid-content">
          <input type="date" class="form-control filterSelect" id="filterEndDate" value="<?= date("Y-m-d") ?>">
        </div>
      </div>
    </div>
    <div class="row row-cards">
      <div class="col-lg-12">
        <div class="d-flex justify-content-end mb-2">
          <button class="btn btn-primary" id="export-excel"><i class="fa-solid fa-table me-2"></i>Export to excel</button>
        </div>
        <div class="card">
          <div class="table-responsive">
            <table class="table table-vcenter card-table table-striped table-hover w-100 table-bordered" id="mytable">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Created On</th>
                  <th>Product Name</th>
                  <th>Serial Number</th>
                  <th>Stock</th>
                  <th>Unit</th>
                  <th>Source</th>
                  <th>Warehouse Location</th>
                  <th>Status</th>
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
    $("#filterEndDate").attr("min", $("#filterStartDate").val());
    $(".filterSelect").on("change", function() {
      $("#filterEndDate").attr("min", $("#filterStartDate").val());
      if($("#filterEndDate").val() < $("#filterStartDate").val()){
        $("#filterEndDate").val($("#filterStartDate").val());
      }
      table.draw();
    });
    $("#export-excel").on("click", function() {
      window.location = "<?= base_url("export/atk_report/") ?>?start=" + $("#filterStartDate").val() + "&end=" + $("#filterEndDate").val() + "&category=" + $("#filterCategory").val() + "&unit=" + $("#filterUnit").val() + "&inventory=" + $("#filterInventory").val();
    });
    var table = $('#mytable').DataTable({
      "processing": true,
      "serverSide": true,
      "scrollX": true,
      "ajax": {
        complete: function(data) {
          console.log(data);
        },
        "url": "<?= base_url('atk/load_report_dt') ?>",
        "dataType": "json",
        "type": "POST",
        data: function(d){
          d.filterCategory = $('#filterCategory').val();
          d.filterUnit = $('#filterUnit').val();
          d.filterInventory = $('#filterInventory').val();
          d.filterStartDate = $('#filterStartDate').val();
          d.filterEndDate = $('#filterEndDate').val();
        }
      },
      columns: [
        {name:'num'},
        {name:'transaction_date'},
        {name:'product_name'},
        {name:'serial_number'},
        {name:'stock'},
        {name:'unit_name'},
        {name:'source'},
        {name:'location_name'},
        {name:'info'},
        ],
      columnDefs: [{
        "orderable": false,
        "searchable": false,
        "targets": [0,4],
      },{
        "className": "text-center",
        "targets": [0],
      }],
      "order": [
        [1, 'desc']
        ]
    });
    
  });
</script>