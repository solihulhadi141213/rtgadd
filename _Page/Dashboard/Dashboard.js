// Fungsi Menampilkan Tabel dengan transisi halus & posisi scroll tetap
function filterAndLoadTable() {
    var ProsesFilter = $('#ProsesFilter').serialize();

    // Simpan posisi scroll saat ini
    var currentScroll = $(window).scrollTop();

    // Efek transisi: fadeOut lembut
    $('#TabelKebutuhanGuru').fadeTo(400, 0.3, function () {
        $.ajax({
            type    : 'POST',
            url     : '_Page/Dashboard/TabelKebutuhanGuru.php',
            data    : ProsesFilter,
            success : function(data) {
                // Ganti konten
                $('#TabelKebutuhanGuru').html(data);

                // Efek transisi fadeIn lembut
                $('#TabelKebutuhanGuru').fadeTo(400, 1);

                // Kembalikan posisi scroll agar layar tidak bergerak
                $(window).scrollTop(currentScroll);
            }
        });
    });
}

let timestamp = new Date().getTime();

$(document).ready(function() {
    //Menampilkan Tabel Pertama Kali
    filterAndLoadTable();

    //Pagging
    $(document).on('click', '#next_button', function() {
        var page_now = parseInt($('#page').val(), 10); // Pastikan nilai diambil sebagai angka
        var next_page = page_now + 1;
        $('#page').val(next_page);
        filterAndLoadTable(0);
    });
    $(document).on('click', '#prev_button', function() {
        var page_now = parseInt($('#page').val(), 10); // Pastikan nilai diambil sebagai angka
        var next_page = page_now - 1;
        $('#page').val(next_page);
        filterAndLoadTable(0);
    });

    //Submit Filter Data
    $('#ProsesFilter').submit(function(){

        //Reset Halaman Ke halaman 1
        $('#page').val("1");

        //Tampilkan Ulang Data
        filterAndLoadTable();

        //Tutup Modal Filter
        $('#ModalFilter').modal('hide');
    });

    // Inisialisasi peta
    var map = L.map('indonesia-map').setView([-2.5489, 118.0149], 5);

    // Tambahkan tile layer (background peta)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Variabel untuk menyimpan data JSON
    var provinceData = {};
    var geoJsonLayer;

    // Load data dari map_count.php
    $.getJSON('_Page/Dashboard/map_count.php?v=1' + timestamp, function(res) {
        if (res.code !== 200) {
            console.error(res.message);
            return;
        }

        // Simpan ke object provinceData
        res.metadata.forEach(function(province) {
            provinceData[province.KODE_PROV] = province;
        });

        // Load GeoJSON
        $.getJSON('GeoJson/provinsi.json', function(geoJsonData) {
            renderMap(geoJsonData);
        }).fail(function() {
            console.error('Gagal memuat file GeoJSON');
        });
    }).fail(function() {
        console.error('Gagal memuat map_count.php');
    });

    function renderMap(geoJsonData) {
        // Fungsi untuk menentukan warna berdasarkan jumlah guru yang kurang
        function getColor(kurangGuru) {
            return kurangGuru > 1000 ? '#020a79ff' :
                   kurangGuru > 750 ? '#201cffff' :
                   kurangGuru > 500 ? '#5d5bffff' :
                   kurangGuru > 200 ? '#7f7dffff' :
                   kurangGuru > 100 ? '#8e99faff' :
                   kurangGuru > 50 ? '#a7aff5ff' :
                   kurangGuru > 10 ? '#ccccf5ff' :
                                      '#f8f8f8ff';
        }

        // Fungsi style untuk setiap feature
        function style(feature) {
            var kodeProv = feature.properties.KODE_PROV;
            var data = provinceData[kodeProv];
            
            return {
                fillColor: data ? getColor(data.kurang_guru) : '#ccc',
                weight: 2,
                opacity: 1,
                color: '#9e8ff0ff',
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

                        <div class="row">
                            <div class="col-12 text-center">
                                <a href="javascript:void(0);" class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#ModalDetailMap" data-id="${kodeProv}">
                                    Selengkapnya
                                </a>
                            </div>
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

        legend.onAdd = function(map) {
            var div = L.DomUtil.create('div', 'info legend');
            var grades = [0, 10, 50, 100, 200, 500, 750, 1000];
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
            if (props) {
                var data = provinceData[props.KODE_PROV];
                if (data) {
                    this._div.innerHTML = `
                        <h6>${data.PROVINSI}</h6>
                        <small>Kode: ${props.KODE_PROV}</small><br>
                        ABK: <b>${data.ABK.toLocaleString()}</b><br>
                        Jumlah Guru: <b>${data.jumlah_guru.toLocaleString()}</b><br>
                        Kurang Guru: <b class="text-danger">${data.kurang_guru.toLocaleString()}</b><br>
                        Kurang ASN: <b class="text-warning">${data.kurang_asn.toLocaleString()}</b>
                    `;
                } else {
                    this._div.innerHTML = "<small>Data tidak tersedia</small>";
                }
            } else {
                this._div.innerHTML = "<h6>Informasi Provinsi</h6>Arahkan kursor ke provinsi";
            }
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