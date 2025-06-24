<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style type="text/css">

</style>
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
                <a class="mb-2 col-btn" data-bs-toggle="collapse" href="#perbandingan" role="button" aria-expanded="false" aria-controls="perbandingan"><h6><i class="bi bi-caret-down-fill"></i> Perbandingan Data Tipelogi</h6></a>
                <div class="multi-collapse collapse show" id="perbandingan" style="">
                    <div class="row">
                        <div class="col-md-4">
                            <label class=" mt-2" for="role">Data Skor</label>
                        </div>
                        <div class="col-md-8">
                            <div class="my-1">
                                <div class="form-group">
                                    <select class="choices form-select w-100" id="role" name="role">
                                        <option value="provinsi" selected>Provinsi</option>
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
                    <div class="row">
                        <div class="col-md-4">
                            <label class=" mt-2" for="provinsi">Provinsi</label>
                        </div>
                        <div class="col-md-8">
                            <div class="my-1">
                                <div class="form-group">
                                    <select class="choices form-select w-100 multiple-remove" id="provinsi" name="provinsi" multiple="multiple">
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
                                    <select class="choices form-select w-100 multiple-remove" id="kabupaten" name="kabupaten" multiple="multiple">
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="var-container">
                        <div class="col-md-4">
                            <label class=" mt-2" for="var">Perangkat Daerah</label>
                        </div>
                        <div class="col-md-8">
                            <div class="my-1">
                                <div class="form-group extra-height">
                                    <select class="choices form-select w-100 multiple-remove" id="var" name="var" multiple="multiple">
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 d-flex justify-content-end mt-3 mb-3">
                        <button type="button" id="view-chart-btn" class="btn btn-primary me-1 mb-1"><i class="bi bi-eye-fill"></i> Tampilkan</button>
                    </div>
                    <div class="col-md-12 d-none" id="chart-container">
                        <div class="card">
                            <div class="card-body">
                                <div>
                                    <div id="bar"></div>
                                </div>

                                <div id="color-legend">
                                    <div>
                                        <span class="legend-color-box" style="background-color: #00c852;"></span>
                                        <span>Tipe A (> 800)</span>
                                    </div>
                                    <div>
                                        <span class="legend-color-box" style="background-color: #02b7d5;"></span>
                                        <span>Tipe B (601 - 800))</span>
                                    </div>
                                    <div>
                                        <span class="legend-color-box" style="background-color: #fed402;"></span>
                                        <span>Tipe C (401 - 600))</span>
                                    </div>
                                    <div>
                                        <span class="legend-color-box" style="background-color: #f46e02;"></span>
                                        <span>Setingkat Bidang (301 - 400))</span>
                                    </div>
                                    <div>
                                        <span class="legend-color-box" style="background-color: #de2b00;"></span>
                                        <span>Setingkat Seksi/Subbidang(< 300))</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <a class="mb-2 col-btn" data-bs-toggle="collapse" href="#pengelompokkan" role="button" aria-expanded="false" aria-controls="pengelompokkan"><h6><i class="bi bi-caret-down-fill"></i> Pengelompokkan Data Tipelogi Berdasarkan Perangkat Daerah</h6></a>
                <div class="multi-collapse collapse show" id="pengelompokkan" style="">
                    <div class="row">
                        <div class="col-md-4">
                            <label class=" mt-2" for="role2">Data Skor</label>
                        </div>
                        <div class="col-md-8">
                            <div class="my-1">
                                <div class="form-group">
                                    <select class="choices form-select w-100" id="role2" name="role2">
                                        <option value="provinsi" selected>Provinsi</option>
                                        <option value="kabupaten">Kabupaten</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label class=" mt-2" for="tahun2">Tahun</label>
                        </div>
                        <div class="col-md-8">
                            <div class="my-1">
                                <div class="form-group">
                                    <select class="choices form-select w-100" id="tahun2" name="tahun2">
                                        <option value="2024" selected>2024</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row d-none" id="prov-container">
                        <div class="col-md-4">
                            <label class=" mt-2" for="provinsi2">Filter Provinsi</label>
                        </div>
                        <div class="col-md-8">
                            <div class="my-1">
                                <div class="form-group">
                                    <select class="choices form-select w-100" id="provinsi2" name="provinsi2">
                                        <option value="" selected>(Pilih Provinsi)</option>
                                        <?php foreach ($provinsi as $k => $v) { ?>
                                            <option value="<?= $v->kode_provinsi ?>"><?= $v->nama_provinsi ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label class=" mt-2" for="var2">Perangkat Daerah</label>
                        </div>
                        <div class="col-md-8">
                            <div class="my-1">
                                <div class="form-group">
                                    <select class="choices form-select w-100" id="var2" name="var2">
                                        <option value="" selected>(Pilih Nomenklatur Perangkat Daerah)</option>
                                        <option value="sekda">Sekretariat Daerah</option>
                                        <option value="sekdprd">Sekretariat DPRD</option>
                                        <option value="inspektorat">Inspektorat</option>
                                        <option value="dinas">Dinas</option>
                                        <option value="badan">Badan</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row d-none" id="perda-container">
                        <div class="col-md-4">
                        </div>
                        <div class="col-md-8">
                            <div class="my-1">
                                <div class="form-group">
                                    <select class="choices form-select w-100" id="badan2" name="badan2">
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 d-flex justify-content-end mt-3 mb-3">
                        <button type="button" id="view-chart-btn2" class="btn btn-primary me-1 mb-1"><i class="bi bi-eye-fill"></i> Tampilkan</button>
                    </div>
                    <div class="d-none" id="pie-container">
                        <div class="card">
                            <div class="card-body">
                                <div class="row" id="pie-parent">

                                </div>
                                <div id="table-box" class="d-none">
                                    <table class="table table-bordered mb-0 border">
                                        <thead>
                                            <th class="text-capitalize">No</th>
                                            <th class="text-capitalize" id="name-text">Provinsi</th>
                                            <th class="text-capitalize">Skor</th>
                                        </thead>
                                        <tbody id="table-body">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>
<script src="<?= base_url("assets") ?>/extensions/apexcharts/apexcharts.min.js"></script>
<script type="text/javascript">
    $( document ).ready(function() {
        const role_select = $('#role')[0];
        const tahun_select = $('#tahun')[0];
        const prov_select = $('#provinsi')[0];
        const kab_select = $('#kabupaten')[0];
        const var_select = $('#var')[0];
        const $role_select = $('#role');
        const $tahun_select = $('#tahun');
        const $prov_select = $('#provinsi');
        const $kab_select = $('#kabupaten');
        const $var_select = $('#var');

        const role_select2 = $('#role2')[0];
        const tahun_select2 = $('#tahun2')[0];
        const prov_select2 = $('#provinsi2')[0];
        const var_select2 = $('#var2')[0];
        const badan_select2 = $('#badan2')[0];
        const $role_select2 = $('#role2');
        const $tahun_select2 = $('#tahun2');
        const $prov_select2 = $('#provinsi2');
        const $var_select2 = $('#var2');
        const $badan_select2 = $('#badan2');

        var choices = new Choices(prov_select, {
            maxItemCount: 5,
            delimiter: ",",
            itemSelectText: "",
            editItems: true,
            removeItemButton: true,
        });
        var choices2 = new Choices(kab_select, {
            maxItemCount: 5,
            delimiter: ",",
            itemSelectText: "",
            editItems: true,
            removeItemButton: true,
        });
        var choices3 = new Choices(role_select, {
            removeItemButton: true,
            itemSelectText: "",
            shouldSort: false,
        });
        var choices4 = new Choices(var_select, {
            maxItemCount: 10,
            delimiter: ",",
            itemSelectText: "",
            editItems: true,
            removeItemButton: true,
            shouldSort: false,
        });
        var choices5 = new Choices(tahun_select, {
            removeItemButton: true,
            itemSelectText: "",
            shouldSort: false,
        });


        var choices6 = new Choices(prov_select2, {
            maxItemCount: 5,
            delimiter: ",",
            itemSelectText: "",
            editItems: true,
            removeItemButton: true,
        });
        var choices7 = new Choices(role_select2, {
            removeItemButton: true,
            itemSelectText: "",
            shouldSort: false,
        });
        var choices8 = new Choices(tahun_select2, {
            removeItemButton: true,
            itemSelectText: "",
            shouldSort: false,
        });
        var choices9 = new Choices(var_select2, {
            removeItemButton: true,
            itemSelectText: "",
            shouldSort: false,
        });
        var choices10 = new Choices(badan_select2, {
            removeItemButton: true,
            itemSelectText: "",
            shouldSort: false,
        });
        $(document).on('click', '.col-btn', function(e) {
            if($(this).hasClass("collapsed")){
                $(this).find("i").removeClass("bi-caret-down-fill");
                $(this).find("i").addClass("bi-caret-right-fill");
            }else{
                $(this).find("i").removeClass("bi-caret-right-fill");
                $(this).find("i").addClass("bi-caret-down-fill");
            }
        });
        $(document).on('change', '#role', function(e) {
            choices.removeActiveItems();
            choices2.removeActiveItems();
            choices4.removeActiveItems();
            if($("#role").val() == "kabupaten"){
                $("#kab-container").removeClass("d-none");
            }else{
                $("#kab-container").addClass("d-none");
            }
        });
        $(document).on('change', '#role2', function(e) {
            choices6.removeActiveItems();
            choices9.setChoiceByValue('');
            choices10.setChoiceByValue('');
            if($("#role2").val() == "kabupaten"){
                $("#prov-container").removeClass("d-none");
            }else{
                $("#prov-container").addClass("d-none");
            }
        });
        $(document).on('change', '#provinsi', function(e) {
            if($("#role").val() == "kabupaten"){
                $.ajax({
                    type: "GET",
                    url: "<?= base_url("dashboard/load_kabupaten") ?>",
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
                    }
                });
            }
        });
        $(document).on('change', '#var2', function(e) {
            if($(this).val() == "dinas" || $(this).val() == "badan" || $(this).val() == "kecamatan"){
                $("#perda-container").removeClass("d-none");
                $.ajax({
                    type: "GET",
                    url: "<?= base_url("tipelogi/get_badan") ?>",
                    data: {
                        tahun: $("#tahun").val(),
                        type: $(this).val(),
                        daerah: $("#role").val(),
                        kab: $("#kabupaten").val(),
                    },
                    dataType: "json",
                    contentType: false,
                    success: function(data)
                    {
                        choices10.setChoices(
                            data,
                            'value',
                            'label',
                            true
                            );
                    }
                });
            }else{
                $("#perda-container").addClass("d-none");
                choices10.setChoiceByValue('');
            }
        });
        function load_perangkat(){
            choices4.removeActiveItems();
            $.ajax({
                type: "GET",
                url: "<?= base_url("dashboard/load_perangkat") ?>",
                data: {
                    kabupaten: $("#kabupaten").val(),
                    role: $("#role").val(),
                    tahun: $("#tahun").val(),
                },
                dataType: "json",
                contentType: false,
                success: function(data)
                {
                    choices4.setChoices(data,'value','label',true);
                }
            });
        }
        $(document).on('change', '#kabupaten', function(e) {
            if($("#role").val() == "kabupaten"){
                load_perangkat()
            }
        });
        $(document).on('change', '#provinsi', function(e) {
            if($("#role").val() == "provinsi"){
                load_perangkat()
            }
        });
        var colorSet = ['#1b5594', '#a9225a', '#edd300', "#49354e", "#4a4742"];
        let chartInstance = null;
        var pieInstances = [];
        Chart.plugins.register(ChartDataLabels);
        var barColors = ['#1b5594', '#a9225a', '#edd300', "#49354e", "#4a4742"];
        $(document).on('click', '#view-chart-btn2', function(e) {
            var tableBox = $('#table-box');
            tableBox.addClass("d-none");
            role_val = $("#role2").val();
            tahun_val = $("#tahun2").val();
            prov_val = $("#provinsi2").val();
            var_val = $("#var2").val();
            badan_val = $("#badan2").val();
            deleteChart();
            $.ajax({
                type: "GET",
                url: "<?= base_url("dashboard/load_pie") ?>",
                data: {
                    role: $("#role2").val(),
                    tahun: $("#tahun2").val(),
                    provinsi: $("#provinsi2").val(),
                    var: $("#var2").val(),
                    badan: $("#badan2").val(),
                },
                dataType: "json",
                contentType: false,
                success: function(data)
                {
                    createChart(data.text, data.value, `Chart Nilai Topologi`);
                    $("#pie-container").removeClass("d-none");
                }
            });
        });
        function createChart(xValues, yValues, title) {
            var $chartContainer = $('#pie-parent');
            var uniqueCanvasId = 'pieA_' + Date.now(); 
            var $colDiv = $('<div>').addClass('pie-col').addClass('pie-col-extra').addClass('col-md-12');

            var $canvas = $('<canvas>').attr('id', uniqueCanvasId).css({
                'width': '100%',
                'height': '100%'
            });
            $colDiv.append($canvas);
            $chartContainer.append($colDiv);
            var ctx = $canvas[0].getContext('2d');
            var displayXValues = xValues;
            var displayYValues = yValues;
            var displayBarColors = barColors;

            if (yValues.every(val => val === 0)) {
                displayXValues = ["No Data"];
                displayYValues = [1];
                displayBarColors = ["#cccccc"];
            }

            var newChart  = new Chart(ctx, {
                type: "pie",
                data: {
                    labels: displayXValues,
                    datasets: [{
                        backgroundColor: displayBarColors,
                        data: displayYValues
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    title: {
                        display: true,
                        text: title,
                        fontSize: 20
                    },
                    legend: {
                        display: true,
                        position: "right"
                    },
                    plugins: {
                        datalabels: {
                            display: true,
                            color: '#fff',
                            anchor: 'center',
                            align: 'start',
                            formatter: function(value, context) {
                                if (yValues.every(val => val === 0)) {
                                    return "No Data";
                                }

                                if (value === 0) {
                                    return '';
                                }

                                return context.chart.data.labels[context.dataIndex];
                            }
                        }
                    },
                    onClick: function(event, activeElements) {
                        if (activeElements.length > 0) {
                            var clickedElementIndex = activeElements[0]._index;
                            var label = this.data.labels[clickedElementIndex];
                            var value = this.data.datasets[0].data[clickedElementIndex];
                            var color = this.data.datasets[0].backgroundColor[clickedElementIndex];
                            $(document).trigger('pieSliceClicked', {
                                label: label,
                                value: value,
                                index: clickedElementIndex
                            });

                        }
                    }
                }
            });
            pieInstances.push(newChart);
        }
        $(document).on('pieSliceClicked', function(event, data) {
            var tableBox = $('#table-box');
            var tableBody = $('#table-body');
            if(data.label != "No Data"){
                $('#name-text').text(role_val);
                $.ajax({
                    type: "GET",
                    url: "<?= base_url("dashboard/load_pie_table") ?>",
                    data: {
                        role: role_val,
                        tahun: tahun_val,
                        provinsi: prov_val,
                        var: var_val,
                        badan: badan_val,
                        skor: data.label
                    },
                    dataType: "json",
                    contentType: false,
                    success: function(data)
                    {
                        let no = 1;
                        tableBody.empty();
                        $.each(data, function(provinceName, score) {
                            const row = $('<tr>');

                            row.append($('<td>').text(no++));
                            row.append($('<td>').text(provinceName));
                            row.append($('<td>').text(score));

                            tableBody.append(row);
                        });
                        tableBox.removeClass("d-none");
                    }
                });
            }
            

        });
        function deleteChart() {
            pieInstances.forEach(function(chart) {
                chart.destroy();
            });
            pieInstances = [];
            $('#pie-parent').find('.pie-col').remove(); 
        }
        $(document).on('click', '#view-chart-btn', function(e) {
            $.ajax({
                type: "GET",
                url: "<?= base_url("dashboard/load_chart") ?>",
                data: {
                    role: $("#role").val(),
                    tahun: $("#tahun").val(),
                    kabupaten: $("#kabupaten").val(),
                    provinsi: $("#provinsi").val(),
                    var: $("#var").val(),
                },
                dataType: "json",
                contentType: false,
                success: function(data)
                {
                    var series = [];
                    let i = 0;
                    for (const prov in data.series) {
                        series.push({
                            name: prov,
                            data: data.series[prov].map((value, index) => ({
                                x: prov,
                                y: value,
                                fillColor: getBarColor(value),
                                strokeColor: colorSet[i]
                            }))
                        });
                        i++;
                    }
                    var options = {
                        series: series,
                        chart: {
                            type: 'bar',
                            height: 350,
                            toolbar: {
                                show: true
                            }
                        },
                        plotOptions: {
                            bar: {
                                horizontal: false,
                                columnWidth: '40%',
                                endingShape: 'rounded',
                                dataLabels: {
                                    position: 'bottom',
                                },
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            formatter: function (val) {
                                if (val === 0) {
                                    return ["tidak", "ada", "data"];
                                }
                                return val;
                            },
                            offsetY: 20,
                            style: {
                                fontSize: '11px',
                                colors: ["#304758"]
                            }
                        },
                        stroke: {
                            show: true,
                            width: 4,
                            colors: ['transparent'],
                            curve: 'straight',
                            lineCap: 'butt'
                        },
                        xaxis: {
                            categories: data.categories,
                            labels: {
                                style: {
                                    fontSize: '12px'
                                }
                            }
                        },
                        yaxis: {
                            title: {
                                text: 'Skor'
                            }
                        },
                        fill: {
                            opacity: 1
                        },
                        tooltip: {
                            y: {
                                formatter: function (val) {
                                    return val + " (" + getTipe(val) + ")";
                                }
                            }
                        },
                        legend: {
                            position: 'bottom',
                            horizontalAlign: 'center',
                            markers: {
                                strokeColor: colorSet,
                                strokeWidth: 1,
                                fillColors: ['#FFFFFF', '#FFFFFF', '#FFFFFF', '#FFFFFF', '#FFFFFF']
                            },
                            labels: {
                                useSeriesColors: false
                            },
                            onItemClick: {
                                toggleDataSeries: true
                            },
                            onItemHover: {
                                highlightDataSeries: true
                            },

                        }
                    };
                    if (chartInstance) {
                        chartInstance.destroy();
                    }
                    chartInstance = new ApexCharts(document.querySelector("#bar"), options);
                    chartInstance.render();
                    $("#chart-container").removeClass("d-none");
                }
            });
});
function getBarColor(value) {
    if(value > 800){
        return "#00c852";
    }else if(value > 600){
        return "#02b7d5";
    }else if(value > 400){
        return "#fed402";
    }else if(value > 300){
        return "#f46e02";
    }else{
        return "#de2b00";
    }
}
function getTipe(value) {
    if(value > 800){
        return "tipe A";
    }else if(value > 600){
        return "tipe B";
    }else if(value > 400){
        return "tipe C";
    }else if(value > 300){
        return "setingkat bidang";
    }else{
        return "setingkat seksi/subbidang";
    }
}
});

</script>