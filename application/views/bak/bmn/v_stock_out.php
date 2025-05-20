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
        <div class="d-flex justify-content-end mb-2">
          <button class="btn btn-primary" id="export-word" data-bs-toggle="modal" data-bs-target="#modal-report"><i class="fa-solid fa-file me-2"></i>Create BAST</button>
        </div>
        <div class="card">
          <div class="table-responsive">
            <table class="table table-vcenter card-table table-striped table-hover w-100 table-bordered" id="mytable">
              <thead>
                <tr>
                  <th>No</th>
                  <th>PIC</th>
                  <th>Category</th>
                  <th>Inventory Item</th>
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
        <form action="<?= base_url("bmn/add_stock_out") ?>" method="POST" enctype="multipart/form-data">
          <div class="modal-header">
            <h5 class="modal-title"><span id="modal-title-text"></span> <?= $title ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" class="form-control" name="id" required>
            <input type="hidden" class="form-control" name="mode" required>
            <div class="mb-1">
              <label class="form-label">PIC</label>
              <input type="text" class="form-control" name="pic" placeholder="PIC" required readonly>
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
              <label class="form-label">Description</label>
              <textarea type="text" class="form-control" name="description" placeholder="Category description"></textarea>
            </div>
            <div class="mb-1">
              <label class="form-label">Quantity</label>
              <input type="number" class="form-control" name="quantity" placeholder="Quantity" value="1" max="1000" min="1" required>
            </div>
            <div class="mb-1">
              <label class="form-label">Serial Number</label>

              <div id="serial-number-div" class="m-0 p-0">
                <select class="form-select" name="serial_number[0]" required>
                  <option value="">-Select Serial Number 1-</option>
                </select>
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
  <div class="modal modal-blur fade" id="modal-report" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><span id="modal-title-text"></span> Create BAST</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="<?= base_url("export/bmn_bast") ?>" method="POST" class="bast" enctype="multipart/form-data">
            <div class="mb-4 card card-body">
              <div class="row">
                <div class="mb-1 col-md-12 col-sm-12">
                  <label class="form-label">PIC</label>
                  <select name="id_pic" class="form-select filterSelect2" id="filterPic" required>
                    <option value="">-Select PIC First-</option>
                    <?php foreach($pic as $k => $v){ ?>
                      <option value="<?= $v->id_pic ?>"><?= $v->pic ?></option>
                    <?php } ?>
                  </select>
                </div>
                <input type="hidden" name="data_export" required>
                <div class="mb-1 col-md-6 col-sm-12">
                  <label class="form-label">Tanggal</label>
                  <input type="date" class="form-control" name="export_date" required>
                </div>
                <div class="mb-1 col-md-6 col-sm-12">
                  <label class="form-label">No. Surat</label>
                  <input type="text" class="form-control" name="export_no_surat" required>
                </div>
                <div class="mb-1 col-md-6 col-sm-12">
                  <label class="form-label">Nama Penerima</label>
                  <input type="text" class="form-control" name="export_nama_penerima" required>
                </div>
                <div class="mb-1 col-md-6 col-sm-12">
                  <label class="form-label">NIP Penerima</label>
                  <input type="number" class="form-control" name="export_nip_penerima" required>
                </div>
                <div class="mb-1 col-md-6 col-sm-12">
                  <label class="form-label">Jabatan Penerima</label>
                  <input type="text" class="form-control" name="export_jabatan_penerima" required>
                </div>
                <div class="mb-1 col-md-6 col-sm-12">
                  <label class="form-label">Alamat Penerima</label>
                  <input type="text" class="form-control" name="export_alamat_penerima" required>
                </div>

                <div class="mb-1 col-md-6 col-sm-12">
                  <label class="form-label">Nama Ketua Tim Pengelola</label>
                  <input type="text" class="form-control" name="export_nama_ketua" required>
                </div>
                <div class="mb-1 col-md-6 col-sm-12">
                  <label class="form-label">NIP Ketua Tim Pengelola</label>
                  <input type="number" class="form-control" name="export_nip_ketua" required>
                </div>


              </div>
              <div class="table-responsive">
                <table class="table table-vcenter card-table table-striped table-hover w-100 table-bordered" id="mytable2">
                  <thead>
                    <tr>
                      <th>Select</th>
                      <th>PIC</th>
                      <th>Category</th>
                      <th>Inventory Item</th>
                      <th>Unit</th>
                      <th>Qty</th>
                      <th>Created</th>
                      <th>Description</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>

                  </tbody>
                </table>
              </div>
            </div>
            <div class="d-flex justify-content-end mb-2">
              <button class="btn btn-primary" id="create-bast-btn" type="submit"><i class="fa-solid fa-file me-2"></i>Create BAST</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  $(document).ready(function(){
    $("#create-bast-btn").on("click", function(e){
      if($("input[name=data_export]").val() == ""){
        e.preventDefault();
        alert("No item to report!");
      }
    });
    $('input[name=export_nip_penerima],input[name=export_nip_ketua]').on('keypress', function(e){
      return e.metaKey || e.which <= 0 || e.which == 8 || /[0-9]/.test(String.fromCharCode(e.which));
    });
    $("#filterCategory").on("change", function() {
      var id = $(this).val();
      if(id == ""){
        id = 0;
      }
      $.ajax({
        url: "<?= base_url('bmn/get_inventory_item_filter/') ?>"+id,
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
    var maxQ = 9;
    $(document).on('change', 'input[name=quantity]', function(){
      if($(this).val() <= 0){
        $(this).val(1);
      }
      if($(this).val() > maxQ){
        $(this).val(maxQ);
      }
      if($("select[name=id_inventory_item]").val() == ""){
        alert("Pilih inventory item terlebih dahulu!");
        $(this).val(1);
      }else{
        getSerial($("select[name=id_inventory_item]").val(), 0);
      }

    });
    function getSerial(id, id_out)
    {
      var idTxt = "?id="+id;
      var idOutTxt = (id_out > 0) ? "&id_out="+id_out : "";
      $.ajax({
        url: "<?= base_url('bmn/get_item_serial/') ?>"+idTxt+idOutTxt,
        cache: false,
        type: "GET",
        success: function(response){
          console.log(response);
          $("#serial-number-div").html('');
          let selectTxt = '';
          maxQ = response.length;
          var selectArr = [];
          var selectedRow = false;
          for (let i = 0; i < $("input[name=quantity]").val(); i++) {
            selectTxt = '<select class="form-select select-serial" name="serial_number['+i+']" required><option value="">-Select Serial Number '+(i+1)+'-</option>';
            selectedRow = false;
            response.forEach(
              function myFunction(item, index){
                let selected = "";
                if(item.stock_out == 1 && !selectArr.includes(item.id_stock) && selectedRow == false){
                  selected = " selected";
                  selectArr.push(item.id_stock);
                  selectedRow = true;
                }
                selectTxt = selectTxt + '<option value="'+item.id_stock+'"'+selected+'>'+item.serial_number+' - '+item.product_name+'</option>';
              });
            selectTxt = selectTxt + '</select>';
            $("#serial-number-div").append(selectTxt);
          }
        }
      });
    }
    $(document).on('change', '.select-serial', function(){
      var selected = $(this);
      if(selected.val() == ""){
        return false;
      }
      $(".select-serial").each(function(i,obj){
        if($(obj).attr("name") != selected.attr("name")){
          if($(obj).val() == selected.val()){
            selected.val("");
            alert("Tidak boleh memilih item yang sama!");
            return false;
          }
        }
      });
    });
    $(document).on('change', '.select-export', function(){
      var data = $("input[name=data_export]").val();
      var arr = data.split(',');
      if(data == ""){
        arr = [];
      }
      console.log(arr);
      if(arr.includes(String($(this).data("id")))){
        var index = arr.indexOf(String($(this).data("id")));
        if (index !== -1) {
          arr.splice(index, 1);
        }
      }else{
        arr.push($(this).data("id"));
      }
      $("input[name=data_export]").val(arr.join(","));
    });

    $(document).on('change', 'select[name=id_inventory_item]', function(){
      var id = $(this).val();
      if(id == ""){
        $('#category_name').val("");
        $('#unit_name').val("");
        return false;
      }
      $.ajax({
        url: "<?= base_url('bmn/load_inventory_item/') ?>"+id,
        cache: false,
        type: "GET",
        success: function(response){
          $('#category_name').val(response.category_name);
          $('#unit_name').val(response.unit_name);
          getSerial(id, $("input[name=id]").val());
        },
        error: function(xhr) {
          alert("Failed to load data!");
        }
      });
    });
    $(".filterSelect").on("change", function() {
      table.draw();
    });
    $(".filterSelect2").on("change", function() {
      $("#modal-report").find("input").val("");
      table2.draw();
    });
    var table = $('#mytable').DataTable({
      "processing": true,
      "serverSide": true,
      "scrollX": true,
      "ajax": {
        complete: function(data) {
          console.log(data);
        },
        "url": "<?= base_url('bmn/load_stock_out_dt') ?>",
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
        {name:'pic'},
        {name:'inventory_item_name'},
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
        "targets": [0,8],
      },{
        "className": "text-nowrap",
        "targets": [8],
      },{
        "className": "text-center",
        "targets": [0],
      }],
      "order": [
        [6, 'desc']
        ]
    });
    var table2 = $('#mytable2').DataTable({
      "processing": true,
      "serverSide": true,
      "scrollX": true,
      "ajax": {
        complete: function(data) {
          console.log(data);
        },
        "url": "<?= base_url('bmn/load_stock_out_dt') ?>",
        "dataType": "json",
        "type": "POST",
        data: function(d){
          d.filterCategory = $('#filterCategory').val();
          d.filterUnit = $('#filterUnit').val();
          d.filterInventory = $('#filterInventory').val();
          d.filterPic = $('#filterPic').val();
          d.export = true;
          d.dataExport = $('input[name=data_export]').val();
        }
      },
      "language": {"zeroRecords": "Select PIC First" },
      columns: [
        {name:'num'},
        {name:'pic'},
        {name:'inventory_item_name'},
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
        "targets": [0,8],
      },{
        "className": "text-nowrap",
        "targets": [8],
      },{
        "className": "text-center",
        "targets": [0],
      }],
      "order": [
        [6, 'desc']
        ]
    });
    $("#modal-edit").on('show.bs.modal', function(e) {
      $('input[name=id]').val("");
      $('select[name=id_inventory_item]').val("");
      $('#category_name').val("");
      $('#unit_name').val("");
      $('textarea[name=description]').val("");
      $("#serial-number-div").html('');
      $('input[name=quantity]').val(1);
      $('input[name=created_date]').val("<?= date("Y-m-d H:i:s") ?>");
      $('input[name=pic]').val("<?= $this->session->userdata("whs_name") ?>");
      $('select[name=id_inventory_item]').prop("disabled", false);

      var btn = $(e.relatedTarget);
      if(btn.data("mode") == "Add"){
        $('#modal-title-text').text("Add");
        $('input[name=id]').prop("required", false);
        $('input[name=mode]').val("Add");
      }
      if(btn.data("mode") == "Edit"){
        $('#modal-title-text').text("Edit");
        $('input[name=id]').prop("required", true);
        $('select[name=id_inventory_item]').prop("disabled", true);
        $('input[name=mode]').val("Edit");
        $.ajax({
          url: "<?= base_url('bmn/load_stock_out/') ?>"+btn.data("id"),
          cache: false,
          type: "GET",
          success: function(response) {
            $('input[name=id]').val(response.id_stock_out);
            $('select[name=id_inventory_item]').val(response.id_inventory_item);
            $('#category_name').val(response.category_name);
            $('#unit_name').val(response.unit_name);
            $('textarea[name=description]').val(response.description);
            $('input[name=quantity]').val(response.quantity);
            $('input[name=pic]').val(response.pic);
            $('input[name=created_date]').val(response.created_date);

            getSerial(response.id_inventory_item, response.id_stock_out);
          },
          error: function(xhr) {
            alert("Failed to load data!");
          }
        });
      }

    });

    $("#modal-report").on('show.bs.modal', function(e) {
      $(this).find("input").val("");
      $(this).find("select").val("");
      table2.draw();
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
          window.location = "<?= base_url("bmn/delete_stock_out/") ?>"+id;
        }
      });
    });
    $("#modal-report").on('shown.bs.modal', function(e) {
      $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
    });

  });
</script>