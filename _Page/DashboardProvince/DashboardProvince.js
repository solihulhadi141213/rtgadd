//Fungsi Menampilkan Data
function filterAndLoadTable() {
    var ProsesFilter = $('#ProsesFilter').serialize();

    // Efek transisi: fadeOut dulu
    $('#TabelKebutuhanGuruByKabKot').fadeOut(200, function () {
        $.ajax({
            type    : 'POST',
            url     : '_Page/DashboardProvince/TabelKebutuhanGuruByKabKot.php',
            data    : ProsesFilter,
            success : function(data) {
                $('#TabelKebutuhanGuruByKabKot').html(data);

                // Setelah ganti konten → fadeIn lagi
                $('#TabelKebutuhanGuruByKabKot').fadeIn(200);
            }
        });
    });
}

$(document).ready(function () {
    var kode_provinsi = $("#kode_provinsi").val();

    //Load Data Kebutuhan Guru menurut kabupaten/Kota
    filterAndLoadTable();

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