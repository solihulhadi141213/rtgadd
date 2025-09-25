// Fungsi Untuk Menampilkan Grafik
function loadPieOfPosition() {
     $.getJSON("_Page/Dashboard/count_of_position.json", function(response) {
        var labels = response.map(function(item) {
            return item.jabatan;
        });

        var series = response.map(function(item) {
            return item.jumlah;
        });

        var options = {
            chart: {
                type: 'pie',
                height: 500
            },
            series: series,
            labels: labels,
            legend: {
                position: 'right',   // default di samping
                horizontalAlign: 'center',
                fontSize: '14px',
                itemMargin: {
                    horizontal: 8,
                    vertical: 4
                }
            },
            responsive: [{
                breakpoint: 768, // tablet & mobile
                options: {
                    chart: {
                        height: 380
                    },
                    legend: {
                        position: 'bottom', // legend pindah ke bawah
                        horizontalAlign: 'center'
                    }
                }
            }]
        };

        $("#pie_of_count_position").empty();

        var chart = new ApexCharts(document.querySelector("#pie_of_count_position"), options);
        chart.render();
    }).fail(function() {
        $("#pie_of_count_position").html("<p class='text-danger'>Gagal memuat data jabatan</p>");
    });
}

// Fungsi untuk menampilkan dashboard
function ShowDashboard() {
    $.ajax({
        type: 'POST',
        url: '_Page/Dashboard/CountDashboard.php',
        dataType: 'json',
        success: function(data) {
            $('#count_client').hide().html(data.client).fadeIn('slow');
            $('#count_school').hide().html(data.school).fadeIn('slow');
            $('#count_teacher').hide().html(data.teacher).fadeIn('slow');
            $('#count_position').hide().html(data.position).fadeIn('slow');
        },
        error: function(xhr, status, error) {
            console.error("Gagal mengambil data dashboard:", error);
        }
    });
}

// Fungsi untuk Menampilkan top_guru_provinsi
function top_guru_provinsi() {
    $.ajax({
        type: 'POST',
        url: '_Page/Dashboard/top_guru_provinsi.php',
        success: function(data) {
            $('#top_guru_provinsi').hide().html(data).fadeIn('slow');
        },
        error: function(xhr, status, error) {
            console.error("Gagal mengambil data dashboard:", error);
        }
    });
}

// Fungsi untuk Menampilkan top_guru_kabupaten
function top_guru_kabupaten() {
    $.ajax({
        type: 'POST',
        url: '_Page/Dashboard/top_guru_kabupaten.php',
        success: function(data) {
            $('#top_guru_kabupaten').hide().html(data).fadeIn('slow');
        },
        error: function(xhr, status, error) {
            console.error("Gagal mengambil data dashboard:", error);
        }
    });
}

$(document).ready(function () {
    //Menampilkan Grafik
    loadPieOfPosition();

    //Menampilkan Peta Vector dengan Highcharts
    $.getJSON('_Page/Dashboard/count_of_position_province.json', function(data) {
        const mapData = data.data.map(province => {
            return {
                'hc-key': province.kode_provinsi,
                value: province.kebutuhan_guru,
                name: province.province,
                kebutuhan: province.kebutuhan_guru,
                tersedia: province.guru_tersedia,
                kekurangan: province.kekurangan
            };
        });
        
        Highcharts.mapChart('indonesia-map', {
            chart: {
                map: 'countries/id/id-all',
                height: 470
            },
            title: null,
            mapNavigation: {
                enabled: true,
                buttonOptions: {
                    verticalAlign: 'bottom'
                }
            },
            colorAxis: {
                min: 0
            },
            series: [{
                data: mapData,
                name: 'Kebutuhan Guru',
                states: {
                    hover: {
                        color: '#BADA55'
                    }
                },
                dataLabels: {
                    enabled: true,
                    format: '{point.name}'
                }
            }]
        });
    });


    ShowDashboard();
    top_guru_provinsi();
    top_guru_kabupaten();

    ShowDashboard();
    // Update setiap 10 detik
    setInterval(ShowDashboard, 10000);
    
});