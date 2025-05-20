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
    <div class="row row-cards">
      <div class="col-lg-12">
        <div class="card">
          <div class="table-responsive">
            <table class="table table-vcenter card-table table-striped table-hover w-100 table-bordered" id="mytable">
              <thead>
                <tr>
                  <th>No</th>
                  <th>User</th>
                  <th>Activity</th>
                  <th>Type</th>
                  <th>Inventory Item</th>
                  <th>Date</th>
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
    var table = $('#mytable').DataTable({
      "processing": true,
      "serverSide": true,
      "scrollX": true,
      "ajax": {
        complete: function(data) {
          console.log(data);
        },
        "url": "<?= base_url('data/load_logs_dt') ?>",
        "dataType": "json",
        "type": "POST",
      },
      columns: [
        {name:'num'},
        {name:'name'},
        {name:'activity'},
        {name:'type'},
        {name:'inventory_item_name'},
        {name:'date'},
        ],
      columnDefs: [{
        "orderable": false,
        "searchable": false,
        "targets": [0],
      },{
        "className": "text-center",
        "targets": [0],
      }],
      "order": [
        [5, 'desc']
        ]
    });

  });
</script>