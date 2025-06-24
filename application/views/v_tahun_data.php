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
                <form action="<?= base_url("kunci/send_tahun") ?>" method="POST">
                    <div class="row">
                        <div class="col-md-4">
                            <label class=" mt-2" for="tahun">Pilih Tahun Pengisian Data</label>
                        </div>
                        <div class="col-md-8">
                            <div class="my-1">
                                <div class="form-group">
                                    <select class="choices form-select w-100" id="tahun" name="tahun">
                                        <option value="1970" <?= $data_tahun->tahun == "1970" ? "selected" : "" ?>>Stop Pengisian</option>
                                        <?php for ($i=2024; $i <= date("Y"); $i++) { ?>
                                            <option value="<?= $i ?>" <?= $data_tahun->tahun == $i ? "selected" : "" ?>><?= $i ?></option>
                                        <?php } ?>
                                    </select>
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
        const tahun_select = $('#tahun')[0];
        var choices = new Choices(tahun_select, {
            removeItemButton: true,
            itemSelectText: "",
        });
        $('form').on('submit', function(e) {
            if (!$(this).data('submitted')) {
                $(this).data('submitted', true);
                $(this).find("button").addClass('disabled');
            }
            else {
                e.preventDefault();
            }
        });

    });

</script>