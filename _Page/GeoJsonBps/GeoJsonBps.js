//Fungsi Menampilkan Data
function filterAndLoadTable() {
    var ProsesFilter = $('#ProsesFilter').serialize();
    $.ajax({
        type    : 'POST',
        url     : '_Page/GeoJsonBps/TabelGeoJsonBps.php',
        data    : ProsesFilter,
        success : function(data) {
            $('#TabelGeoJsonBps').html(data);
        }
    });
}

//Fungsi Request Data Provinsi
function RequestProvinsiFromBps() {
    var url_endpoint = $('#url_endpoint').val();
    //Reload Data dengan ajax
    $('#response_data').html('<tr><td colspan="4" class="text-center">Loading...</td></tr>');
    $.ajax({
        type 	    : 'POST',
        url 	    : '_Page/GeoJsonBps/ProsesSendRequestProvince.php',
        data        : {url_endpoint: url_endpoint},
        success     : function(data){
            $('#response_data').html(data);
        }
    });
}

//Fungsi Get Kab Kot
function RequestKabKot(id_geo_region) {
   $('#response_data_kab_kot').html("Loading...");
    $.ajax({
        type 	    : 'POST',
        url 	    : '_Page/GeoJsonBps/ProsesGetKabKot.php',
        data        : {id_geo_region: id_geo_region},
        success     : function(data){
            $('#response_data_kab_kot').html(data);
        }
    });
}

//Fungsi Detail
function ShowDetailGeoRegion(id_geo_region) {
    
    $('#TabelKabKot').html("Loading...");
    $.ajax({
        type 	    : 'POST',
        url 	    : '_Page/GeoJsonBps/TabelKabKot.php',
        data        : {id_geo_region: id_geo_region},
        success     : function(data){
            $('#TabelKabKot').html(data);
        }
    });
}

