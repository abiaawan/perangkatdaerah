<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><?= $header ?></h3>
                <!-- <p class="text-subtitle text-muted">Informasi Data Tematik adalah data untuk memberikan wawasan spesifik mengenai suatu topik atau tema dalam konteks geografis.</p> -->
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url("variable/$url") ?>"><?= $title ?></a></li>
                        <?php if($tipe_var == "sekda" || $tipe_var == "sekdprd" || $tipe_var == "inspektorat"){ ?>
                            <li class="breadcrumb-item active" aria-current="page"><?= $subtitle ?></li>
                        <?php }else{ ?>
                            <li class="breadcrumb-item" aria-current="page"><a href="<?= base_url("variable/$url") ?>"><?= $subtitle ?></a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?= $subsubtitle ?></li>
                        <?php } ?>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section">
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <form id="sheet-form" class="form form-horizontal" action="<?= base_url("variable/send_variable") ?>" method="POST">
                        <div class="form-body sheet">
                            <div class="row border">
                                <div class="col-md-8 border text-center pt-2">
                                    <h5>Indikator & Kelas Interval</h5>
                                </div>
                                <div class="col-md-4 border text-center pt-2">
                                    <h5>Upload Lampiran</h5>
                                </div>
                            </div>
                            <input type="hidden" name="title" value="<?= ($tipe_var == "sekda" || $tipe_var == "sekdprd" || $tipe_var == "inspektorat") ? $subtitle : $subtitle . ": ". $subsubtitle ?>">
                            <input type="hidden" name="tipe_var" value="<?= $tipe_var ?>">
                            <input type="hidden" name="tipe_soal" value="<?= $tipe_soal ?>">
                            <input type="hidden" name="id_badan" value="<?= $id_badan ?>">
                            <?php 
                            function find_answer($answer, $id_soal)
                            {
                                foreach ($answer as $k => $v) {
                                    if($v->id_soal == $id_soal){
                                        return $v->value;
                                    }
                                }
                                return "";
                            }
                            function find_file($answer, $id_soal, $status_jawaban, $check_tahun_pengisian)
                            {
                                foreach ($answer as $k => $v) {
                                    if($v->id_soal == $id_soal){
                                        if($v->upload <> ""){
                                            $deleteBtn = $status_jawaban->status == "submit" || $check_tahun_pengisian == false ? "" : '<button type="button" class="btn btn-danger delete-btn" data-id="'.$v->id_soal.'"><i class="bi bi-trash"></i> Hapus</button>';
                                            return '<button type="button" class="btn btn-success view-btn" data-file="'.$v->upload.'"><i class="bi bi-eye"></i> Lihat</button> <button type="button" class="btn btn-primary download-btn" data-file="'.$v->upload.'"><i class="bi bi-download"></i> Unduh</button> '.$deleteBtn;
                                        }
                                        return "";
                                    }
                                }
                                return "";
                            }
                            ?>
                            <?php foreach($soal as $k => $v){ ?>
                                <div class="row border">
                                    <div class="col-md-8 p-3">
                                        <h5><?= "{$v->no}. {$v->soal}" ?></h5>
                                        <div class="form-group">
                                            <input type="text" class="form-control answer shadow-none" placeholder="Ketik jawaban" data-id="<?= "{$v->kode_soal}-{$v->no}" ?>" <?= $status_jawaban->status == "submit" || $check_tahun_pengisian == false ? "disabled" : "" ?> value="">
                                            <input type="hidden" class="form-control true-answer shadow-none" name="ans[<?= "{$v->id_soal}" ?>]" id="<?= "{$v->kode_soal}-{$v->no}" ?>" placeholder="Ketik jawaban" <?= $status_jawaban->status == "submit" || $check_tahun_pengisian == false ? "disabled" : "" ?> value="<?= find_answer($jawaban, $v->id_soal) ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4 border p-3">
                                        <div class="text-center mb-3">
                                            <?php
                                            $findFile = find_file($jawaban, $v->id_soal, $status_jawaban, $check_tahun_pengisian);
                                            ?>
                                            <label for="upload_file_<?= $v->id_soal ?>"><span class="bi bi-file-earmark<?= $findFile <> "" ? "-check" : "" ?> icon-xl" id="uploadicon_<?= "{$v->id_soal}" ?>"></span></label>
                                        </div>
                                        <div class="text-center">

                                            <?php
                                            if($findFile <> ""){
                                                echo $findFile;
                                            }else{
                                                if($status_jawaban->status <> "submit" && $check_tahun_pengisian == true){
                                                    echo '<input type="file" id="upload_file_'.$v->id_soal.'" class="upload-file" data-id="'.$v->id_soal.'" accept="application/pdf"><div class="desc-upload"><small><small>* 10MB maximum</small></small></div>';
                                                }
                                            }
                                            ?>
                                            
                                        </div>
                                        <div class="progress progress-primary progress-sm">
                                            <div class="progress-bar progress-bar-striped upload-progress" id="uploadprogress_<?= "{$v->id_soal}" ?>" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="progress progress-primary progress-sm">
                                <div class="progress-bar progress-bar-striped" id="submitprogress" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="col-12 d-flex justify-content-end mt-3" id="submit-container">
                                <a href="<?= base_url("variable/$url") ?>" class="btn btn-secondary me-1 mb-1"><i class="bi bi-arrow-counterclockwise"></i> Kembali</a>
                                <?php
                                if($status_jawaban->status <> "submit"){
                                    if($check_tahun_pengisian == true){
                                        echo '<button type="submit" class="btn btn-primary me-1 mb-1 btn-submit" value="draft"><i class="bi bi-floppy"></i> Simpan Draft</button> <button type="submit" class="btn btn-success me-1 mb-1 btn-submit" value="submit"><i class="bi bi-send"></i> Submit</button>';
                                    }
                                }else{
                                    echo '<a href="'.base_url("variable/download_variable/").'?tipe_var='.$tipe_var.'&tipe_soal='.$tipe_soal.'&id_badan='.$id_badan.'" type="button" class="btn btn-success me-1 mb-1" download><i class="bi bi-download"></i> Unduh</a>';
                                }
                                ?>
                                
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-skor" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable"
            role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Add data variable <?= $tipe_soal ?> sukses!
                    </h5>
                </div>
                <div class="modal-body">
                    <p>
                        Berikut hasil penilaiannya:
                    </p>
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Indikator & Kelas Interval</th>
                                    <th>Skor</th>
                                </tr>
                            </thead>
                            <tbody id="skor-body">
                            </tbody>
                        </table>
                    </div>
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
    $(document).ready(function() {

        $(document).on('click', '.download-btn', function(e) {
            var link = document.createElement("a");
            link.download = name;
            <?php
            $tipeDaerah = $this->session->userdata('whs_role');
            $kodeDaerah = $this->session->userdata('whs_role') == "provinsi" ? $this->session->userdata('whs_kode_provinsi') : $this->session->userdata('whs_kode_kabupaten');
            ?>
            link.href = "<?= base_url("public/$tipeDaerah/$kodeDaerah/") ?>"+$(this).data("file");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            delete link;
        });
        $(document).on('click', '.delete-btn', function(e) {
            var upElem = $(this);
            var formData = new FormData();
            formData.append('tipe_var', $('input[name="tipe_var"]').val());
            formData.append('tipe_soal', $('input[name="tipe_soal"]').val());
            formData.append('id_badan', $('input[name="id_badan"]').val());
            formData.append('id', upElem.data("id"));
            $.ajax({
                type: "POST",
                url: "<?= base_url("variable/delete_file") ?>",
                data: formData,
                processData: false,
                contentType: false,
                success: function(data)
                {
                    upElem.parent().append('<input type="file" class="upload-file" data-id="'+upElem.data("id")+'" accept="application/pdf"><div><small><small>* 10MB maximum</small></small></div>');
                    const Toast = Swal.mixin({
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                          toast.addEventListener("mouseenter", Swal.stopTimer);
                          toast.addEventListener("mouseleave", Swal.resumeTimer);
                      },
                  });
                    Toast.fire({
                        icon: "success",
                        title: "File Berhasil dihapus!",
                    });
                    $("#uploadicon_"+upElem.data("id")).toggleClass("bi-file-earmark");
                    $("#uploadicon_"+upElem.data("id")).toggleClass("bi-file-earmark-check");
                    upElem.parent().find("button").remove();
                },
                error: function(data)
                {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener("mouseenter", Swal.stopTimer);
                            toast.addEventListener("mouseleave", Swal.resumeTimer);
                        },
                    });
                    Toast.fire({
                        icon: "error",
                        title: data.responseText,
                    });
                }
            });
        });
        $(document).on('change', '.upload-file', function(e) {
            if(this.files[0].size > 10485760) {
                alert("Besar file melebihi 10MB!");
                this.value = "";
                return;
            }
            if(this.files[0].type !== 'application/pdf') {
                alert("File harus berformat PDF!");
                this.value = "";
                return;
            }
            var upElem = $(this);
            var formData = new FormData();
            formData.append('file', this.files[0]);
            formData.append('tipe_var', $('input[name="tipe_var"]').val());
            formData.append('tipe_soal', $('input[name="tipe_soal"]').val());
            formData.append('id_badan', $('input[name="id_badan"]').val());
            formData.append('id', upElem.data("id"));
            $.ajax({
                xhr: function() {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function(evt){
                        if (evt.lengthComputable) {
                            var percentComplete = evt.loaded / evt.total;
                            $("#uploadprogress_"+upElem.data("id")).css("width", (percentComplete*100)+"%");
                            $("#uploadprogress_"+upElem.data("id")).attr("aria-valuenow", percentComplete*100);
                        }
                    }, false);

                    return xhr;
                },
                type: "POST",
                url: "<?= base_url("variable/upload_file") ?>",
                data: formData,
                processData: false,
                contentType: false,
                success: function(data)
                {

                    upElem.parent().append('<button type="button" class="btn btn-success view-btn" data-file="'+data+'"><i class="bi bi-eye"></i> Lihat</button> <button type="button" class="btn btn-primary download-btn" data-file="'+data+'"><i class="bi bi-download"></i> Unduh</button> <button type="button" class="btn btn-danger delete-btn" data-id="'+upElem.data("id")+'"><i class="bi bi-trash"></i> Hapus</button>');
                    const Toast = Swal.mixin({
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                          toast.addEventListener("mouseenter", Swal.stopTimer);
                          toast.addEventListener("mouseleave", Swal.resumeTimer);
                      },
                  });
                    Toast.fire({
                        icon: "success",
                        title: "File Berhasil ter-upload!",
                    });
                    $(".upload-progress").css("width", "0%");
                    $(".upload-progress").attr("aria-valuenow", 0);
                    $("#uploadicon_"+upElem.data("id")).toggleClass("bi-file-earmark");
                    $("#uploadicon_"+upElem.data("id")).toggleClass("bi-file-earmark-check");
                    upElem.parent().find("div").remove();
                    upElem.parent().find("input").remove();
                },
                error: function(data)
                {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener("mouseenter", Swal.stopTimer);
                            toast.addEventListener("mouseleave", Swal.resumeTimer);
                        },
                    });
                    Toast.fire({
                        icon: "error",
                        title: data.responseText,
                    });
                    $(".upload-progress").css("width", "0%");
                    $(".upload-progress").attr("aria-valuenow", 0);
                    upElem.parent().find("input").val("");
                }
            });
        });
