let district_code = $("#district_code").val();

// Fungsi Menampilkan Select Option pada element id school_level_by_kab_kot
function ShowOptionSchoolLevel(school_level) {
    $.ajax({
        type    : 'POST',
        url     : '_Page/DashboardDistrict/OptionSchoolLevel.php',
        data    : { school_level: school_level},
        success : function(data) {
            // Tampilkan Option
            $('#school_level_by_kab_kot').html(data);
        }
    });
}
// Fungsi Animasi Count Up (dinamis menyesuaikan angka)
function animateCountUp(element, start, end, duration) {
    let startTime = null;

    function step(timestamp) {
        if (!startTime) startTime = timestamp;
        let progress = Math.min((timestamp - startTime) / duration, 1);
        let value = Math.floor(progress * (end - start) + start);
        element.innerHTML = value.toLocaleString('id-ID'); // Format ribuan
        if (progress < 1) {
            window.requestAnimationFrame(step);
        }
    }

    window.requestAnimationFrame(step);
}

//Menampilkan Detail School Level
function ShowDetailSchoolLevel(district_code,school_level) {
    $('#FormDetailSchoolLevel').fadeTo(400, 0.3, function () {
        $.ajax({
            type    : 'POST',
            url     : '_Page/DashboardDistrict/FormDetailSchoolLevel.php',
            data    : {district_code: district_code, school_level: school_level},
            success : function(data) {
                // Ganti konten
                $('#FormDetailSchoolLevel').html(data);

                // Efek transisi fadeIn lembut
                $('#FormDetailSchoolLevel').fadeTo(400, 1);
            }
        });
    });
}
// Fungsi Untuk Menampilkan Jumlah Kebutuhan Guru
function ShowDistrictInformation(district_code) {
    
    //Loading Pertama kali
    $('#show_abk').html('Loading...');
    $('#show_asn').html('Loading...');
    $('#show_pppk2024').html('Loading...');
    $('#jumlah_kebutuhan_guru').html('Loading...');
    $('#jumlah_ppg_belum_diangkat').html('Loading...');

    //Tampilkan Data Dengan Ajax
    $.ajax({
        type    : 'POST',
        url     : '_Page/DashboardDistrict/ProsesHitungDistrict.php',
        data    : {district_code: district_code},
        dataType: 'json',
        success : function(response) {

            var status = response.code;
            
            if(status==200){
                //Tangkap Nilai
                let abk = parseInt(response.abk) || 0;
                let asn = parseInt(response.asn) || 0;
                let pppk2024 = parseInt(response.pppk2024) || 0;
                let kurang_guru = parseInt(response.kurang_guru) || 0;
                let ppg_belum_diangkat = parseInt(response.ppg_belum_diangkat) || 0;

                //Tangkap Element
                let show_abk = document.getElementById("show_abk");
                let show_asn = document.getElementById("show_asn");
                let show_pppk2024 = document.getElementById("show_pppk2024");
                let jumlah_kebutuhan_guru = document.getElementById("jumlah_kebutuhan_guru");
                let jumlah_ppg_belum_diangkat = document.getElementById("jumlah_ppg_belum_diangkat");

                // Durasi animasi disesuaikan (maksimal 2 detik)
                let duration = 2000; 

                animateCountUp(show_abk, 0, abk, duration);
                animateCountUp(show_asn, 0, asn, duration);
                animateCountUp(show_pppk2024, 0, pppk2024, duration);
                animateCountUp(jumlah_kebutuhan_guru, 0, kurang_guru, duration);
                animateCountUp(jumlah_ppg_belum_diangkat, 0, ppg_belum_diangkat, duration);
            }

            if(status!==200){
                $('#show_abk').html(response.message);
                $('#show_asn').html(response.message);
                $('#show_pppk2024').html(response.message);
                $('#jumlah_kebutuhan_guru').html(response.message);
                $('#jumlah_ppg_belum_diangkat').html(response.message);
            }

        }
    });
}

