
//Fungsi Menampilkan Nominal Kebutuhan Guru
function ShowNominalKebutuhanGuruProvinsi(kode_provinsi) {
    $.ajax({
        type    : 'POST',
        url     : '_Page/DashboardProvince/ShowNominalKebutuhanGuruProvinsi.php',
        data    : {province_code: kode_provinsi},
        dataType: 'json',
        success : function(response) {
            if (response.code === 200) {
                // Hapus alert lama, ganti ke tempat angka
                $('#show_nominal_kebutuhan_guru').html(
                    '<div>' + 
                        '<b id="counter">0</b>' +
                    '</div>'
                );

                // Animasi hitungan
                let target = response.kebutuhan_guru;
                let counterElement = $('#counter');
                let current = 0;
                let increment = Math.ceil(target / 100); // jumlah kenaikan per step
                let interval = setInterval(function() {
                    current += increment;
                    if (current >= target) {
                        current = target;
                        clearInterval(interval);
                    }
                    counterElement.text(current.toLocaleString('id-ID'));
                }, 20); // kecepatan transisi (20ms per step)
                
            } else {
                $('#show_nominal_kebutuhan_guru').html(
                    '<div class="alert alert-danger p-2 mb-2">' + 
                        response.message + 
                    '</div>'
                );
            }
        },
        error: function(xhr, status, error) {
            $('#show_nominal_kebutuhan_guru').html(
                '<div class="alert alert-danger p-2 mb-2">' + 
                    'Terjadi kesalahan: ' + error + 
                '</div>'
            );
        }
    });
}

//Fungsi Menampilkan Nominal Lulusan PPG yang Belum Diangkat
function ShowNominalLulusanPpg(kode_provinsi) {
    $.ajax({
        type    : 'POST',
        url     : '_Page/DashboardProvince/ShowNominalLulusanPpg.php',
        data    : {province_code: kode_provinsi},
        dataType: 'json',
        success : function(response) {
            if (response.code === 200) {
                // Hapus alert lama, ganti ke tempat angka
                $('#show_lulusan_ppg_pending').html(
                    '<div>' + 
                        '<b id="counter_2">0</b>' +
                    '</div>'
                );

                // Animasi hitungan
                let target = response.lulusan_ppg;
                let counterElement = $('#counter_2');
                let current = 0;
                let increment = Math.ceil(target / 100); // jumlah kenaikan per step
                let interval = setInterval(function() {
                    current += increment;
                    if (current >= target) {
                        current = target;
                        clearInterval(interval);
                    }
                    counterElement.text(current.toLocaleString('id-ID'));
                }, 20); // kecepatan transisi (20ms per step)
                
            } else {
                $('#show_lulusan_ppg_pending').html(
                    '<div class="alert alert-danger p-2 mb-2">' + 
                        response.message + 
                    '</div>'
                );
            }
        },
        error: function(xhr, status, error) {
            $('#show_lulusan_ppg_pending').html(
                '<div class="alert alert-danger p-2 mb-2">' + 
                    'Terjadi kesalahan: ' + error + 
                '</div>'
            );
        }
    });
}

//Fungsi Menampilkan Data Kebutuhan Guru Menurut Kab/Kota
function ShowTableKebutuhanGuruByKabKot() {
    var DataFilter = $('#ProsesFilterKebutuhanGuruByKabKot').serialize();

    // Efek transisi: fadeOut lembut
    $('#TabelKebutuhanGuruByKabKot').fadeTo(400, 0.3, function () {
        $.ajax({
            type    : 'POST',
            url     : '_Page/DashboardProvince/TabelKebutuhanGuruByKabKot.php',
            data    : DataFilter,
            success : function(data) {
                // Ganti konten
                $('#TabelKebutuhanGuruByKabKot').html(data);

                // Efek transisi fadeIn lembut
                $('#TabelKebutuhanGuruByKabKot').fadeTo(400, 1);

                // ❌ Jangan pakai scrollTop lagi, biar tetap di posisi hasil animasi
            }
        });
    });
}

