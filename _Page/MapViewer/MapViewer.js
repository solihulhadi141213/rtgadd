$(document).ready(function() {
    let map = null;
    let geoJsonLayer = null;
    let indonesiaBounds = null;
    
    // Fungsi untuk menampilkan alert
    function showAlert(message, type) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        $('#alert-container').html(alertHtml);
        
        // Auto dismiss setelah 5 detik
        setTimeout(() => {
            $('.alert').alert('close');
        }, 5000);
    }
    
    // Fungsi untuk inisialisasi peta dengan tampilan Indonesia
    function initMap() {
        if (map === null) {
            // Buat peta dengan view seluruh Indonesia
            map = L.map('map_viewer').setView([-2.5, 118], 5);
            
            // Tambahkan tile layer (peta dasar)
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            
            // Tambahkan batas Indonesia (dalam bentuk persegi panjang sederhana)
            // Koordinat bounding box Indonesia
            indonesiaBounds = L.rectangle([
                [-11, 95],  // Barat daya
                [6, 141]    // Timur laut
            ], {
                color: "#ff3388",
                weight: 2,
                fillOpacity: 0.05
            }).addTo(map);
            
            // Tambahkan teks "INDONESIA" di tengah peta
            L.marker([-2.5, 118])
                .bindTooltip("INDONESIA", {permanent: true, className: "indo-label", direction: "center"})
                .addTo(map)
                .openTooltip();
            
            showAlert('Peta Indonesia berhasil dimuat. Silakan masukkan data GeoJSON Anda.', 'info');
        }
        return map;
    }
    
    // Fungsi untuk menampilkan GeoJSON pada peta
    function showGeoJSONOnMap(geoJsonData) {
        // Hapus layer GeoJSON sebelumnya jika ada
        if (geoJsonLayer) {
            map.removeLayer(geoJsonLayer);
        }
        
        // Tambahkan layer GeoJSON baru
        geoJsonLayer = L.geoJSON(geoJsonData, {
            style: function(feature) {
                return {
                    color: '#3388ff',
                    weight: 2,
                    opacity: 0.8,
                    fillColor: '#3388ff',
                    fillOpacity: 0.4
                };
            },
            onEachFeature: function(feature, layer) {
                // Tambahkan popup dengan informasi properti
                if (feature.properties) {
                    let popupContent = '<div class="p-2"><h6>Informasi GeoJSON</h6><table class="table table-sm">';
                    for (const key in feature.properties) {
                        popupContent += `<tr><td><b>${key}</b></td><td>${feature.properties[key]}</td></tr>`;
                    }
                    popupContent += '</table></div>';
                    layer.bindPopup(popupContent);
                }
                
                // Tambahkan tooltip dengan nama properti
                if (feature.properties && feature.properties.Name) {
                    layer.bindTooltip(feature.properties.Name, {
                        permanent: false,
                        direction: 'auto'
                    });
                }
            }
        }).addTo(map);
        
        // Sesuaikan tampilan peta agar sesuai dengan GeoJSON, tapi tetap dalam batas Indonesia
        const geoJsonBounds = geoJsonLayer.getBounds();
        map.fitBounds(geoJsonBounds, { padding: [20, 20] });
    }
    
    // Fungsi untuk mereset peta ke tampilan Indonesia
    function resetMap() {
        if (map) {
            map.setView([-2.5, 118], 5);
            if (geoJsonLayer) {
                map.removeLayer(geoJsonLayer);
                geoJsonLayer = null;
            }
            showAlert('Peta telah direset ke tampilan Indonesia.', 'info');
        }
    }
    
    // Event handler untuk tombol "Tampilkan Peta"
    $('#button_show_map').click(function() {
        const geoJsonText = $('#geo_json_example').val().trim();
        
        if (!geoJsonText) {
            showAlert('Silakan masukkan data GeoJSON terlebih dahulu.', 'warning');
            return;
        }
        
        try {
            // Parse string GeoJSON menjadi objek JavaScript
            const geoJsonData = JSON.parse(geoJsonText);
            
            // Validasi struktur GeoJSON
            if (!geoJsonData.type || geoJsonData.type !== 'FeatureCollection') {
                throw new Error('Format GeoJSON tidak valid. Harus berupa FeatureCollection.');
            }
            
            if (!geoJsonData.features || geoJsonData.features.length === 0) {
                throw new Error('Tidak ada fitur GeoJSON yang ditemukan.');
            }
            
            // Inisialisasi peta jika belum ada
            initMap();
            
            // Tampilkan GeoJSON pada peta
            showGeoJSONOnMap(geoJsonData);
            
            showAlert('GeoJSON berhasil ditampilkan pada peta Indonesia.', 'success');
        } catch (error) {
            console.error('Error parsing GeoJSON:', error);
            showAlert('Terjadi kesalahan: ' + error.message, 'danger');
        }
    });
    
    // Event handler untuk tombol "Reset Peta"
    $('#button_reset_map').click(function() {
        resetMap();
    });
    
    // Inisialisasi peta saat halaman pertama kali dimuat
    initMap();
});