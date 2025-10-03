$(document).ready(function () {
    var kode_provinsi = $("#kode_provinsi").val();

    // ===================== PIE CHART APEXCHART =====================
    $.getJSON("_Page/DashboardProvince/kebutuhan_guru_by_jenjang.json", function(data) {
        let provData = data.find(item => item.province_code === kode_provinsi);

        if(provData && provData.kebutuhan_guru_by_jenjang){
            let seriesData = provData.kebutuhan_guru_by_jenjang.map(item => item.kebutuhan);
            let labelsData = provData.kebutuhan_guru_by_jenjang.map(item => item.jenjang);

            var options = {
                chart: {
                    type: 'pie',
                    height: 385
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
    var map = L.map('ShowMapProvinsiAndAkbupaten').setView([-2, 118], 5);

    // Tile Basemap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    // Load GeoJson Provinsi
    $.getJSON("GeoJson/provinsi.json", function(provinsi) {
        L.geoJSON(provinsi, {
            filter: function(feature) {
                return feature.properties.KODE_PROV === kode_provinsi;
            },
            style: {
                color: "blue",
                weight: 2,
                fillColor: "lightblue",
                fillOpacity: 0.4
            }
        }).addTo(map);
    });

    // Load GeoJson Kabupaten
    $.getJSON("GeoJson/kabupaten.json", function(kabupaten) {
        var kabLayer = L.geoJSON(kabupaten, {
            filter: function(feature) {
                return feature.properties.KDPPUM === kode_provinsi;
            },
            style: {
                color: "green",
                weight: 1,
                fillColor: "yellow",
                fillOpacity: 0.5
            },
            onEachFeature: function (feature, layer) {
                var popupContent = "<b>" + feature.properties.WADMKK + "</b><br>" +
                                   "Kecamatan: " + (feature.properties.WADMKC || '-') + "<br>" +
                                   "Desa/Kelurahan: " + (feature.properties.WADMKD || '-') + "<br>" +
                                   "Luas: " + feature.properties.LUAS + " km²";
                layer.bindPopup(popupContent);
            }
        }).addTo(map);

        // Zoom ke batas kabupaten di provinsi tersebut
        map.fitBounds(kabLayer.getBounds());
    });
});