// Fungsi Menampilkan Tabel dengan transisi halus & posisi scroll tetap
function filterAndLoadTable() {
    // Efek transisi: fadeOut lembut
    $('#TabelKebutuhanGuru').fadeTo(400, 0.3, function () {
        $.ajax({
            type    : 'POST',
            url     : '_Page/Dashboard/TabelKebutuhanGuru.php',
            success : function(data) {
                // Ganti konten
                $('#TabelKebutuhanGuru').html(data);

                // Efek transisi fadeIn lembut
                $('#TabelKebutuhanGuru').fadeTo(400, 1);
            }
        });
    });
}




$(document).ready(function() {
    var timestamp = new Date().getTime();
    //Menampilkan Tabel Pertama Kali
    filterAndLoadTable();

    //Modal Detail Muncul
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

    // ==================MENAMPILKAN PETA================================
    // var sample_code = [15,33,12,64,14];
    var sample_code = [];
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
    $.getJSON('_Page/Dashboard/map_count.json?v=1' + timestamp, function(res) {
        // Perbaikan: Struktur JSON yang diberikan adalah array langsung, bukan objek dengan property code dan metadata
        // Simpan ke object provinceData
        res.forEach(function(province) {
            provinceData[province.KODE_PROV] = province;
        });

        // Load GeoJSON
        $.getJSON('GeoJson/provinsi.json', function(geoJsonData) {
            renderMap(geoJsonData);
        }).fail(function() {
            console.error('Gagal memuat file GeoJSON');
        });
    }).fail(function() {
        console.error('Gagal memuat map_count.json');
    });

    function renderMap(geoJsonData) {
        // Fungsi untuk menentukan warna berdasarkan jumlah guru yang kurang
        function getColor(persentase_kebutuhan_guru) {
             return persentase_kebutuhan_guru == 0 ? '#ffffffff' :          
                persentase_kebutuhan_guru <= 10 ? '#aff8b5ff' :        
                persentase_kebutuhan_guru <= 20 ? '#77ff82ff' :        
                persentase_kebutuhan_guru <= 30 ? '#4dff5cff' :        
                persentase_kebutuhan_guru <= 40 ? '#08e61aff' :  
                persentase_kebutuhan_guru <= 50 ? '#18fd2bff' :        
                persentase_kebutuhan_guru <= 60 ? '#07ff1bff' :        
                persentase_kebutuhan_guru <= 70 ? '#09ce19ff' :        
                persentase_kebutuhan_guru <= 80 ? '#1cb629ff' :        
                persentase_kebutuhan_guru <= 90 ? '#069112ff' :        
                                '#04700dff';       
        }

        // Fungsi untuk menentukan style border berdasarkan sample_code
        function getBorderStyle(kodeProv) {
            // Jika KODE_PROV ada dalam sample_code, berikan border merah
            if (sample_code.includes(parseInt(kodeProv)) || sample_code.includes(kodeProv.toString())) {
                return {
                    weight: 4,
                    color: '#ff0000',
                    opacity: 1
                };
            } else {
                return {
                    weight: 2,
                    color: '#9e8ff0ff',
                    opacity: 1,
                    dashArray: '3'
                };
            }
        }

        // Fungsi style untuk setiap feature
        function style(feature) {
            var kodeProv = feature.properties.KODE_PROV;
            var data = provinceData[kodeProv];
            var borderStyle = getBorderStyle(kodeProv);
            
            return {
                fillColor: data ? getColor(data.persentase_kebutuhan_guru) : '#ccc',
                weight: borderStyle.weight,
                opacity: borderStyle.opacity,
                color: borderStyle.color,
                dashArray: borderStyle.dashArray,
                fillOpacity: 0.7
            };
        }

        // Fungsi untuk highlight saat hover
        function highlightFeature(e) {
            var layer = e.target;
            var kodeProv = layer.feature.properties.KODE_PROV;
            var borderStyle = getBorderStyle(kodeProv);

            layer.setStyle({
                weight: borderStyle.weight + 1,
                color: '#666',
                dashArray: '',
                fillOpacity: 0.9
            });

            if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
                layer.bringToFront();
            }

            // Update info panel
            updateInfo(layer.feature.properties);
        }

        // Fungsi reset highlight
        function resetHighlight(e) {
            geoJsonLayer.resetStyle(e.target);
            updateInfo();
        }

        // Fungsi saat klik pada provinsi
        function onFeatureClick(e) {
            var kodeProv = e.target.feature.properties.KODE_PROV;
            var data = provinceData[kodeProv];
            
            if (data) {
                // Tampilkan modal dengan informasi detail yang disederhanakan
                showProvinceDetailModal(data);
            }
            
            console.log('Provinsi diklik:', kodeProv);
        }

        // Event handlers untuk setiap feature
        function onEachFeature(feature, layer) {
            var kodeProv = feature.properties.KODE_PROV;
            var data = provinceData[kodeProv];
            
            // Bind popup dengan informasi provinsi yang disederhanakan
            if (data) {
                var popupContent = `
                    <div class="province-popup">
                        <h6><strong>${data.PROVINSI}</strong></h6>
                        <hr>
                        <table class="table table-sm table-borderless mb-2">
                            <tr>
                                <td>ABK:</td>
                                <td class="text-end"><strong>${data.ABK.toLocaleString()}</strong></td>
                            </tr>
                            <tr>
                                <td>Jumlah Guru:</td>
                                <td class="text-end"><strong>${data.jumlah_guru.toLocaleString()}</strong></td>
                            </tr>
                            <tr>
                                <td>Kebutuhan Guru:</td>
                                <td class="text-end"><strong class="text-danger">${data.kurang_guru.toLocaleString()}</strong></td>
                            </tr>
                            <tr>
                                <td>Persentase Kebutuhan Guru:</td>
                                <td class="text-end"><strong class="text-danger">${data.persentase_kebutuhan_guru.toLocaleString()} %</strong></td>
                            </tr>
                        </table>
                        <div class="text-center">
                            <button type="button" class="btn btn-sm btn-block btn-primary" data-bs-toggle="modal" data-bs-target="#ModalDetailMap" data-id="${kodeProv}">
                                Selengkapnya
                            </button>
                            <small class="text-muted">Klik untuk detail lengkap</small>
                        </div>
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

        legend.onAdd = function (map) {
            var div = L.DomUtil.create('div', 'legend card shadow-sm');

            var grades = [0, 1, 11, 21, 31, 41, 51, 61, 71, 81, 91];
            var labels = [];

            // Header card
            var html = `
                <div class="card-header py-2">
                    <strong class="card-title mb-0" style="font-size: 12px;">Keterangan</strong>
                </div>
                <div class="card-body py-2">
            `;

            for (var i = 0; i < grades.length; i++) {
                var from = grades[i];
                var to = grades[i + 1] ? grades[i + 1] - 1 : 100;

                var color = getColor(from === 0 ? 0 : from); // 0% tetap putih

                html += `
                    <div class="d-flex align-items-center mb-1">
                        <div style="
                            width: 20px; 
                            height: 15px; 
                            background-color: ${color};
                            border: 1px solid #ccc; 
                            margin-right: 6px;
                        "></div>
                        <small>${from === 0 ? '0 %' : from + ' % – ' + to + ' %'}</small>
                    </div>
                `;
            }

            html += `</div>`; // close card-body

            div.innerHTML = html;
            return div;
        };


        legend.addTo(map);

        // Info panel
        var info = L.control({position: 'topright'});

        info.onAdd = function(map) {
            this._div = L.DomUtil.create('div', 'info');
            this.update();
            return this._div;
        };

        info.update = function(props) {
            if (props) {
                var data = provinceData[props.KODE_PROV];
                if (data) {
                    var isSampleCode = sample_code.includes(parseInt(props.KODE_PROV)) || sample_code.includes(props.KODE_PROV.toString());
                    var sampleCodeIndicator = isSampleCode ? '<span style="color: red; font-weight: bold;"> ★</span>' : '';
                    
                    this._div.innerHTML = `
                        <h6>${data.PROVINSI}${sampleCodeIndicator}</h6>
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td>ABK:</td>
                                <td class="text-end"><strong>${data.ABK.toLocaleString()}</strong></td>
                            </tr>
                            <tr>
                                <td>Jumlah Guru:</td>
                                <td class="text-end"><strong>${data.jumlah_guru.toLocaleString()}</strong></td>
                            </tr>
                            <tr>
                                <td>Kebutuhan:</td>
                                <td class="text-end"><strong class="text-danger">${data.kurang_guru.toLocaleString()}</strong></td>
                            </tr>
                            <tr>
                                <td>Persentase Kebutuhan:</td>
                                <td class="text-end"><strong class="text-danger">${data.persentase_kebutuhan_guru.toLocaleString()} %</strong></td>
                            </tr>
                        </table>
                    `;
                } else {
                    this._div.innerHTML = "<small>Data tidak tersedia</small>";
                }
            } else {
                this._div.innerHTML = "<h6>Informasi Provinsi</h6><small>Arahkan kursor ke provinsi</small>";
            }
        };

        info.addTo(map);
    }

    // Fungsi untuk menampilkan modal detail (jika diperlukan)
    function showProvinceDetailModal(data) {
        // Implementasi fungsi ini sesuai kebutuhan Anda
        console.log('Menampilkan modal untuk:', data.PROVINSI);
    }

    // Fungsi update info (jika diperlukan)
    function updateInfo(props) {
        // Fungsi ini akan dipanggil oleh info.update()
    }

});

