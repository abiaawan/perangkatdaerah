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
    <div class="row row-card">
      <div class="col-lg-12">
        <div class="card">
          <div class="col-lg-12 p-5">
            <?php if($item){ ?>
              <?php $defaultPhoto = base_url("assets/static/empty-image.png"); ?>
              <div class="text-center"><img id="imgPreviewItem" style="max-height: 120px" src="<?= ($item->img <> "") ? base_url("public/".$item->type."/").$item->img : $defaultPhoto ?>"></div>
              <div class="row">
                <div class="col-4 datagrid-title" style="line-height: 1.4rem !important">Product Name</div>
                <div class="col-8 datagrid-content">: <span id="productNameTxt"><?= $item->product_name ?></span></div>
              </div>
              <div class="row">
                <div class="col-4 datagrid-title" style="line-height: 1.4rem !important">PIC</div>
                <div class="col-8 datagrid-content">: <span id="picTxt"><?= $item->pic ?></span></div>
              </div>
              <div class="row">
                <div class="col-4 datagrid-title" style="line-height: 1.4rem !important">Category</div>
                <div class="col-8 datagrid-content">: <span id="categoryTxt"><?= $item->category_name ?></span></div>
              </div>
              <div class="row">
                <div class="col-4 datagrid-title" style="line-height: 1.4rem !important">Serial Number</div>
                <div class="col-8 datagrid-content">: <span id="serialTxt"><?= $item->serial_number ?></span></div>
              </div>
              <div class="row">
                <div class="col-4 datagrid-title" style="line-height: 1.4rem !important">Description</div>
                <div class="col-8 datagrid-content">: <span id="descriptionTxt"><?= $item->description ?></span></div>
              </div>
              <div class="row">
                <div class="col-4 datagrid-title" style="line-height: 1.4rem !important">Date Stock In</div>
                <div class="col-8 datagrid-content">: <span id="dateInTxt"><?= $item->created_date ?></span></div>
              </div>
              <div class="row">
                <div class="col-4 datagrid-title" style="line-height: 1.4rem !important">Date Stock Out</div>
                <div class="col-8 datagrid-content">: <span id="dateOutTxt"><?= $item->date_stock_out ?></span></div>
              </div>
              <div class="row">
                <div class="col-4 datagrid-title" style="line-height: 1.4rem !important">Location</div>
                <div class="col-8 datagrid-content">: <span id="locationTxt"><?= $item->location_name ?></span></div>
              </div>
              <div class="row">
                <div class="col-4 datagrid-title" style="line-height: 1.4rem !important">Status</div>
                <div class="col-8 datagrid-content">: <span id="statusTxt"><?= $item->status ?></span></div>
              </div>
              <div class="row">
                <div class="col-4 datagrid-title" style="line-height: 1.4rem !important">QR</div>
                <div class="col-8 datagrid-content"><span id="qrTxt"><?= $item->qr ?></span></div>
              </div>
              <div class="d-flex justify-content-end">
                <?php if($this->session->userdata("whs_create") == true){
                 ?>
                 <button class="btn btn-primary" id="addStockBtn"><i class="fa-solid fa-plus me-1"></i> Add Stock</button>
               <?php } ?>
             <?php }else{echo "Item not found!";} ?>
           </div>
         </div>
       </div>
     </div>
   </div>
 </div>
 <?php if($item->type == "atk"){ ?>
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
<?php }elseif($item->type == "bmn"){ ?>
  <div class="modal modal-blur fade" id="modal-stock" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <div class="modal-content">
        <form action="<?= base_url("bmn/add_stock_in") ?>" method="POST" enctype="multipart/form-data">
          <div class="modal-header">
            <h5 class="modal-title"><span id="modal-title-text">Add</span> Stock In</h5>
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
                <input type="text" class="form-control serial-number" name="serial_number[0]" placeholder="Serial Number 1" required>
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
<?php } ?>
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
      <?php if($item->type == "bmn"){ ?>
        $("#serial-number-div").html("");
        for (let i = 0; i < $(this).val(); i++) {
          $("#serial-number-div").append('<input type="text" class="form-control serial-number" name="serial_number['+i+']" placeholder="Serial Number '+(i+1)+'" required>');
        } 
      <?php } ?>
    });
    $(document).on('click', '#addStockBtn', function(){
      openAddStockModal($(this));
    });
    function openAddStockModal(){
      $.ajax({
        url: "<?= base_url(($item->type ?? "").'/load_stock_row/') ?>"+<?= $item->id_stock ?? "" ?>,
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
  });
</script>