//Menampilkan Data Pertama Kali
$(document).ready(function() {
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

    //Filter Data
    $('#ProsesFilter').submit(function(){
        $('#page').val("1");
        filterAndLoadTable();
        $('#ModalFilter').modal('hide');
    });

    //Ketika KeywordBy Diubah
    $('#KeywordBy').change(function(){
        var KeywordBy = $('#KeywordBy').val();
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/GeoJsonBps/FormFilter.php',
            data        : {KeywordBy: KeywordBy},
            success     : function(data){
                $('#FormFilter').html(data);
            }
        });
    });

    //Ketika send_request di click
    $('#send_request').click(function(){
        RequestProvinsiFromBps();
    });

    //Proses Tambah Kelas
    $('#ProsesTambah').submit(function(){
        $('#NotifikasiTambah').html('<div class="spinner-border text-secondary" role="status"><span class="sr-only"></span></div>');
        
        var form = $('#ProsesTambah')[0];
        var data = new FormData(form);
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/GeoJsonBps/ProsesTambah.php',
            data 	    :  data,
            cache       : false,
            processData : false,
            contentType : false,
            enctype     : 'multipart/form-data',
            success     : function(data){
                $('#NotifikasiTambah').html(data);
                var NotifikasiTambahBerhasil=$('#NotifikasiTambahBerhasil').html();
                if(NotifikasiTambahBerhasil=="Berhasil"){
                    
                    //Request Ulang Data
                    RequestProvinsiFromBps();
                    
                    //Menampilkan Ulang Data
                    filterAndLoadTable();
                }
            }
        });
    });

    //Modal Menampilkan Tabel Kabupaten
    $('#ModalDetail').on('show.bs.modal', function (e) {
        var id_geo_region = $(e.relatedTarget).data('id');
        ShowDetailGeoRegion(id_geo_region);
    });

    //Proses Simpan GeoJson Kabupaten
    $('#ProsesSimpanGeoJsonCoordinateDistrict').submit(function(){
        $('#NotifikasiSimpanGeoJsonCoordinateDistrict').html('<div class="spinner-border text-secondary" role="status"><span class="sr-only"></span></div>');
        var form = $('#ProsesSimpanGeoJsonCoordinateDistrict')[0];
        var data = new FormData(form);
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/GeoJsonBps/ProsesSimpanGeoJsonCoordinateDistrict.php',
            data 	    :  data,
            cache       : false,
            processData : false,
            contentType : false,
            enctype     : 'multipart/form-data',
            success     : function(data){
                $('#NotifikasiSimpanGeoJsonCoordinateDistrict').html(data);
                var NotifikasiSimpanGeoJsonCoordinateDistrictBerhasil=$('#NotifikasiSimpanGeoJsonCoordinateDistrictBerhasil').html();
                if(NotifikasiSimpanGeoJsonCoordinateDistrictBerhasil=="Berhasil"){
                    $('#NotifikasiSimpanGeoJsonCoordinateDistrict').html('');

                    //Tutup Modal
                    $('#ModalGeoJsonCoordinateDistrict').modal('hide');

                    //Tampilkan Swal
                     Swal.fire(
                        'Success!',
                        'Menyimpan GeoJson Kab/Kota Berhasil!',
                        'success'
                    )
                    //Menampilkan Data
                    filterAndLoadTable();
                }
            }
        });
    });
    

    //Modal Get Coordinates
    $('#ModalGetCoordinates').on('show.bs.modal', function (e) {
        var id_geo_region = $(e.relatedTarget).data('id');
        $('#FormGetCoordinates').html("Loading...");
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/GeoJsonBps/FormGetCoordinates.php',
            data        : {id_geo_region: id_geo_region},
            success     : function(data){
                $('#FormGetCoordinates').html(data);
            }
        });
    });

    //Proses Get Coordinates
    $('#ProsesGetCoordinates').submit(function(){
        $('#NotifikasiGetCoordinates').html('<div class="spinner-border text-secondary" role="status"><span class="sr-only"></span></div>');

        //Tangkap JSON string pada FormFoordinate yang ada di _Page/GeoJsonBps/FormGetCoordinates.php
        var ProsesGetCoordinates=$('#ProsesGetCoordinates').serialize();

        //Kirim dengan AJAX
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/GeoJsonBps/ProsesGetCoordinates.php',
            data 	    :  ProsesGetCoordinates,
            success     : function(data){
                $('#NotifikasiGetCoordinates').html(data);
                var NotifikasiGetCoordinatesBerhasil=$('#NotifikasiGetCoordinatesBerhasil').html();
                if(NotifikasiGetCoordinatesBerhasil=="Berhasil"){

                    //Kosongkan Notifikasi
                    $('#NotifikasiGetCoordinates').html('');

                    //Tutup Modal
                    $('#ModalGetCoordinates').modal('hide');

                    //Tampilkan Swal
                    Swal.fire(
                        'Success!',
                        'Ubah Jabatan Per Instansi Berhasil!',
                        'success'
                    )
                    //Menampilkan Ulang Data
                    filterAndLoadTable();
                }
            }
        });
    });

    //Modal Edit
    $('#ModalEdit').on('show.bs.modal', function (e) {
        var id_geo_region = $(e.relatedTarget).data('id');
        $('#FormEdit').html("Loading...");
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/GeoJsonBps/FormEdit.php',
            data        : {id_geo_region: id_geo_region},
            success     : function(data){
                $('#FormEdit').html(data);
                $('#NotifikasiEdit').html('');

                //Enable tombol
                $('#ButtonEdit').prop('disabled', false);
            }
        });
    });

    //Proses Edit
    $('#ProsesEdit').submit(function(){
        $('#NotifikasiEdit').html('<div class="spinner-border text-secondary" role="status"><span class="sr-only"></span></div>');
        var form = $('#ProsesEdit')[0];
        var data = new FormData(form);
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/GeoJsonBps/ProsesEdit.php',
            data 	    :  data,
            cache       : false,
            processData : false,
            contentType : false,
            enctype     : 'multipart/form-data',
            success     : function(data){
                $('#NotifikasiEdit').html(data);
                var NotifikasiEditBerhasil=$('#NotifikasiEditBerhasil').html();
                var id_geo_region_put = $('#id_geo_region_put').val();

                if(NotifikasiEditBerhasil=="Berhasil"){
                    $('#NotifikasiEdit').html('');
                    $('#ModalEdit').modal('hide');
                    Swal.fire(
                        'Success!',
                        'Ubah Geo Region Berhasil!',
                        'success'
                    );

                    //Jika Data Yang Di Edit Memiliki id_geo_region_put
                    if(id_geo_region_put!==""){
                        $('#ModalDetail').modal('show');
                        ShowDetailGeoRegion(id_geo_region_put);
                    }
                    //Menampilkan Data
                    filterAndLoadTable();
                }
            }
        });
    });

    //Modal Get Kab Kot
    $('#ModalGetKabKot').on('show.bs.modal', function (e) {
        var id_geo_region = $(e.relatedTarget).data('id');
        RequestKabKot(id_geo_region);
        //kosongkan notifikasi
        $('#NotifikasiGetKabKot').html('');
    });

    //Proses ProsesGetKabKot
    $('#ProsesGetKabKot').submit(function(){
        $('#NotifikasiGetKabKot').html('<div class="spinner-border text-secondary" role="status"><span class="sr-only"></span></div>');
        var form = $('#ProsesGetKabKot')[0];
        var data = new FormData(form);
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/GeoJsonBps/ProsesSimpanKabKot.php',
            data 	    :  data,
            cache       : false,
            processData : false,
            contentType : false,
            enctype     : 'multipart/form-data',
            success     : function(data){
                $('#NotifikasiGetKabKot').html(data);
                var NotifikasiGetKabKotBerhasil=$('#NotifikasiGetKabKotBerhasil').html();
                var id_geo_region=$('#id_geo_region_put').val();
                if(NotifikasiGetKabKotBerhasil=="Berhasil"){
                    //Muat ulang kab/kot
                    RequestKabKot(id_geo_region);
                    //Menampilkan Data
                    filterAndLoadTable();
                }
            }
        });
    });

    //Modal Hapus
    $('#ModalHapus').on('show.bs.modal', function (e) {
        var id_geo_region = $(e.relatedTarget).data('id');
        $('#FormHapus').html("Loading...");
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/GeoJsonBps/FormHapus.php',
            data        : {id_geo_region: id_geo_region},
            success     : function(data){
                $('#FormHapus').html(data);

                //Kosongkan Notifikasi
                $('#NotifikasiHapus').html('');

                //Enable tombol
                $('#ButtonHapus').prop('disabled', false);
            }
        });
    });

    //Proses Hapus
    $('#ProsesHapus').submit(function(){
        $('#NotifikasiHapus').html('<div class="spinner-border text-secondary" role="status"><span class="sr-only"></span></div>');
        var form = $('#ProsesHapus')[0];
        var data = new FormData(form);
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/GeoJsonBps/ProsesHapus.php',
            data 	    :  data,
            cache       : false,
            processData : false,
            contentType : false,
            enctype     : 'multipart/form-data',
            success     : function(data){
                $('#NotifikasiHapus').html(data);
                var NotifikasisHapusBerhasil=$('#NotifikasisHapusBerhasil').html();
                var id_geo_region_put_edit = $('#id_geo_region_put_edit').val();
                if(NotifikasisHapusBerhasil=="Berhasil"){
                    $('#NotifikasisHapus').html('');

                    //Tutup Modal
                    $('#ModalHapus').modal('hide');

                    //Tampilkan Swal
                     Swal.fire(
                        'Success!',
                        'Hapus Jabatan Berhasil!',
                        'success'
                    );

                    //Jika Data Yang Di Edit Memiliki id_geo_region_put
                    if(id_geo_region_put_edit!==""){
                        $('#ModalDetail').modal('show');
                        ShowDetailGeoRegion(id_geo_region_put_edit);
                    }

                    //Menampilkan Data
                    filterAndLoadTable();
                }
            }
        });
    });

    //Modal Export
    $('#ModalExport').on('show.bs.modal', function (e) {
        $('#FormExport').html("Loading...");
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/GeoJsonBps/FormExport.php',
            success     : function(data){
                $('#FormExport').html(data);
            }
        });
    });

    // MODAL SHOW MAP (Berfungsi Untuk Menampilkan Peta Pada Modal)
    $('#ModalShowMap').on('show.bs.modal', function (e) {
        var id_geo_region = $(e.relatedTarget).data('id');
        $('#FormShowMap').html("Loading...");
        $('#container_for_view_map').html('<div class="text-center p-5">Waiting Process..</div>');

        $.ajax({
            type        : 'POST',
            url         : '_Page/GeoJsonBps/FormShowMap.php',
            data        : {id_geo_region: id_geo_region},
            success: function(response){
                $('#FormShowMap').html(response);
            }
        });
    });
});