// Fungsi Menampilkan Chart Pie
function ShowChartPie() {
    var district_code = $("#district_code").val() || "";

    // Validasi district_code tidak kosong
    if (district_code === "") {
        $('#ShowChartPie').html('<div class="text-muted text-center">District code kosong</div>');
        $('#ShowChartPieDebug').text('');
        return;
    }

    // Loading
    $('#ShowChartPie').html('<div class="text-center">Loading chart...</div>');
    $('#ShowChartPieDebug').text('');

    $.ajax({
        type: 'POST',
        url: '_Page/DashboardDistrict/ShowChartPie.php',
        data: { district_code: district_code },
        dataType: 'json',
        success: function(response) {
            // Tampilkan debug ringkas
            $('#ShowChartPieDebug').text(JSON.stringify(response.debug, null, 2));

            if (!response || typeof response.code === 'undefined') {
                $('#ShowChartPie').html('<div class="text-danger">Response tidak valid (format JSON salah)</div>');
                console.error('Invalid response:', response);
                return;
            }

            if (response.code !== 200) {
                // Error / session expired / invalid input
                $('#ShowChartPie').html('<div class="text-danger text-center">' + response.message + '</div>');
                console.warn('ShowChartPie warning:', response);
                return;
            }

            var metadata = response.metadata || [];

            if (metadata.length === 0) {
                $('#ShowChartPie').html('<div class="text-muted text-center">Tidak ada data untuk kabupaten ini.</div>');
                return;
            }

            // Buat series & labels dari KurangGuru
            var labels = metadata.map(function(m){ return m.school_level || 'Unknown'; });
            var series = metadata.map(function(m){ return Number(m.KurangGuru) || 0; });

            var totalKurang = series.reduce(function(a,b){ return a + b; }, 0);
            if (totalKurang === 0) {
                $('#ShowChartPie').html('<div class="text-muted text-center">Data ditemukan, tetapi semua nilai KurangGuru = 0</div>');
                return;
            }

            // Buat container chart
            $('#ShowChartPie').html('<div id="chartPie" style="height:300px;"></div>');

            // Clean up chart instance sebelumnya jika ada
            if (window._chartPieInstance) {
                try { window._chartPieInstance.destroy(); } catch(e) {}
            }

            var options = {
                chart: {
                    type: 'pie',
                    height: 400,
                    events: {
                        dataPointSelection: function(event, chartContext, config) {
                            // Dapatkan school_level dari slice yang diklik
                            var clickedSchoolLevel = labels[config.dataPointIndex];
                            
                            // Tampilkan modal
                            $('#ModalDetailSchoolLevel').modal('show');
                            
                            // Panggil fungsi ShowDetailSchoolLevel setelah modal ditampilkan
                            $('#ModalDetailSchoolLevel').on('shown.bs.modal', function () {
                                ShowDetailSchoolLevel(district_code, clickedSchoolLevel);
                            });
                        }
                    }
                },
                series: series,
                labels: labels,
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    y: {
                        formatter: function(val) { return val + " guru"; }
                    }
                },
                noData: {
                    text: 'No data to display'
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '50%'
                        },
                        customScale: 1,
                        dataLabels: {
                            offset: -5,
                            minAngleToShowLabel: 10
                        }
                    }
                },
                states: {
                    hover: {
                        filter: {
                            type: 'darken',
                            value: 0.1
                        }
                    }
                }
            };

            window._chartPieInstance = new ApexCharts(document.querySelector("#chartPie"), options);
            window._chartPieInstance.render();
        },
        error: function(xhr, status, err) {
            $('#ShowChartPieDebug').text('AJAX error: ' + status + '\n' + xhr.responseText);
            $('#ShowChartPie').html('<div class="text-danger text-center">Terjadi kesalahan saat memuat data. Periksa console / debug.</div>');
            console.error('AJAX error', status, err, xhr.responseText);
        }
    });
}

//Fungsi Menampilkan Tabel 
function ShowTabelGuruByJabatan() {

    //Validasi district_code tidak kosong
    if(district_code!==""){
        
        //Tempelkan district_code ke filter
       $('#district_code_filter').val(district_code);
        
        var ProsesFilter = $('#ProsesFilter').serialize();

        // Efek transisi: fadeOut lembut
        $('#TabelGuruByJabatan').fadeTo(400, 0.3, function () {
            $.ajax({
                type    : 'POST',
                url     : '_Page/DashboardDistrict/TabelGuruByJabatan.php',
                data    : ProsesFilter,
                success : function(data) {
                    // Ganti konten
                    $('#TabelGuruByJabatan').html(data);

                    // Efek transisi fadeIn lembut
                    $('#TabelGuruByJabatan').fadeTo(400, 1);
                }
            });
        });
    }
}

