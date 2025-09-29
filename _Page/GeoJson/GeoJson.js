$(document).on('click', '.show_child_row_data', function() {
    var provId = $(this).data('prov');
    var rows = $(".child-of-" + provId);

    // Toggle tampil/sembunyi
    rows.toggle();

    // Ganti ikon tombol
    var icon = $(this).find('i');
    if (rows.is(":visible")) {
        icon.removeClass("bi-chevron-down").addClass("bi-chevron-up");
    } else {
        icon.removeClass("bi-chevron-up").addClass("bi-chevron-down");
    }
});

$('#ModalShowMapProvinsi').on('show.bs.modal', function (e) {
    var kode_provinsi = $(e.relatedTarget).data('id');
    $('#ShowMapProvinsi').html("Loading...");

    // Tunggu modal benar-benar tampil supaya container punya ukuran
    setTimeout(function(){
        // Hapus isi lalu buat div map baru
        $('#ShowMapProvinsi').html('<div id="mapProv" style="height:600px;"></div>');

        // Inisialisasi peta
        var map = L.map('mapProv').setView([-2.5, 118], 5); // posisi default Indonesia

        // Tambah basemap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        // Load GeoJSON provinsi
        $.getJSON("GeoJson/provinsi.json", function(data){
            // Filter provinsi sesuai kode
            var provinsi = {
                "type": "FeatureCollection",
                "features": data.features.filter(function(f){
                    return f.properties.KODE_PROV == kode_provinsi;
                })
            };

            // Tambahkan ke map
            var layer = L.geoJson(provinsi, {
                style: {
                    color: "blue",
                    weight: 2,
                    fillColor: "lightblue",
                    fillOpacity: 0.5
                },
                onEachFeature: function(feature, layer){
                    layer.bindPopup("<b>"+feature.properties.PROVINSI+"</b><br/>Kode: "+feature.properties.KODE_PROV);
                }
            }).addTo(map);

            // Zoom ke provinsi
            map.fitBounds(layer.getBounds());
        });
    }, 500);
});

$('#ModalShowMapProvinsiAndAkbupaten').on('show.bs.modal', function (e) {
    var kode_provinsi = $(e.relatedTarget).data('id');
    $('#ShowMapProvinsiAndAkbupaten').html("Loading...");

    setTimeout(function(){
        $('#ShowMapProvinsiAndAkbupaten').html('<div id="mapProvKab" style="height:600px;"></div>');

        var map = L.map('mapProvKab').setView([-2.5, 118], 5);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        var kabLayer; // simpan layer kabupaten

        // load provinsi
        $.getJSON("GeoJson/provinsi.json", function(dataProv){
            var provinsi = {
                "type": "FeatureCollection",
                "features": dataProv.features.filter(function(f){
                    return f.properties.KODE_PROV == kode_provinsi;
                })
            };

            var provLayer = L.geoJson(provinsi, {
                style: { color: "blue", weight: 3, fillColor: "lightblue", fillOpacity: 0.4 },
                onEachFeature: function(feature, layer){
                    layer.bindPopup("<b>Provinsi:</b> " + feature.properties.PROVINSI);
                }
            }).addTo(map);

            map.fitBounds(provLayer.getBounds());

            // event zoom
            map.on("zoomend", function(){
                if(map.getZoom() >= 8){ // tampilkan kabupaten ketika zoom >= 8
                    if(!kabLayer){
                        $.getJSON("GeoJson/kabupaten.json", function(dataKab){
                            var kabupaten = {
                                "type": "FeatureCollection",
                                "features": dataKab.features.filter(function(f){
                                    // sesuaikan dengan field di file kabupaten.json
                                    return f.properties.KODE_PROV == kode_provinsi 
                                           || f.properties.KDPPUM == kode_provinsi;
                                })
                            };

                            kabLayer = L.geoJson(kabupaten, {
                                style: { color: "green", weight: 1.5, fillColor: "yellow", fillOpacity: 0.3 },
                                onEachFeature: function(feature, layer){
                                    layer.bindPopup(
                                        "<b>Kabupaten/Kota:</b> " + (feature.properties.WADMKK || '-') +
                                        "<br><b>Provinsi:</b> " + (feature.properties.WADMPR || '-')
                                    );
                                }
                            }).addTo(map);
                        });
                    }
                } else {
                    if(kabLayer){
                        map.removeLayer(kabLayer);
                        kabLayer = null;
                    }
                }
            });
        });
    }, 500);
});

$('#ModalShowMapKabupaten').on('show.bs.modal', function (e) {
    var objectId = $(e.relatedTarget).data('id'); // OBJECTID dari tombol
    $('#ShowMapKabupaten').html("Loading...");

    // Ambil geojson kabupaten
    fetch("GeoJson/kabupaten.json")
        .then(res => res.json())
        .then(kabGeoJSON => {
            // Filter kabupaten berdasarkan OBJECTID
            let filtered = {
                ...kabGeoJSON,
                features: kabGeoJSON.features.filter(f => f.properties.OBJECTID == objectId)
            };

            if (filtered.features.length === 0) {
                $('#ShowMapKabupaten').html("<div class='alert alert-danger'>Data kabupaten tidak ditemukan</div>");
                return;
            }

            // Render peta
            Highcharts.mapChart('ShowMapKabupaten', {
                chart: {
                    map: filtered
                },
                title: {
                    text: filtered.features[0].properties.WADMKK + " - " + filtered.features[0].properties.WADMPR
                },
                subtitle: {
                    text: 'Kabupaten dengan OBJECTID: ' + objectId
                },
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
                    data: filtered.features.map(f => [f.properties.OBJECTID, 1]),
                    keys: ['id', 'value'],
                    joinBy: 'id',
                    name: 'Kabupaten',
                    states: {
                        hover: {
                            color: '#f5a623'
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        format: '{point.name}'
                    }
                }]
            });
        })
        .catch(err => {
            $('#ShowMapKabupaten').html("<div class='alert alert-danger'>Error memuat GeoJSON: " + err + "</div>");
        });
});