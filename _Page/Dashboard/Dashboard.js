$(document).ready(function() {
    // Inisialisasi peta
    var map = L.map('indonesia-map').setView([-2.5489, 118.0149], 5);

    // Tambahkan tile layer (background peta)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Variabel untuk menyimpan data JSON
    var provinceData = {};
    var geoJsonLayer;

    // Load data dari map_count.json
    $.getJSON('_Page/Dashboard/map_count.json', function(data) {
        // Konversi array menjadi object dengan KODE_PROV sebagai key
        data.forEach(function(province) {
            provinceData[province.KODE_PROV] = province;
        });

        // Load GeoJSON dan render peta
        $.getJSON('GeoJson/provinsi.json', function(geoJsonData) {
            renderMap(geoJsonData);
        }).fail(function() {
            console.error('Gagal memuat file GeoJSON');
        });
    }).fail(function() {
        console.error('Gagal memuat file map_count.json');
    });

    function renderMap(geoJsonData) {
        // Fungsi untuk menentukan warna berdasarkan jumlah guru yang kurang
        function getColor(kurangGuru) {
            return kurangGuru > 300 ? '#d73027' :
                   kurangGuru > 250 ? '#fc8d59' :
                   kurangGuru > 200 ? '#fee08b' :
                   kurangGuru > 150 ? '#d9ef8b' :
                   kurangGuru > 100 ? '#91cf60' :
                                      '#1a9850';
        }

        // Fungsi style untuk setiap feature
        function style(feature) {
            var kodeProv = feature.properties.KODE_PROV;
            var data = provinceData[kodeProv];
            
            return {
                fillColor: data ? getColor(data.kurang_guru) : '#ccc',
                weight: 2,
                opacity: 1,
                color: 'white',
                dashArray: '3',
                fillOpacity: 0.7
            };
        }

        // Fungsi untuk highlight saat hover
        function highlightFeature(e) {
            var layer = e.target;

            layer.setStyle({
                weight: 3,
                color: '#666',
                dashArray: '',
                fillOpacity: 0.9
            });

            if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
                layer.bringToFront();
            }

            // Update info panel (opsional)
            updateInfo(layer.feature.properties);
        }

        // Fungsi reset highlight
        function resetHighlight(e) {
            geoJsonLayer.resetStyle(e.target);
            updateInfo();
        }

        // Fungsi zoom saat klik
        function zoomToFeature(e) {
            map.fitBounds(e.target.getBounds());
        }

        // Fungsi saat klik pada provinsi
        function onFeatureClick(e) {
            var kodeProv = e.target.feature.properties.KODE_PROV;
            
            // Panggil modal dengan data-id
            // $('#ModalDetailMap').attr('data-id', kodeProv);
            // $('#ModalDetailMap').modal('show');
            
            // Anda bisa menambahkan logika untuk menampilkan data detail di modal di sini
            console.log('Provinsi diklik:', kodeProv);
        }

        // Event handlers untuk setiap feature
        function onEachFeature(feature, layer) {
            var kodeProv = feature.properties.KODE_PROV;
            var data = provinceData[kodeProv];
            
            // Bind popup dengan informasi provinsi
            if (data) {
                var popupContent = `
                    <div class="province-popup">
                        <h6><strong>${data.PROVINSI}</strong></h6>
                        <hr>
                        <table class="table table-sm">
                            <tr>
                                <td>ABK:</td>
                                <td><strong>${data.ABK.toLocaleString()}</strong></td>
                            </tr>
                            <tr>
                                <td>Jumlah Guru:</td>
                                <td><strong>${data.jumlah_guru.toLocaleString()}</strong></td>
                            </tr>
                            <tr>
                                <td>Kurang Guru:</td>
                                <td><strong class="text-danger">${data.kurang_guru.toLocaleString()}</strong></td>
                            </tr>
                            <tr>
                                <td>Kurang ASN:</td>
                                <td><strong class="text-warning">${data.kurang_asn.toLocaleString()}</strong></td>
                            </tr>
                        </table>
                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#ModalDetailMap" data-id="${kodeProv}">
                            <small class="text-primary">
                                Klik untuk detail lebih lanjut
                            </small>
                        </a>
                    </div>
                `;
                layer.bindPopup(popupContent);
            }

            layer.on({
                mouseover: highlightFeature,
                mouseout: resetHighlight,
                click: onFeatureClick
            });
        }

        // Render GeoJSON ke peta
        geoJsonLayer = L.geoJSON(geoJsonData, {
            style: style,
            onEachFeature: onEachFeature
        }).addTo(map);

        // Tambahkan legend
        var legend = L.control({position: 'bottomright'});

        legend.onAdd = function(map) {
            var div = L.DomUtil.create('div', 'info legend');
            var grades = [0, 100, 150, 200, 250, 300];
            var labels = ['<strong>Kekurangan Guru</strong>'];
            var from, to;

            for (var i = 0; i < grades.length; i++) {
                from = grades[i];
                to = grades[i + 1];

                labels.push(
                    '<i style="background:' + getColor(from + 1) + '"></i> ' +
                    from + (to ? '&ndash;' + to : '+'));
            }

            div.innerHTML = labels.join('<br>');
            return div;
        };

        legend.addTo(map);

        // Info panel (opsional)
        var info = L.control({position: 'topright'});

        info.onAdd = function(map) {
            this._div = L.DomUtil.create('div', 'info');
            this.update();
            return this._div;
        };

        info.update = function(props) {
            this._div.innerHTML = '<h6>Informasi Provinsi</h6>' + 
                (props ? 
                    '<b>' + props.PROVINSI + '</b><br>' +
                    'Kode: ' + props.KODE_PROV
                    : 'Arahkan kursor ke provinsi');
        };

        info.addTo(map);
    }

    // Fungsi update info panel
    function updateInfo(props) {
        // Implementasi update info panel jika diperlukan
    }
});

$('#ModalDetailMap').on('show.bs.modal', function (e) {
    var province_code = $(e.relatedTarget).data('id');
    $('#ShowDetailMap').html("Loading...");
    $.ajax({
        type 	    : 'POST',
        url 	    : '_Page/Dashboard/ShowDetailMap.php',
        data        : {province_code: province_code},
        success     : function(data){
            $('#ShowDetailMap').html(data);
        }
    });
});