//Fungsi Menampilkan Tabel Kab/Kota
function ShowTableKabKota() {

    //Tangkap Data Filter
    var ProsesFilterKabKot = $('#ProsesFilterKabKot').serialize();

    // Efek transisi: fadeOut lembut
    $('#TabelKabKota').fadeTo(400, 0.3, function () {
        $.ajax({
            type    : 'POST',
            url     : '_Page/DashboardDistrict/TabelKabKota.php',
            data    : ProsesFilterKabKot,
            success : function(data) {
                // Ganti konten
                $('#TabelKabKota').html(data);

                // Efek transisi fadeIn lembut
                $('#TabelKabKota').fadeTo(400, 1);
            }
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
            url     : '_Page/DashboardDistrict/TabelDetailJabatan.php',
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

    //Menampilkan Informasi District
    ShowDistrictInformation(district_code);

    // Muat chart saat dokumen siap
    ShowChartPie();

    // Bila ada aksi mengubah district, panggil ulang ShowChartPie()
    $('#district_code').on('change', function(){
        ShowChartPie();
    });

    //Tampilkan Form Select Option
    ShowOptionSchoolLevel();

    //Menampilkan Tabel Pertama Kali
    ShowTabelGuruByJabatan();

    //Pagging
    $(document).on('click', '#next_button', function() {
        var page_now = parseInt($('#page').val(), 10); // Pastikan nilai diambil sebagai angka
        var next_page = page_now + 1;
        $('#page').val(next_page);
        ShowTabelGuruByJabatan(0);
    });
    $(document).on('click', '#prev_button', function() {
        var page_now = parseInt($('#page').val(), 10); // Pastikan nilai diambil sebagai angka
        var next_page = page_now - 1;
        $('#page').val(next_page);
        ShowTabelGuruByJabatan(0);
    });

    //Ketika school_level_by_kab_kot diubah
    $('#school_level_by_kab_kot').change(function(){
        ShowTabelGuruByJabatan();
    });


    //INISIALISASI TABEL KAB/KOTA
     if ($('#TabelKabKota').length > 0) {
        ShowTableKabKota();
    }
    
    //Ketika Modal Pencarian Kab/Kota
    $('#ModalFilterKabKot').on('shown.bs.modal', function() {
        $('#keyword_kabkot').focus().select();
    });

    //Ketika Proses Pencarian Kab/Kota
    $('#ProsesFilterKabKot').submit(function(){

        //Default Halaman
        $('#page_kabkot').val("1");

        //Tampilkan Ulang Data
        ShowTableKabKota();

        //Tutup Modal
        $('#ModalFilterKabKot').modal('hide');
    });

    //Pagging Kab/Kot
    $(document).on('click', '#next_button_kabkot', function() {
        var page_now = parseInt($('#page_kabkot').val(), 10); // Pastikan nilai diambil sebagai angka
        var next_page = page_now + 1;
        $('#page_kabkot').val(next_page);
        ShowTableKabKota(0);
    });
    $(document).on('click', '#prev_button_kabkot', function() {
        var page_now = parseInt($('#page_kabkot').val(), 10); // Pastikan nilai diambil sebagai angka
        var next_page = page_now - 1;
        $('#page_kabkot').val(next_page);
        ShowTableKabKota(0);
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

    //Modal Detail Jabatan
    $('#ModalDetailJabatan').on('show.bs.modal', function (e) {
        var id_region = $(e.relatedTarget).data('id_region');
        var id_position = $(e.relatedTarget).data('id_position');

        //Reset halaman menjadi 1
        $('#page_detail_jabatan').val('1');

        //Tempelkan id_region dan id_position ke form filter
        $('#put_id_region').val(id_region);
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

    //Event ketika 'BtnLihatUraianByJenjang' di click
    $(document).on('click', '#BtnLihatUraianByJenjang', function() {
        var school_level = $('#get_school_level_from_detail').html();
        
        //Tempelkan school_level ke school_level_by_kab_kot
        $('#school_level_by_kab_kot').val(school_level);

        //Reset Posisi Halaman
        $('#page').val("1");

        //Tampilkan Ulang Data
        ShowTabelGuruByJabatan();

        //Tutup Modal
        $('#ModalDetailSchoolLevel').modal('hide');

        //Scroll ke konten kebutuhan guru
        $('html, body').animate({
            scrollTop: $("#konten_kebutuhan_guru_menurut_jabatan").offset().top - 80 // -80 biar agak turun dikit dari header
        }, 600); // durasi animasi 600ms
    });
});