//Fungsi Menampilkan Data Kebutuhan Guru Menurut Jabatan
function ShowTableKebutuhanGuruByJabatan() {
    var DataFilterJabatan = $('#ProsesFilterKebutuhanGuruByJabatan').serialize();

    // Efek transisi: fadeOut lembut
    $('#TabelKebutuhanGuruByJabatan').fadeTo(400, 0.3, function () {
        $.ajax({
            type    : 'POST',
            url     : '_Page/DashboardProvince/TabelKebutuhanGuruByJabatan.php',
            data    : DataFilterJabatan,
            success : function(data) {
                // Ganti konten
                $('#TabelKebutuhanGuruByJabatan').html(data);

                // Efek transisi fadeIn lembut
                $('#TabelKebutuhanGuruByJabatan').fadeTo(400, 1);
            }
        });
    });
}

//Fungsi Menampilkan Detail Dari Chart Ke Dalam Modal
function ShowModalDetailContent(province_code,school_level) {
    $('#ModalDetailContent').html('Loading...');
    $.ajax({
        type    : 'POST',
        url     : '_Page/DashboardProvince/DetailContent.php',
        data    : {province_code: province_code, school_level:school_level},
        success : function(response) {
            $('#ModalDetailContent').html(response);
        }
    });
}

// Debug function untuk memeriksa data Pada Peta Interaktif
function debugGeoJSON(data) {
    console.log("Total features:", data.features.length);
    
    data.features.forEach((feature, index) => {
        console.log(`Feature ${index}:`, {
            properties: feature.properties,
            geometry_type: feature.geometry.type,
            coordinates_length: feature.geometry.coordinates ? feature.geometry.coordinates.length : 0
        });
    });
}

//Fungsi Menampilkan Tabel Jabatan
function ShowTabelDetailJabatan() {

    //Tangkap Data Filter
    var FilterTabelJabatan = $('#FilterTabelJabatan').serialize();

    // Efek transisi: fadeOut lembut
    $('#TabelDetailJabatan').fadeTo(400, 0.3, function () {
        $.ajax({
            type    : 'POST',
            url     : '_Page/DashboardProvince/TabelDetailJabatan.php',
            data    : FilterTabelJabatan,
            success : function(data) {
                // Ganti konten
                $('#TabelDetailJabatan').html(data);

                // Efek transisi fadeIn lembut
                $('#TabelDetailJabatan').fadeTo(400, 1);
            }
        });
    });
}