// Jika masih error, coba definisikan semua fungsi di global scope
$(document).ready(function() {
    
    // Definisikan fungsi di global scope
    window.enableSaveButton = function() {
        $('#ProsesSimpanGeoJsonCoordinateDistrict button[type="submit"]')
            .prop('disabled', false)
            .html('<i class="bi bi-save"></i> Simpan Data');
    };
    
    window.processDistrictsSequentially = function(districtCodes, currentIndex, total) {
        if (currentIndex >= districtCodes.length) {
            // Semua proses selesai
            $('#progressBar')
                .css('width', '100%')
                .text('100%')
                .removeClass('progress-bar-animated');
            $('#progressStatus').html(`
                <span class="text-success">
                    <i class="bi bi-check-circle"></i> Semua data berhasil diproses!
                </span>
            `);
            
            // Aktifkan tombol simpan
            window.enableSaveButton();
            return;
        }
        
        var districtCode = districtCodes[currentIndex];
        var progress = Math.round((currentIndex / total) * 100);
        
        // Update progress bar
        $('#progressBar')
            .css('width', progress + '%')
            .text(progress + '%');
        $('#progressStatus').html(`
            Memproses data ${currentIndex + 1} dari ${total}...
            <br><small>Kode: ${districtCode}</small>
        `);
        
        // Kirim request untuk satu district
        $.ajax({
            type: 'POST',
            url: '_Page/GeoJsonBps/ProcessSingleDistrict.php',
            data: {
                district_code: districtCode,
                current_index: currentIndex,
                total: total
            },
            success: function(response) {
                // Tambahkan hasil ke form
                $('#FormGetMapDistrict').append(response);
                
                // Proses berikutnya setelah delay 1 detik
                setTimeout(function() {
                    window.processDistrictsSequentially(districtCodes, currentIndex + 1, total);
                }, 1000);
            },
            error: function(xhr, status, error) {
                // Tetap lanjut ke berikutnya meski error
                $('#FormGetMapDistrict').append(`
                    <div class="row mb-3 border-bottom">
                        <div class="col-12">
                            <div class="alert alert-warning">
                                <small>Gagal memproses kode: ${districtCode} - ${error}</small>
                            </div>
                        </div>
                    </div>
                `);
                
                // Lanjut ke berikutnya setelah delay
                setTimeout(function() {
                    window.processDistrictsSequentially(districtCodes, currentIndex + 1, total);
                }, 1000);
            }
        });
    };
    
    //Proses Mendapatkan GeoJson Kabupaten
    $('#ProsesGetGeoJsonCoordinateDistrict').submit(function(e){
        e.preventDefault();
        
        //Tangkap Data Dari Form
        var ProsesGetGeoJsonCoordinateDistrict = $('#ProsesGetGeoJsonCoordinateDistrict').serialize();
        
        //Tampilkan Modal
        $('#ModalGeoJsonCoordinateDistrict').modal('show');
        $('#ModalDetail').modal('hide');
        
        //Kirimkan data dengan ajax untuk memulai proses
        $('#FormGetMapDistrict').html(`
            <div class="text-center">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p>Memulai proses pengambilan data...</p>
                <div class="progress mb-3">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" 
                         id="progressBar" role="progressbar" 
                         style="width: 0%" 
                         aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                        0%
                    </div>
                </div>
                <div id="progressStatus" class="small text-muted">
                    Menyiapkan proses...
                </div>
            </div>
        `);
        
        $.ajax({
            type: 'POST',
            url: '_Page/GeoJsonBps/FormGetMapDistrict.php',
            data: ProsesGetGeoJsonCoordinateDistrict,
            dataType: 'json',
            success: function(response){
                if(response.status === 'success') {
                    // Mulai proses sequential dengan progress
                    window.processDistrictsSequentially(response.district_codes, 0, response.total);
                } else {
                    $('#FormGetMapDistrict').html(`
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle"></i> ${response.message}
                        </div>
                    `);
                }
            },
            error: function(){
                $('#FormGetMapDistrict').html(`
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i> Gagal memulai proses!
                    </div>
                `);
            }
        });
    });
});

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
        $('#ModalShowMap').modal('hide');
        $('html, body').animate({scrollTop: 0}, 'slow');
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
