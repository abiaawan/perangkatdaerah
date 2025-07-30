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
                <form action="<?= base_url("bobot/send_bobot") ?>" method="POST">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="jml-penduduk">Variable Umum</label>
                        </div>
                        <div class="col-md-8">
                            <div class="my-1">
                                <div class="input-group">
                                    <input type="text" class="form-control input" placeholder="Variable Umum" id="umum" name="umum" value="<?= $data_bobot->variable_umum * 100 ?>" required>
                                    <span class="input-group-text" id="basic-addon2"><i class="bi bi-percent h5"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="jml-penduduk">Variable Teknis</label>
                        </div>
                        <div class="col-md-8">
                            <div class="my-1">
                                <div class="input-group">
                                    <input type="text" class="form-control input" placeholder="Variable Teknis" id="teknis" name="teknis" value="<?= $data_bobot->variable_teknis * 100 ?>" required>
                                    <span class="input-group-text" id="basic-addon2"><i class="bi bi-percent h5"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 d-flex justify-content-end mt-3 mb-3">
                        <button type="submit" id="submit-btn" class="btn btn-primary me-1 mb-1"><i class="bi bi-send"></i> Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
</div>
<script type="text/javascript">
    $( document ).ready(function() {
        $('form').on('submit', function(e) {
            if (!$(this).data('submitted')) {
                $(this).data('submitted', true);
                $(this).find("button").addClass('disabled');
            }
            else {
                e.preventDefault();
            }
        });
        $('#teknis').on('input', function(e) {
            maxLNumber(this, 2);
            $('#umum').val(100-$(this).val());
        });
        $('#umum').on('input', function(e) {
            maxLNumber(this, 2);
            $('#teknis').val(100-$(this).val());
        });
        function maxLNumber(obj, len){
            let value = obj.value;
            const maxLength = len;
            let cleanedValue = value.replace(/\D/g, '');
            obj.value = cleanedValue;
            if (cleanedValue.length > maxLength) {
                obj.value = cleanedValue.slice(0, maxLength);
            }
        }

    });

</script>