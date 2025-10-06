
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

    // Simpan posisi scroll saat ini
    var currentScroll = $(window).scrollTop();

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

                // Kembalikan posisi scroll agar layar tidak bergerak
                $(window).scrollTop(currentScroll);
            }
        });
    });
}

//Fungsi Menampilkan Data Kebutuhan Guru Menurut Jabatan
function ShowTableKebutuhanGuruByJabatan() {
    var DataFilterJabatan = $('#ProsesFilterKebutuhanGuruByJabatan').serialize();

    // Simpan posisi scroll saat ini
    var currentScroll = $(window).scrollTop();

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

                // Kembalikan posisi scroll agar layar tidak bergerak
                $(window).scrollTop(currentScroll);
            }
        });
    });
}



$(document).ready(function () {
    var kode_provinsi = $("#kode_provinsi").val();

    //Menampilkan Jumlah Angka Kebutuhan Guru Level Provinsi
    ShowNominalKebutuhanGuruProvinsi(kode_provinsi);

    //Menampilkan Lulusan PPG Belum Diangkat
    ShowNominalLulusanPpg(kode_provinsi);

    //Load Data Kebutuhan Guru menurut kabupaten/Kota (Pertama Kali)
    ShowTableKebutuhanGuruByKabKot();

    //Event Ketika Filter Kebutuhan Guru Menurut Kab/Kot Disubmit
    $('#ProsesFilterKebutuhanGuruByKabKot').submit(function(){

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

    let timestamp = new Date().getTime();
    // ===================== PIE CHART APEXCHART =====================
    $.getJSON("_Page/DashboardProvince/kebutuhan_guru_by_jenjang.json?v=" + timestamp, function(data) {
        let provData = data.find(item => item.province_code === kode_provinsi);

        if(provData && provData.kebutuhan_guru_by_jenjang){
            let seriesData = provData.kebutuhan_guru_by_jenjang.map(item => item.kebutuhan);
            let labelsData = provData.kebutuhan_guru_by_jenjang.map(item => item.jenjang);

            var options = {
                chart: {
                    type: 'pie',
                    height: 400
                },
                series: seriesData,
                labels: labelsData,
                legend: {
                    position: 'bottom'
                }
            };

            var chart = new ApexCharts(document.querySelector("#kebutuhan_guru_by_jenjang"), options);
            chart.render();
        } else {
            $("#kebutuhan_guru_by_jenjang").html("<p class='text-center text-muted'>Data tidak tersedia</p>");
        }
    });

    // ===================== LEAFLET MAP =====================
    var map = L.map('ShowMapProvinsiAndAkbupaten').setView([-2,118], 5);
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
                            '#ADD8E6';  // biru muda (default)
    }

    // Ambil data GeoJSON dari API
    $.getJSON("_Page/DashboardProvince/GetGeoProvince.php?province_code=" + kode_provinsi, function(data){
        var geoLayer = L.geoJSON(data, {
            style: function(feature){
                let kurangGuru = feature.properties.kurang_guru || 0;
                return {
                    color: "black",   // garis pinggir
                    weight: 1,
                    fillColor: getColor(kurangGuru),
                    fillOpacity: 0.7
                };
            },
            onEachFeature: function(feature, layer){
                let id_region = feature.properties.id_region || "-";
                let prov = feature.properties.province_name || "-";
                let kabkota = feature.properties.district_name || "-";
                let kurangGuru = feature.properties.kurang_guru || 0;
                let popupContent = `
                    <b>${kabkota}</b><br>
                    Provinsi: ${prov}<br>
                    Kurang Guru: <span class="text-danger">${kurangGuru}</span><br>
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#ModalDetailKabKot" data-id="${id_region}">Lihat Selengkapnya</a>
                `;
                layer.bindPopup(popupContent);
            }
        }).addTo(map);

        // Zoom ke batas geojson
        map.fitBounds(geoLayer.getBounds());
    });




    //Modal Detail Kab/Kota
    $('#ModalDetailKabKot').on('show.bs.modal', function (e) {
        var id_region = $(e.relatedTarget).data('id');
        $('#FormDetailKabKot').html("Loading...");
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/DashboardProvince/FormDetailKabKot.php',
            data        : {id_region: id_region},
            success     : function(data){
                $('#FormDetailKabKot').html(data);
            }
        });
    });
});