$(document).ready(function () {
    var kode_provinsi = $("#kode_provinsi").val();
    var province_code = $("#province_code").val();

    //Menampilkan Jumlah Angka Kebutuhan Guru Level Provinsi
    ShowNominalKebutuhanGuruProvinsi(kode_provinsi);

    //Menampilkan Lulusan PPG Belum Diangkat
    ShowNominalLulusanPpg(kode_provinsi);

    //Load Data Kebutuhan Guru menurut kabupaten/Kota (Pertama Kali)
    ShowTableKebutuhanGuruByKabKot();

    //Event Ketika 'school_level_by_kab_kot' diubah
    $('#school_level_by_kab_kot').change(function(){

        //Reset Posisi Halaman
        $('#page_kebutuhan_guru_by_kabkot').val("1");

        //Tampilkan Ulang Data
        ShowTableKebutuhanGuruByKabKot();

        //Tutup Modal
        $('#ModalFilterKebutuhanGuruByKabKot').modal('hide');
    });

    //PAGGING Kebutuhan Guru Menurut Kab/Kot
    $(document).on('click', '#next_button', function() {
        var page_now = parseInt($('#page_kebutuhan_guru_by_kabkot').val(), 10); // Pastikan nilai diambil sebagai angka
        var next_page = page_now + 1;
        $('#page_kebutuhan_guru_by_kabkot').val(next_page);
        ShowTableKebutuhanGuruByKabKot(0);
    });
    $(document).on('click', '#prev_button', function() {
        var page_now = parseInt($('#page_kebutuhan_guru_by_kabkot').val(), 10); // Pastikan nilai diambil sebagai angka
        var next_page = page_now - 1;
        $('#page_kebutuhan_guru_by_kabkot').val(next_page);
        ShowTableKebutuhanGuruByKabKot(0);
    });

    //Load Data Kebutuhan Guru menurut Jabatan (Pertama Kali)
    ShowTableKebutuhanGuruByJabatan();

    //Event ketika school_level_2 change
    $('#school_level_2').change(function(){
       ShowTableKebutuhanGuruByJabatan();
    });

    //PAGGING Kebutuhan Guru Menurut Position (Jabatan)
    $(document).on('click', '#next_button_2', function() {
        var page_now = parseInt($('#page_kebutuhan_guru_by_jabatan').val(), 10); // Pastikan nilai diambil sebagai angka
        var next_page = page_now + 1;
        $('#page_kebutuhan_guru_by_jabatan').val(next_page);
        ShowTableKebutuhanGuruByJabatan(0);
    });
    $(document).on('click', '#prev_button_2', function() {
        var page_now = parseInt($('#page_kebutuhan_guru_by_jabatan').val(), 10); // Pastikan nilai diambil sebagai angka
        var next_page = page_now - 1;
        $('#page_kebutuhan_guru_by_jabatan').val(next_page);
        ShowTableKebutuhanGuruByJabatan(0);
    });

    // ===================== PIE CHART APEXCHART =====================
    // Ambil data dari PHP
    // Pastikan elemen ada
    if(!$("#ChartKebutuhanGuru").length){
        console.error("Elemen #ChartKebutuhanGuru tidak ditemukan!");
        return;
    }

    $.ajax({
        type: "POST",
        url: "_Page/DashboardProvince/kebutuhan_guru_by_jenjang.php",
        data: { province_code: province_code },
        dataType: "json",
        success: function(response) {
            if(response.code && response.code != 200){
                alert(response.message);
                return;
            }

            let labels = [];
            let series = [];
            let rawData = [];

            $.each(response.data, function(index, item){
                labels.push(item.school_level);
                series.push(parseInt(item.kebutuhan_guru));
                rawData.push(item);
            });

            // Hapus chart lama kalau ada
            $("#ChartKebutuhanGuru").empty();

            let options = {
                chart: {
                    type: 'pie',
                    height: 400,
                    events: {
                        dataPointSelection: function(event, chartContext, config) {
                            let index = config.dataPointIndex;
                            let selectedData = rawData[index];

                            //Tampilkan Modal
                            $("#ModalDetailByJenjang").modal("show");

                            //Tamilkan Detaiil Data
                            ShowModalDetailContent(selectedData.province_code,selectedData.school_level) 
                        }
                    }
                },
                labels: labels,
                series: series,
                legend: {
                    position: 'bottom'
                }
            };

            let chart = new ApexCharts(document.querySelector("#ChartKebutuhanGuru"), options);
            chart.render();
        }
    });


    //Modal Detail Kab/Kota
    $('#ModalDetailKabKot').on('show.bs.modal', function (e) {
        var district_code = $(e.relatedTarget).data('id');
        $('#FormDetailKabKot').html("Loading...");
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/DashboardProvince/FormDetailKabKot.php',
            data        : {district_code: district_code},
            success     : function(data){
                $('#FormDetailKabKot').html(data);
            }
        });
    });

    //Event ketika 'BtnLihatUraianByJenjang' di click
    $(document).on('click', '#BtnLihatUraianByJenjang', function() {
        var school_level = $('#get_school_level_from_detail').html();
        
        //Tempelkan school_level ke school_level_by_kab_kot
        $('#school_level_by_kab_kot').val(school_level);

        //Reset Posisi Halaman
        $('#page_kebutuhan_guru_by_kabkot').val("1");

        //Tampilkan Ulang Data
        ShowTableKebutuhanGuruByKabKot();

        //Tutup Modal
        $('#ModalDetailByJenjang').modal('hide');

        //Scroll ke konten kebutuhan guru
        $('html, body').animate({
            scrollTop: $("#KontenKebutuhanGuruMenurutKabupaten").offset().top - 80 // -80 biar agak turun dikit dari header
        }, 600); // durasi animasi 600ms
    });

    // ===================== LEAFLET MAP =====================
    let timestamp = new Date().getTime();

    // Inisialisasi peta dengan view default Indonesia
    var map = L.map('ShowMapProvinsiAndAkbupaten', {
        zoomControl: false,
        scrollWheelZoom: false
    }).setView([-2, 118], 5);

    // Tambahkan zoom control di pojok kiri atas
    L.control.zoom({
        position: 'topleft'
    }).addTo(map);

    // Basemap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    // Fungsi untuk memberi warna berdasarkan kurang_guru
    function getColor(value) {
        return value > 5000 ? '#000000' : // hitam
            value > 2000 ? '#00008B' : // biru sangat tua
            value > 1000 ? '#0000CD' : // biru tua
            value > 500  ? '#4169E1' : // royal blue
            value > 100  ? '#6495ED' : // cornflower blue
            value > 50   ? '#87CEEB' : // sky blue
            value > 10   ? '#ADD8E6' : // light blue
                            '#F8F9FA';  // putih (default)
    }

    // Tambahkan legenda
    var legend = L.control({position: 'bottomright'});

    legend.onAdd = function (map) {
        var div = L.DomUtil.create('div', 'legend');
        div.innerHTML = `
            <div class="card shadow-sm">
                <div class="card-header py-2">
                    <strong class="card-title mb-0" style="font-size: 12px;">Legenda Kekurangan Guru</strong>
                </div>
                <div class="card-body py-2">
                    <div class="d-flex align-items-center mb-1">
                        <div style="width: 20px; height: 15px; background-color: #F8F9FA; border: 1px solid #ccc; margin-right: 5px;"></div>
                        <small>0-10</small>
                    </div>
                    <div class="d-flex align-items-center mb-1">
                        <div style="width: 20px; height: 15px; background-color: #ADD8E6; border: 1px solid #ccc; margin-right: 5px;"></div>
                        <small>11-50</small>
                    </div>
                    <div class="d-flex align-items-center mb-1">
                        <div style="width: 20px; height: 15px; background-color: #87CEEB; border: 1px solid #ccc; margin-right: 5px;"></div>
                        <small>51-100</small>
                    </div>
                    <div class="d-flex align-items-center mb-1">
                        <div style="width: 20px; height: 15px; background-color: #6495ED; border: 1px solid #ccc; margin-right: 5px;"></div>
                        <small>101-500</small>
                    </div>
                    <div class="d-flex align-items-center mb-1">
                        <div style="width: 20px; height: 15px; background-color: #4169E1; border: 1px solid #ccc; margin-right: 5px;"></div>
                        <small>501-1000</small>
                    </div>
                    <div class="d-flex align-items-center mb-1">
                        <div style="width: 20px; height: 15px; background-color: #0000CD; border: 1px solid #ccc; margin-right: 5px;"></div>
                        <small>1001-2000</small>
                    </div>
                    <div class="d-flex align-items-center mb-1">
                        <div style="width: 20px; height: 15px; background-color: #00008B; border: 1px solid #ccc; margin-right: 5px;"></div>
                        <small>2001-5000</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <div style="width: 20px; height: 15px; background-color: #000000; border: 1px solid #ccc; margin-right: 5px;"></div>
                        <small>>5000</small>
                    </div>
                </div>
            </div>
        `;
        return div;
    };

    legend.addTo(map);

    // Fungsi untuk mendapatkan bounds dari GeoJSON
    function getGeoJSONBounds(geoJSON) {
        var bounds = new L.LatLngBounds();
        
        geoJSON.eachLayer(function(layer) {
            if (layer.getBounds) {
                bounds.extend(layer.getBounds());
            }
        });
        
        return bounds.isValid() ? bounds : null;
    }

    // Ambil data GeoJSON dari API
    $.getJSON("_Page/DashboardProvince/GetGeoProvince.php?province_code=" + kode_provinsi)
        .done(function(data){
            debugGeoJSON(data);
            console.log("Data GeoJSON diterima:", data);
            
            if (data.features && data.features.length > 0) {
                var geoLayer = L.geoJSON(data, {
                    style: function(feature){
                        let kurangGuru = feature.properties.kurang_guru || 0;
                        let isSample = feature.properties.is_sample || false;
                        
                        return {
                            color: isSample ? "red" : "black", // Garis merah untuk sample district
                            weight: isSample ? 3 : 1, // Garis lebih tebal untuk sample
                            fillColor: getColor(kurangGuru),
                            fillOpacity: 0.7
                        };
                    },
                    onEachFeature: function(feature, layer){
                        let id_region = feature.properties.id_region || "-";
                        let prov = feature.properties.province_name || "-";
                        let kabkota_code = feature.properties.district_code || "-";
                        let kabkota = feature.properties.district_name || "-";
                        let kurangGuru = feature.properties.kurang_guru || 0;
                        let isSample = feature.properties.is_sample || false;
                        
                        // Tampilan popup yang lebih menarik dengan Bootstrap
                        let popupContent = `
                            <div class="popup-content" style="min-width: 200px;">
                                <div class="border-bottom pb-2 mb-2">
                                    <h6 class="mb-1 fw-bold">${kabkota}</h6>
                                    <small class="text-muted">${prov}</small><br>
                                    <small class="text-muted">Kekurangan Guru : ${kurangGuru}</small>
                                </div>
                                <div class="text-end">
                                    <button type="button" class="btn btn-sm btn-secondary btn-block" data-bs-toggle="modal" data-bs-target="#ModalDetailKabKot" data-id="${kabkota_code}">
                                        Lihat Detail
                                    </button>
                                </div>
                            </div>
                        `;
                        layer.bindPopup(popupContent);
                    }
                }).addTo(map);

                // Coba dapatkan bounds dan zoom ke peta
                try {
                    var bounds = geoLayer.getBounds();
                    if (bounds.isValid()) {
                        map.fitBounds(bounds, { padding: [20, 20] });
                        console.log("Zoom ke bounds berhasil");
                    } else {
                        console.warn("Bounds tidak valid, menggunakan view default");
                        // Tetap di view default Indonesia
                        map.setView([-2, 118], 5);
                    }
                } catch (error) {
                    console.error("Error saat zoom ke bounds:", error);
                    map.setView([-2, 118], 5);
                }
                
            } else {
                $('#ShowMapProvinsiAndAkbupaten').html('<small class="text-danger">Tidak ada data geografis untuk provinsi ini.</small>');
            }
        })
        .fail(function(jqXHR, textStatus, errorThrown){
            console.error("Error loading GeoJSON:", textStatus, errorThrown);
            $('#ShowMapProvinsiAndAkbupaten').html('<small class="text-danger">Gagal memuat peta. Silakan refresh halaman.</small>');
        });

    // Fungsi untuk menampilkan modal detail
    function showDetailModal(kabkota_code) {
        // Trigger modal dan set data
        $('#ModalDetailKabKot').modal('show');
        // Tambahkan logika untuk load data detail berdasarkan kabkota_code
        console.log('Loading detail untuk:', kabkota_code);
    }

    // CSS untuk legenda
    var style = document.createElement('style');
    style.innerHTML = `
        .legend {
            background: transparent !important;
            border: none !important;
        }
        .legend .card {
            background: white;
            border-radius: 8px;
        }
        .legend .card-header {
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        .legend .card-body {
            padding: 8px 12px;
        }
    `;
    document.head.appendChild(style);

    //Modal Detail Kebutuhan Guru By Jabatan
    $('#ModalDetailKebutuhanGuruByJabatan').on('show.bs.modal', function (e) {
        var province_code = $(e.relatedTarget).data('province_code');
        var id_position = $(e.relatedTarget).data('id_position');

        //Reset halaman menjadi 1
        $('#page_detail_jabatan').val('1');

        //Tempelkan province_code dan id_position ke form filter
        $('#put_province_code').val(province_code);
        $('#put_id_positiom').val(id_position);

        //Panggil Function
        ShowTabelDetailJabatan();
    });

    //Pagging
    $(document).on('click', '#next_button_school', function() {
        var page_now = parseInt($('#page_detail_jabatan').val(), 10); // Pastikan nilai diambil sebagai angka
        var next_page = page_now + 1;
        $('#page_detail_jabatan').val(next_page);
        ShowTabelDetailJabatan(0);
    });
    $(document).on('click', '#prev_button_school', function() {
        var page_now = parseInt($('#page_detail_jabatan').val(), 10); // Pastikan nilai diambil sebagai angka
        var next_page = page_now - 1;
        $('#page_detail_jabatan').val(next_page);
        ShowTabelDetailJabatan(0);
    });
});