$('.btn-submit').on('click', function(e) {
    e.preventDefault();
    var form = $("#sheet-form");
    var actionUrl = form.attr('action');
    var submitButtonValue = $(this).val();
    let isValid = true;
    if(submitButtonValue == "draft"){
        $('.answer').prop("required", false); 
        $('.true-answer').prop("required", false); 
        $('.upload-file').prop("required", false); 
    }else{
        $('.answer').prop("required", true);
        $('.true-answer').prop("required", true); 
        $('.upload-file').prop("required", true); 
        if (!form[0].checkValidity()) {
            isValid = false;
        }
    }
    if (!isValid) {
        form[0].reportValidity();
        return;
    }
    var formData = form.serialize();
    formData += '&submit_type=' + encodeURIComponent(submitButtonValue);
    $.ajax({
        xhr: function() {
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function(evt){
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    $("#submitprogress").css("width", (percentComplete*100)+"%");
                    $("#submitprogress").attr("aria-valuenow", percentComplete*100);
                }
            }, false);

            return xhr;
        },
        type: "POST",
        url: actionUrl,
        data: formData,
        success: function(data)
        {
            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true,
                didOpen: (toast) => {
                  toast.addEventListener("mouseenter", Swal.stopTimer);
                  toast.addEventListener("mouseleave", Swal.resumeTimer);
              },
          });
            Toast.fire({
                icon: "success",
                title: "Berhasil menyimpan!",
            });
            $("#submitprogress").css("width", "0%");
            $("#submitprogress").attr("aria-valuenow", 0);
            if(submitButtonValue == "submit"){
                data = JSON.parse(data);
                let html = "";
                let x = 1;
                for (const key in data) {
                    if (data.hasOwnProperty(key)) {
                        const item = data[key];
                        html += "<tr><td>" + x + ".</td><td>" + item.indikator + "</td><td>" + item.skor + "</td></tr>";
                        x++;
                    }
                }
                $("#skor-body").html(html);
                $(".answer").prop('disabled', true);
                $('.true-answer').prop("disabled", true); 
                $(".sheet").find("input[type='file']").remove();
                $(".sheet").find(".desc-upload").remove();
                $(".sheet").find(".delete-btn").remove();
                $(".sheet").find(".btn-submit").remove();
                $("#submit-container").append('<a href="<?= base_url("variable/download_variable/").'?tipe_var='.$tipe_var.'&tipe_soal='.$tipe_soal.'&id_badan='.$id_badan ?>" type="button" class="btn btn-success me-1 mb-1" download><i class="bi bi-download"></i> Unduh</a>');
                $('#modal-skor').modal('show');
            }
        },
        error: function(data)
        {
            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener("mouseenter", Swal.stopTimer);
                    toast.addEventListener("mouseleave", Swal.resumeTimer);
                },
            });
            Toast.fire({
                icon: "error",
                title: data.responseText,
            });
            $("#submitprogress").css("width", "0%");
            $("#submitprogress").attr("aria-valuenow", 0);
        }
    });
});
        // $('.answer').on('input', function(e) {
        //     maxLNumber(this, 20);
        // });
        // function maxLNumber(obj, len){
        //     let value = obj.value;
        //     const maxLength = len;
        //     let cleanedValue = value.replace(/\D/g, '');
        //     obj.value = cleanedValue;
        //     if (cleanedValue.length > maxLength) {
        //         obj.value = cleanedValue.slice(0, maxLength);
        //     }
        // }
const pdfModal = $('#pdfModal');
const pdfViewerIframe = $('#pdfViewer');
$(document).on( 'click','.view-btn', function(e) {
    e.preventDefault();
    const pdfUrl = "<?= base_url("public/$tipeDaerah/$kodeDaerah/") ?>"+$(this).data('file');
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

        // $(".answer").val($("#"+$(this).data("id")).val() != "" ? formatRupiah($(this)val(), '') : '');
$(".answer").val(function(){
    return $("#"+$(this).data("id")).val() != "" ? formatRupiah($("#"+$(this).data("id")).val(), '') : '';
});
$(document).on('keyup', '.answer', function(){
    $(this).val(formatRupiah($(this).val(), ''));
    $("#"+$(this).data("id")).val(formatRupiah($(this).val(), '').replaceAll(".","").replaceAll(" ",""));
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
            // rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    rupiah = split[1] != undefined ? rupiah : rupiah;
    return prefix == undefined ? rupiah : (rupiah ? '' + rupiah : '');
}
});
</script>
