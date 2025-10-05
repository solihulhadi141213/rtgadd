let district_code = $("#district_code").val();

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
            $('#ShowChartPie').html('<div id="chartPie" style="height:360px;"></div>');

            // Clean up chart instance sebelumnya jika ada
            if (window._chartPieInstance) {
                try { window._chartPieInstance.destroy(); } catch(e) {}
            }

            var options = {
                chart: {
                    type: 'pie',
                    height: 360
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
        //Buka Data Dengan AJAX
        $('#TabelGuruByJabatan').html('<tr><td colspan="6" class="text-center">Loading...</td></tr>');
        $.ajax({
            type    : 'POST',
            url     : '_Page/DashboardDistrict/TabelGuruByJabatan.php',
            data    : ProsesFilter,
            success : function(data) {
                $('#TabelGuruByJabatan').html(data);
            }
        });
    }
}

$(document).ready(function () {

    // Muat chart saat dokumen siap
    ShowChartPie();

    // Bila ada aksi mengubah district, panggil ulang ShowChartPie()
    $('#district_code').on('change', function(){
        ShowChartPie();
    });

    //Menampilkan Tabel
    ShowTabelGuruByJabatan();
});