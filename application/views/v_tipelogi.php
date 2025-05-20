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
                        <li class="breadcrumb-item"><a href="#">Tipelogi</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?= $title ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section">
        <div class="card">
            <div class="card-body m-0 p-1">
                <div class="row" id="filter-sheet">
                    <div class="col-md-4 mb-0">
                        <h6>Pilih Provinsi</h6>
                        <div class="form-group">
                            <select class="choices form-select" id="provinsi">
                                <option value="" selected>(Pilih Provinsi)</option>
                                <?php foreach ($provinsi as $k => $v) { ?>
                                    <option value="<?= $v->kode_provinsi ?>"><?= $v->nama_provinsi ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 mb-0">
                        <h6>Pilih Tahun</h6>
                        <div class="form-group">
                            <select class="choices form-select" id="tahun">
                                <?php for ($i=2024; $i <= date("Y")-1; $i++) { ?>
                                    <option value="<?= $i ?>" selected><?= $i ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="mb-3 d-none" id="back-sheet">
                    <button class="btn btn-secondary" id="back-btn"><i class="bi bi-arrow-counterclockwise"></i> Kembali</button>
                </div>
                <div class="row">
                    <div class="col-md-12" id="map-sheet">
                        <div id="map" class="leaflet-map"></div>
                    </div>
                    <div class="col-md-8 d-none" id="detail-sheet">
                        <div class="text-center">
                            <h6 id="judul-daerah"></h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th colspan="2">Informasi Umum</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>Jumlah Penduduk</th>
                                        <td id="text-penduduk"></td>
                                    </tr>
                                    <tr>
                                        <th>Kepadatan</th>
                                        <td id="text-kepadatan"></td>
                                    </tr>
                                    <tr>
                                        <th>Luas Wilayah</th>
                                        <td id="text-luas"></td>
                                    </tr>
                                    <tr>
                                        <th>APBD</th>
                                        <td id="text-apbd"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="p-2 mt-2">
                            <h6>Nomenklatur Perangkat Daerah</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="hidden" id="daerah">
                                    <input type="hidden" id="id_provinsi">
                                    <input type="hidden" id="id_kabupaten">
                                    <div class="form-group" id="select-prov">
                                        <select class="form-select" id="perangkat">
                                            <option value="" selected>(Pilih Nomenklatur Perangkat Daerah)</option>
                                            <option value="sekda">Sekretariat Daerah</option>
                                            <option value="sekdprd">Sekretariat DPRD</option>
                                            <option value="inspektorat">Inspektorat</option>
                                            <option value="dinas">Dinas</option>
                                            <option value="badan">Badan</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group" id="select-subperangkat">
                                        <select class="form-select" id="subperangkat">
                                            <option value="" selected>(Pilih)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-start mt-1">
                                <button class="btn btn-primary" id="cari-skor"><i class="bi bi-send"></i> Submit</button>
                            </div>
                            <div class="border rounded mt-4 p-3 d-none" id="skor-sheet">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p id="title-skor"></p>
                                        <h5 id="total-skor">Skor: </h5>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="m-0">Keterangan:</h6>
                                        <p class="m-0">A = > 800</p>
                                        <p class="m-0">B = 600 - 800</p>
                                        <p class="m-0">C = 401 - 600</p>
                                        <p class="m-0">Bidang = 300 - 400</p>
                                        <p class="m-0">Seksi/Subbidang = < 300</p>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>
</div>
<script type="text/javascript">
    $( document ).ready(function() {
        const prov_select = $('#provinsi')[0];
        const year_select = $('#tahun')[0];
        const perangkat_select = $('#perangkat')[0];
        const subperangkat_select = $('#subperangkat')[0];
        const $prov_select = $('#provinsi');
        const $year_select = $('#tahun');
        const $perangkat_select = $('#perangkat');
        const $subperangkat_select = $('#subperangkat');
        var choices = new Choices(prov_select, {
            removeItemButton: true,
            itemSelectText: "",
        });
        var choices2 = new Choices(year_select, {
            itemSelectText: "",
        });
        var choices3 = new Choices(perangkat_select, {
            removeItemButton: true,
            itemSelectText: "",
            shouldSort: false,
        });
        var choices4 = new Choices(subperangkat_select, {
            removeItemButton: true,
            itemSelectText: "",
        });

        var indoLatLong = [-1.9118907,117.9811181];
        var map = L.map('map').setView(indoLatLong, 5);

        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
        }).addTo(map);
        var province = L.layerGroup();
        var state = L.layerGroup();
        var selectedpoint = L.layerGroup();
        load_provinsi();
        $(document).on('change', '#provinsi', function(e) {
            if($(this).val() == ""){
                map.removeLayer(state);
                map.invalidateSize();
                map.setView(indoLatLong, 5, {
                  "animate": true,
                  "pan": {
                    "duration": 5
                }
            });
                load_provinsi();
            }else{
                state.clearLayers();
                map.removeLayer(province);
                load_kabupaten($(this).val());
            }
        });
        $(document).on('change', '#perangkat', function(e) {
            if($(this).val() == "dinas" || $(this).val() == "badan" || $(this).val() == "kecamatan"){
                $("#select-subperangkat").removeClass("d-none");
                $.ajax({
                    type: "GET",
                    url: "<?= base_url("tipelogi/get_badan") ?>",
                    data: {
                        tahun: $("#tahun").val(),
                        type: $(this).val(),
                        daerah: $("#daerah").val(),
                        kab: $("#id_kabupaten").val(),
                    },
                    dataType: "json",
                    contentType: false,
                    success: function(data)
                    {
                        choices4.setChoices(
                            data,
                            'value',
                            'label',
                            true
                            );
                    }
                });
            }else{
                $("#select-subperangkat").addClass("d-none");
            }
        });
        $(document).on('click', '#cari-skor', function(e) {
            if($("#perangkat").val() == ""){
                alert("Pilih Nomenklatur Perangkat Daerah terlebih dahulu!");
                return;
            }
            if($("#perangkat").val() == "dinas" || $("#subperangkat").val() == "badan" || $("#subperangkat").val() == "kecamatan"){
                if($("#subperangkat").val() == ""){
                    alert("Pilih Nomenklatur Perangkat Daerah terlebih dahulu!");
                    return;
                }
            }

            $.ajax({
                type: "GET",
                url: "<?= base_url("tipelogi/cari_skor") ?>",
                data: {
                    id_prov: $("#id_provinsi").val(),
                    id_kab: $("#id_kabupaten").val(),
                    daerah: $("#daerah").val(),
                    perangkat: $("#perangkat").val(),
                    subperangkat: $("#subperangkat").val(),
                    tahun: $("#tahun").val(),
                },
                dataType: "json",
                contentType: false,
                success: function(data)
                {
                    $("#skor-sheet").removeClass("d-none");
                    var perangkat = "";
                    var title = "";
                    var namaDaerah = $("#judul-daerah").text();
                    if($("#perangkat").val() == "sekda" || $("#perangkat").val() == "sekdprd" || $("#perangkat").val() == "inspektorat"){
                        title = $("#perangkat"+" option:selected").text()+" "+namaDaerah;
                    }else{
                        title = $("#perangkat"+" option:selected").text()+" "+ucwords(strtolower($("#subperangkat option:selected").text()))+" "+namaDaerah;
                    }
                    $("#title-skor").text(title);
                    $("#total-skor").text("Skor: "+data);
                }
            });
        });
        $(document).on('click', '#back-btn', function(e) {
            $("#filter-sheet").removeClass("d-none");
            $("#back-sheet").addClass("d-none");
            $("#detail-sheet").addClass("d-none");
            $("#map-sheet").toggleClass("col-md-12");
            $("#map-sheet").toggleClass("col-md-4");
            choices.setChoiceByValue('');
            map.removeLayer(selectedpoint);
            map.removeLayer(state);
            selectedpoint.clearLayers();
            state.clearLayers();
            setTimeout(function (){
                map.invalidateSize();
                map.setView(indoLatLong, 5, {
                  "animate": true,
                  "pan": {
                    "duration": 5
                }
            });
            }, 1000);
            province.addTo(map);


        });
        $(document).on('click', '.btn-detail', function(e) {
            map.closePopup();
            var elem = $(this);
            $("#select-subperangkat").addClass("d-none");
            $("#id_provinsi").val(elem.data("id_provinsi"));
            $("#id_kabupaten").val(elem.data("id_kabupaten"));
            $("#daerah").val(elem.data("daerah"));
            $("#text-penduduk").text(elem.data("penduduk") + " jiwa");
            $("#text-kepadatan").text(elem.data("kepadatan") + " jiwa/km²");
            $("#text-luas").text(elem.data("luas") + " km²");
            var rp = formatRupiah(parseInt(elem.data("apbd")), 'Rp. ');
            $("#text-apbd").text(rp != "" ? rp : "-");
            if(elem.data("daerah") == "provinsi"){
                $("#judul-daerah").text("Provinsi "+ucwords(strtolower(elem.data("nama"))));
            }else{
                $("#judul-daerah").text(ucwords(strtolower(elem.data("nama"))));
            }
            
            $("#filter-sheet").addClass("d-none");
            $("#skor-sheet").addClass("d-none");
            $("#back-sheet").removeClass("d-none");
            $("#map-sheet").toggleClass("col-md-12");
            $("#map-sheet").toggleClass("col-md-4");
            if(elem.data("daerah") == "provinsi"){
                choices3.destroy();
                $perangkat_select.find('option[value="kecamatan"]').remove();
                choices3 = new Choices(perangkat_select, {
                    removeItemButton: true,
                    itemSelectText: "",
                    shouldSort: false,
                });
            }else{
                choices3.setChoices(
                  [
                    { value: 'kecamatan', label: 'Kecamatan', selected: false }
                    ],
                  'value',
                  'label',
                  false
                  );
            }
            map.removeLayer(province);
            map.removeLayer(state);
            L.marker([elem.data("latitude"), elem.data("longitude")]).addTo(selectedpoint);
            selectedpoint.addTo(map);
            setTimeout(function (){
                map.invalidateSize();
                map.setView([elem.data("latitude"),elem.data("longitude")], 8, {
                  "animate": true,
                  "pan": {
                    "duration": 2
                }
            });
                $("#detail-sheet").removeClass("d-none");
            }, 1000);
            
        });
        function load_provinsi()
        {
            $.ajax({
                type: "GET",
                url: "<?= base_url("tipelogi/load_provinsi") ?>",
                dataType: "json",
                processData: false,
                contentType: false,
                success: function(data)
                {
                    data.forEach(function (item, index, arr) {
                        L.marker([item.latitude, item.longitude]).bindTooltip(item.nama_provinsi,{permanent: true, direction: 'top',offset:L.point(-14, -5)}).bindPopup('<table class="table table-bordered border text-black"><tbody><tr><td>Provinsi</td><td>'+item.nama_provinsi+'</td></tr><tr><td>Informasi Kelembagaan</td><td></td></tr><tr><td colspan="2">Informasi Demografi</td></tr><tr><td>Jumlah Penduduk</td><td>'+(item.penduduk != null ? item.penduduk : "-")+' jiwa</td></tr><tr><td>Kepadatan</td><td>'+(item.kepadatan != null ? item.kepadatan : "-")+' jiwa/km²</td></tr><tr><td colspan="2">Informasi Fisik - Demografis</td></tr><tr><td>Luas Wilayah</td><td>'+(item.luas != null ? item.luas : "-")+' km²</td></tr><tr><td colspan="2" class="text-center"><button class="btn btn-primary btn-sm btn-detail" data-id_provinsi="'+item.kode_provinsi+'" data-latitude="'+item.latitude+'" data-longitude="'+item.longitude+'" data-penduduk="'+(item.penduduk != null ? item.penduduk : "-")+'" data-kepadatan="'+(item.kepadatan != null ? item.kepadatan : "-")+'" data-luas="'+(item.luas != null ? item.luas : "-")+'" data-apbd="'+(item.apbd != null ? item.apbd : "-")+'" data-nama="'+item.nama_provinsi+'" data-daerah="provinsi"><i class="bi bi-eye-fill"></i> Lihat Detail</button></td></tr></tbody></table>').addTo(province);
                    });
                    province.addTo(map);
                }
            });
        }
        function load_kabupaten(id)
        {
            $.ajax({
                type: "GET",
                url: "<?= base_url("tipelogi/load_kabupaten") ?>",
                data: {
                    id: id
                },
                dataType: "json",
                contentType: false,
                success: function(data)
                {
                    data.data_kabupaten.forEach(function (item, index, arr) {
                        L.marker([item.latitude, item.longitude]).bindTooltip(item.nama_kabupaten,{permanent: true, direction: 'top',offset:L.point(-14, -5)}).bindPopup('<table class="table table-bordered border text-black"><tbody><tr><td>Kabupaten/Kota</td><td>'+item.nama_kabupaten+'</td></tr><tr><td>Informasi Kelembagaan</td><td></td></tr><tr><td colspan="2">Informasi Demografi</td></tr><tr><td>Jumlah Penduduk</td><td>'+(item.penduduk != null ? item.penduduk : "-")+' jiwa</td></tr><tr><td>Kepadatan</td><td>'+(item.kepadatan != null ? item.kepadatan : "-")+' jiwa/km²</td></tr><tr><td colspan="2">Informasi Fisik - Demografis</td></tr><tr><td>Luas Wilayah</td><td>'+(item.luas != null ? item.luas : "-")+' km²</td></tr><tr><td colspan="2" class="text-center"><button class="btn btn-primary btn-sm btn-detail" data-id_provinsi="'+item.kode_provinsi+'" data-id_kabupaten="'+item.kode_kabupaten+'" data-latitude="'+item.latitude+'" data-longitude="'+item.longitude+'" data-penduduk="'+(item.penduduk != null ? item.penduduk : "-")+'" data-kepadatan="'+(item.kepadatan != null ? item.kepadatan : "-")+'" data-luas="'+(item.luas != null ? item.luas : "-")+'" data-apbd="'+(item.apbd != null ? item.apbd : "-")+'" data-nama="'+item.nama_kabupaten+'" data-daerah="kabupaten"><i class="bi bi-eye-fill"></i> Lihat Detail</button></td></tr></tbody></table>').addTo(state);
                    });
                    map.setView([data.data_provinsi.latitude, data.data_provinsi.longitude], 8, {
                        "animate": true,
                        "pan": {
                            "duration": 2
                        }
                    });
                    state.addTo(map);
                }
            });
        }
        function ucwords (str) {
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                return $1.toUpperCase();
            });
        }

        function strtolower (str) {
            return (str+'').toLowerCase();
        }
        function formatRupiah(angka, prefix)
        {
            var number_string = angka.toString().replace(/[^,\d]/g, '').toString(),
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

    });

</script>