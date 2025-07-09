<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="page-heading mb-0">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><?= $title ?></h3>
                <p class="text-subtitle text-muted"></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?= $title ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label class=" mt-2" for="role">Data Pengisian</label>
                    </div>
                    <div class="col-md-8">
                        <div class="my-1">
                            <div class="form-group">
                                <select class="choices form-select w-100" id="role" name="role">
                                    <?php if($this->session->userdata("whs_role") <> "provinsi"){ ?>
                                        <option value="provinsi" selected>Provinsi</option>
                                    <?php } ?>
                                    <option value="kabupaten">Kabupaten</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label class=" mt-2" for="tahun">Tahun</label>
                    </div>
                    <div class="col-md-8">
                        <div class="my-1">
                            <div class="form-group">
                                <select class="choices form-select w-100" id="tahun" name="tahun">
                                    <?php for ($i=2024; $i <= date("Y")-1; $i++) { ?>
                                        <option value="<?= $i ?>" <?= $i==date("Y")-1 ? "selected" : "" ?>><?= $i ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if($this->session->userdata("whs_role") <> "provinsi"){ ?>
                    <div class="row">
                        <div class="col-md-4">
                            <label class=" mt-2" for="provinsi">Provinsi</label>
                        </div>
                        <div class="col-md-8">
                            <div class="my-1">
                                <div class="form-group">
                                    <select class="choices form-select w-100" id="provinsi" name="provinsi">
                                        <option value="" selected>(Pilih Provinsi)</option>
                                        <?php foreach ($provinsi as $k => $v) { ?>
                                            <option value="<?= $v->kode_provinsi ?>"><?= $v->nama_provinsi ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row d-none" id="kab-container">
                        <div class="col-md-4">
                            <label class=" mt-2" for="kabupaten">Kabupaten</label>
                        </div>
                        <div class="col-md-8">
                            <div class="my-1">
                                <div class="form-group">
                                    <select class="choices form-select w-100" id="kabupaten" name="kabupaten">
                                        <option value="" selected>(Pilih Kabupaten)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php }else{ ?>
                    <div class="row">
                        <div class="col-md-4">
                            <label class=" mt-2" for="provinsi">Provinsi</label>
                        </div>
                        <div class="col-md-8">
                            <div class="my-1">
                                <div class="form-group">
                                    <select class="choices form-select w-100" id="provinsi" name="provinsi">
                                        <?php foreach ($provinsi as $k => $v) { ?>
                                            <option value="<?= $v->kode_provinsi ?>" selected><?= $v->nama_provinsi ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="kab-container">
                        <div class="col-md-4">
                            <label class=" mt-2" for="kabupaten">Kabupaten</label>
                        </div>
                        <div class="col-md-8">
                            <div class="my-1">
                                <div class="form-group">
                                    <select class="choices form-select w-100" id="kabupaten" name="kabupaten">
                                        <option value="" selected>(Pilih Kabupaten)</option>
                                        <?php foreach ($kabupaten as $k => $v) { ?>
                                            <option value="<?= $v->kode_kabupaten ?>"><?= $v->nama_kabupaten ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <div class="col-12 d-flex justify-content-end mt-3 mb-3">
                    <button type="button" id="view-table-btn" class="btn btn-primary me-1 mb-1"><i class="bi bi-search"></i> Cari</button>
                </div>
                <div id="table-container">
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-total-skor" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" data-bs-focus="false">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-centered modal-dialog-scrollable"
        role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Approval
                </h5>
            </div>
            <div class="modal-body" id="total-skor-container">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary ms-1" data-bs-dismiss="modal">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">OK</span>
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-xl-custom">
        <div class="modal-content modal-content-custom">
            <div class="modal-header">
                <h5 class="modal-title" id="pdfModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 d-flex flex-grow-1">
                <iframe id="pdfViewer" class="modal-body-iframe"></iframe>
            </div>
        </div>
    </div>
</div>
</section>
</div>
<script type="text/javascript">
    $( document ).ready(function() {
        const role_select = $('#role')[0];
        const prov_select = $('#provinsi')[0];
        const kab_select = $('#kabupaten')[0];
        const $role_select = $('#role');
        const $prov_select = $('#provinsi');
        const $kab_select = $('#kabupaten');
        var choices_tahun = new Choices($('#tahun')[0], {
            removeItemButton: true,
            itemSelectText: "",
        });
        var choices = new Choices(prov_select, {
            removeItemButton: true,
            itemSelectText: "",
        });
        var choices2 = new Choices(kab_select, {
            removeItemButton: true,
            itemSelectText: "",
        });
        var choices3 = new Choices(role_select, {
            removeItemButton: true,
            itemSelectText: "",
            shouldSort: false,
        });
        $(document).on('change', '#role', function(e) {

            choices.setChoiceByValue('');
            choices2.setChoiceByValue('');
            if($("#role").val() == "kabupaten"){
                $("#kab-container").removeClass("d-none");
            }else{
                $("#kab-container").addClass("d-none");
            }
        });
        <?php if(isset($_GET['d'])){ ?>
            choices3.setChoiceByValue('<?= $_GET['d'] == "kabupaten" ? "kabupaten" : "provinsi"; ?>');
            $role_select.trigger('change');
        <?php } ?>
        var kabChanged = false;
        $(document).on('change', '#provinsi', function(e) {
            if($("#role").val() == "kabupaten"){
                $.ajax({
                    type: "GET",
                    url: "<?= base_url("user/load_kabupaten") ?>",
                    data: {
                        id: $(this).val(),
                    },
                    dataType: "json",
                    contentType: false,
                    success: function(data)
                    {
                        choices2.setChoices(
                            data,
                            'value',
                            'label',
                            true
                            );
                        <?php if(isset($_GET['k'])){ ?>
                            if(kabChanged == false){
                                choices2.setChoiceByValue('<?= (int)$_GET['k']; ?>');
                                $kab_select.trigger('change');
                                kabChanged = true;    
                            }
                        <?php } ?>
                        <?php if(isset($_GET['d'])){ ?>
                            <?php if($_GET['d'] == "kabupaten"){ ?>
                                setTimeout(function(){
                                    $("#view-table-btn").trigger("click");    
                                },100);

                            <?php } ?>
                        <?php } ?>
                    }
                });
            }
        });
        <?php if(isset($_GET['p'])){ ?>
            choices.setChoiceByValue('<?= (int)$_GET['p']; ?>');
            $prov_select.trigger('change');
            <?php if(isset($_GET['d'])){ ?>
                <?php if($_GET['d'] == "provinsi"){ ?>
                    setTimeout(function(){
                        $("#view-table-btn").trigger("click");    
                    },100);
                    
                <?php } ?>
            <?php } ?>
        <?php } ?>
        function check_validation()
        {
            if($("#provinsi").val() == ""){
                return false;
            }
            if($("#role").val() == "kabupaten" && $("#kabupaten").val() == ""){
                return false;
            }
            return true;
        }
        var table = $("table").DataTable();
        $(document).on('click', '#view-table-btn', function(e) {
            if(check_validation() == false)
            {
                alert("lengkapi pilihan terlebih dahulu!");
                return;
            }
            var upElem = $(this);
            var formData = new FormData();
            formData.append('role', $("#role").val());
            formData.append('tahun', $("#tahun").val());
            formData.append('provinsi', $("#provinsi").val());
            formData.append('kabupaten', $("#kabupaten").val());
            $.ajax({
                type: "POST",
                url: "<?= base_url("approval/get_approval") ?>",
                data: formData,
                processData: false,
                contentType: false,
                success: function(data)
                {
                    $("#table-container").html(data);
                    table.destroy();
                    table = $("table").DataTable({
                        "responsive": true,
                        "paging": false,
                        "searching": true,
                        "info": false,
                        "scrollX": true,
                        "pageLength": 1000,
                        "bPaginate": false,
                        "ordering": false,
                        "bLengthChange": false,
                    });
                }
            });
        });
        var title = "";
        $(document).on('click', '.view-skor-btn', function(e) {
            if(check_validation() == false)
            {
                alert("lengkapi pilihan terlebih dahulu!");
                return;
            }
            var upElem = $(this);
            var formData = new FormData();
            title = $(this).data("title");
            formData.append('tipe_var', $(this).data("var"));
            formData.append('tipe_soal', $(this).data("tipe"));
            formData.append('id_badan', $(this).data("badan"));
            formData.append('role', $(this).data("role"));
            formData.append('tahun', $(this).data("tahun"));
            formData.append('provinsi', $(this).data("prov"));
            formData.append('kabupaten', $(this).data("kab"));
            $.ajax({
                type: "POST",
                url: "<?= base_url("approval/view_approval") ?>",
                data: formData,
                processData: false,
                contentType: false,
                success: function(data)
                {
                    if(data == "404"){
                        alert("Soal belum dibuat!");
                    }else if(data == "204"){
                        alert("User belum submit data!");
                    }else if(data == "100"){
                        $("#skor-title").text($("#badan option:selected").text());
                        $('#modal-skor').modal('show');
                    }else{
                        $("#total-skor-container").html(data);
                        $('#modal-total-skor').modal('show');
                    }
                    
                }
            });
        });
        $(document).on('click', '.approve-btn', function(e){

            var upElem = $(this);
            var formData = new FormData();
            Swal.fire({
                title: 'Yakin ingin approve?',
                showDenyButton: true,
                confirmButtonText: 'Ya',
                denyButtonText: 'Tidak',
                customClass: {
                    actions: 'my-actions',
                    confirmButton: 'order-1',
                    denyButton: 'order-2',
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    formData.append('title', title);
                    formData.append('tipe_var', $(this).data("var"));
                    formData.append('tipe_soal', $(this).data("tipe"));
                    formData.append('id_badan', $(this).data("badan"));
                    formData.append('role', $(this).data("role"));
                    formData.append('tahun', $(this).data("tahun"));
                    formData.append('provinsi', $(this).data("prov"));
                    formData.append('kabupaten', $(this).data("kab"));
                    $.ajax({
                        type: "POST",
                        url: "<?= base_url("approval/approve") ?>",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(data)
                        {

                            $('#modal-total-skor').modal('hide');
                            $("#view-table-btn").trigger("click");
                        }
                    });
                }
            })

        });
        $(document).on('click', '.reject-btn', function(e){

            var upElem = $(this);
            var formData = new FormData();
            Swal.fire({
                title: 'Yakin ingin reject?',
                input: 'textarea',
                inputLabel: 'Alasan Penolakan',
                inputPlaceholder: 'Tuliskan alasan penolakan di sini...',
                inputAttributes: {
                    'aria-label': 'Tuliskan alasan penolakan di sini'
                },
                showDenyButton: true,
                confirmButtonText: 'Ya',
                denyButtonText: 'Tidak',
                customClass: {
                    actions: 'my-actions',
                    confirmButton: 'order-1',
                    denyButton: 'order-2',
                },
                didOpen: () => {
                    const textarea = Swal.getInput();
                    if (textarea) {
                        textarea.focus();
                    }
                },
                inputValidator: (value) => {
                    if (!value) {
                        return 'Anda harus mengisi alasan penolakan!'
                    }
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    formData.append('comment', result.value);
                    formData.append('title', title);
                    formData.append('tipe_var', $(this).data("var"));
                    formData.append('tipe_soal', $(this).data("tipe"));
                    formData.append('id_badan', $(this).data("badan"));
                    formData.append('role', $(this).data("role"));
                    formData.append('tahun', $(this).data("tahun"));
                    formData.append('provinsi', $(this).data("prov"));
                    formData.append('kabupaten', $(this).data("kab"));
                    $.ajax({
                        type: "POST",
                        url: "<?= base_url("approval/reject") ?>",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(data)
                        {

                            $('#modal-total-skor').modal('hide');
                            $("#view-table-btn").trigger("click");
                        }
                    });
                }
            })

        });
        const pdfModal = $('#pdfModal');
        const pdfViewerIframe = $('#pdfViewer');
        $(document).on( 'click','.view-btn', function(e) {
            e.preventDefault();
            const pdfUrl = "<?= base_url("public/") ?>"+$(this).data('daerah')+"/"+$(this).data('kodedaerah')+"/"+$(this).data('file');
            if (pdfUrl) {
                $("#pdfModalLabel").text('PDF Viewer');
                const viewerJsPath = '<?= base_url("assets/extensions/viewerjs/index.html") ?>';
                pdfViewerIframe.attr('src', `${viewerJsPath}#${pdfUrl}`);
                pdfModal.modal('show');
            } else {
                console.error('No PDF URL found for this item.');
            }
        });
        pdfModal.on('hidden.bs.modal', function () {
            pdfViewerIframe.attr('src', '');
        });
        $('#modal-total-skor').on('shown.bs.modal', function() {
            $(document).off('focusin.modal');
        });
        $(document).on( 'click','.rejected-span', function(e) {
            swal.fire({
                title: "Alasan Penolakan",
                text: $(this).data("comment"),
            });
        });
    });

</script>