//Fungsi Menampilkan Data
function filterAndLoadTable() {
    var ProsesFilter = $('#ProsesFilter').serialize();

    // Lembut: turunkan opacity jadi 0.3
    $('#TabelWilayah').fadeTo(200, 0.3, function () {
        $.ajax({
            type    : 'POST',
            url     : '_Page/Wilayah/TabelWilayah.php',
            data    : ProsesFilter,
            success : function(data) {
                $('#TabelWilayah').html(data);

                // Naikkan lagi opacity ke 1
                $('#TabelWilayah').fadeTo(200, 1);
            }
        });
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
            url 	    : '_Page/Wilayah/FormFilter.php',
            data        : {KeywordBy: KeywordBy},
            success     : function(data){
                $('#FormFilter').html(data);
            }
        });
    });

    //FORM TAMBAH WILAYAH
    //Ketika memilih kategori
    $('#category_wilayah').change(function(){
        var category_wilayah = $('#category_wilayah').val();
        if(category_wilayah=="Province"){

            //Tempelkan input text form provinsi
            $.ajax({
                type 	    : 'POST',
                url 	    : '_Page/Wilayah/FormInputProvince.php',
                success     : function(data){
                    $('#FormProvince').html(data);
                }
            });

            //Kosongkan Form District
            $('#FormDistrict').html('');

            //Kosongkan Notifikasi
            $('#NotifikasiTambah').html('');

            //Enable tombol
            $('#ButtonTambah').prop('disabled', false);
        }
        if(category_wilayah=="District"){

            //Tempelkan Select option form provinsi
            $.ajax({
                type 	    : 'POST',
                url 	    : '_Page/Wilayah/FormSelectProvince.php',
                success     : function(data){
                    $('#FormProvince').html(data);
                }
            });

            //Tempelkan Form Input District
            $.ajax({
                type 	    : 'POST',
                url 	    : '_Page/Wilayah/FormInputDistrict.php',
                success     : function(data){
                    $('#FormDistrict').html(data);
                }
            });


            //Kosongkan Notifikasi
            $('#NotifikasiTambah').html('');

            //Enable tombol
            $('#ButtonTambah').prop('disabled', false);
        }
    });

    //Proses Tambah Kelas
    $('#ProsesTambah').submit(function(){
        $('#NotifikasiTambah').html('<div class="spinner-border text-secondary" role="status"><span class="sr-only"></span></div>');
        var form = $('#ProsesTambah')[0];
        var data = new FormData(form);
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Wilayah/ProsesTambah.php',
            data 	    :  data,
            cache       : false,
            processData : false,
            contentType : false,
            enctype     : 'multipart/form-data',
            success     : function(data){
                $('#NotifikasiTambah').html(data);
                var NotifikasiTambahBerhasil=$('#NotifikasiTambahBerhasil').html();
                if(NotifikasiTambahBerhasil=="Berhasil"){
                   //Tutup Modal
                    $('#ModalTambah').modal('hide');

                    //Menampilkan Data
                    filterAndLoadTable();
                    Swal.fire(
                        'Success!',
                        'Tambah Wilayah Berhasil!',
                        'success'
                    );
                    //Reset Form
                    $("#ProsesTambah")[0].reset();
                }
            }
        });
    });

    //Modal Detail
    $('#ModalDetail').on('show.bs.modal', function (e) {
        var id_region = $(e.relatedTarget).data('id');
        $('#FormDetail').html("Loading...");
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Wilayah/FormDetail.php',
            data        : {id_region: id_region},
            success     : function(data){
                $('#FormDetail').html(data);
            }
        });
    });

    //Modal Edit
    $('#ModalEdit').on('show.bs.modal', function (e) {
        var id_region = $(e.relatedTarget).data('id');
        $('#FormEdit').html("Loading...");
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Wilayah/FormEdit.php',
            data        : {id_region: id_region},
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
            url 	    : '_Page/Wilayah/ProsesEdit.php',
            data 	    :  data,
            cache       : false,
            processData : false,
            contentType : false,
            enctype     : 'multipart/form-data',
            success     : function(data){
                $('#NotifikasiEdit').html(data);
                var NotifikasiEditBerhasil=$('#NotifikasiEditBerhasil').html();
                if(NotifikasiEditBerhasil=="Berhasil"){
                    $('#NotifikasiEdit').html('');
                    $('#ModalEdit').modal('hide');
                    Swal.fire(
                        'Success!',
                        'Ubah Wilayah Berhasil!',
                        'success'
                    )
                    //Menampilkan Data
                    filterAndLoadTable();
                }
            }
        });
    });

    //Modal Hapus
    $('#ModalHapus').on('show.bs.modal', function (e) {
        var id_region = $(e.relatedTarget).data('id');
        $('#FormHapus').html("Loading...");
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Wilayah/FormHapus.php',
            data        : {id_region: id_region},
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
            url 	    : '_Page/Wilayah/ProsesHapus.php',
            data 	    :  data,
            cache       : false,
            processData : false,
            contentType : false,
            enctype     : 'multipart/form-data',
            success     : function(data){
                $('#NotifikasiHapus').html(data);
                var NotifikasisHapusBerhasil=$('#NotifikasisHapusBerhasil').html();
                if(NotifikasisHapusBerhasil=="Berhasil"){
                    $('#NotifikasisHapus').html('');

                    //Tutup Modal
                    $('#ModalHapus').modal('hide');

                    //Tampilkan Swal
                     Swal.fire(
                        'Success!',
                        'Hapus Wilayah Berhasil!',
                        'success'
                    )
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
            url 	    : '_Page/Wilayah/FormExport.php',
            success     : function(data){
                $('#FormExport').html(data);
            }
        });
    });

    // PROSES IMPORT
    // INIT VARIABLES
    let stopProcess = false;
    let totalData = 0;
    let processedData = 0;
    let currentParser = null;

    // Object untuk menyimpan statistik
    let stats = {
        // Validasi
        empty_province_code: 0,
        empty_province_code_dapodik: 0,
        empty_province_name: 0,
        empty_district_code: 0,
        empty_district_code_dapodik: 0,
        empty_district_name: 0,
        
        // Operasi Database Province
        insert_province_success: 0,
        insert_province_failed: 0,
        update_province_success: 0,
        update_province_failed: 0,
        
        // Operasi Database District
        insert_district_success: 0,
        insert_district_failed: 0,
        update_district_success: 0,
        update_district_failed: 0,
        
        // Error details
        error_details: []
    };

    // Jika Reset Form Import
    $('#ResetFormImport').click(function(){
        resetCounter();
        $("#progressSection").hide();
        $('#ResetFormImport').prop('disabled', true);
        $('#btnImport').prop('disabled', false);
        $('#btnStop').prop('disabled', true);
    });

    // Tombol Stop
    $('#btnStop').click(function(){
        stopProcess = true;
        $(this).prop('disabled', true);
        if (currentParser) {
            currentParser.abort();
        }
        $('#progressDetail').html('<strong class="text-warning">Proses dihentikan oleh pengguna!</strong>');
    });

    // Proses Import
    $('#ProsesImport').submit(function(e){
        e.preventDefault();
        
        let file = $('#data_wilayah')[0].files[0];
        if (!file) {
            alert('Pilih file CSV terlebih dahulu!');
            return;
        }

        // Reset semua counter
        resetCounter();
        
        // Tampilkan progress section
        $("#progressSection").show();
        $('#btnImport').prop('disabled', true);
        $('#btnStop').prop('disabled', false);
        $('#ResetFormImport').prop('disabled', true);

        // Mulai proses import dengan Papa Parse
        startCSVImport(file);
    });

    // Fungsi untuk memulai proses import CSV
    function startCSVImport(file) {
        console.log('Memulai proses import CSV...');

        // Pertama, hitung total baris
        countTotalRows(file).then(totalRows => {
            totalData = totalRows;
            console.log('Total data yang akan diproses:', totalData);
            updateUI();
            
            // Mulai parsing dengan Papa Parse
            startParsing(file);
        }).catch(error => {
            console.error('Error counting rows:', error);
            showError('Error menghitung total data: ' + error.message);
        });
    }

    // Fungsi untuk menghitung total baris CSV
    function countTotalRows(file) {
        return new Promise((resolve, reject) => {
            let rowCount = 0;
            
            Papa.parse(file, {
                header: true,
                skipEmptyLines: true,
                chunkSize: 10000,
                step: function(results, parser) {
                    // Skip empty rows
                    if (results.data && Object.keys(results.data).length > 0) {
                        rowCount++;
                    }
                },
                complete: function() {
                    resolve(rowCount);
                },
                error: function(error) {
                    reject(error);
                }
            });
        });
    }

    // Fungsi untuk memulai parsing dengan Papa Parse
    function startParsing(file) {
        $('#progressDetail').text('Memproses data...');
        
        // OPTIMASI: Tingkatkan chunkSize menjadi 500-1000 baris
        const CHUNK_SIZE = 1000; // ↑ Naikkan dari 100 menjadi 500
        
        Papa.parse(file, {
            header: true,
            skipEmptyLines: true,
            dynamicTyping: true,
            chunkSize: CHUNK_SIZE, // Proses 500 baris per batch
            chunk: function(results, parser) {
                if (stopProcess) {
                    parser.abort();
                    $('#progressDetail').text('Proses dihentikan!');
                    return;
                }

                currentParser = parser;
                let batch = results.data;
                
                // Validasi jika batch kosong
                if (!batch || batch.length === 0) {
                    parser.resume();
                    return;
                }

                // Pause parser sementara menunggu AJAX selesai
                parser.pause();

                // Proses batch secara synchronous
                processBatch(batch).then(() => {
                    if (!stopProcess) {
                        // Lanjutkan parsing setelah batch selesai
                        parser.resume();
                    }
                }).catch((error) => {
                    console.error('Error in batch processing:', error);
                    if (!stopProcess) {
                        parser.resume();
                    }
                });
            },
            complete: function() {
                console.log('Parsing CSV selesai');
                currentParser = null;
                finishImport();
            },
            error: function(error) {
                console.error('Error parsing CSV:', error);
                showError('Error parsing file CSV: ' + error.message);
            }
        });
    }

    // Fungsi untuk memproses batch dengan async/await
    async function processBatch(batch) {
        // Filter out empty rows
        const validBatch = batch.filter(row => 
            row && Object.keys(row).length > 0 && !Object.values(row).every(val => val === "" || val === null)
        );

        if (validBatch.length === 0) {
            processedData += batch.length; // Tetap hitung yang diproses
            updateUI();
            return;
        }

        try {
            const response = await $.ajax({
                url: '_Page/Wilayah/ProsesImport.php',
                type: 'POST',
                data: { 
                    batch: JSON.stringify(validBatch) 
                },
                dataType: 'json',
                timeout: 30000 // 30 detik timeout
            });

            // Update data setelah AJAX selesai
            processedData += batch.length; // Gunakan batch.length asli untuk progress
            
            // Update semua statistik dari response
            for (const key in response) {
                if (stats.hasOwnProperty(key)) {
                    if (Array.isArray(stats[key])) {
                        // Untuk array (error_details), gabungkan dan batasi jumlahnya
                        stats[key] = [...stats[key], ...(response[key] || [])].slice(-100);
                    } else {
                        stats[key] += response[key] || 0;
                    }
                }
            }
            
            updateUI();
            updateReportTable();
            
        } catch (error) {
            console.error('Error processing batch:', error);
            processedData += batch.length;
            stats.insert_district_failed += validBatch.length;
            
            let errorMsg = `Error AJAX: ${error.statusText || error.message}`;
            if (error.status === 0) {
                errorMsg = 'Error: Koneksi timeout atau terputus';
            } else if (error.status === 500) {
                errorMsg = 'Error: Server error (500)';
            }
            
            stats.error_details.push(errorMsg);
            updateUI();
            updateReportTable();
        }
    }

    // Fungsi ketika import selesai
    function finishImport() {
        if (!stopProcess) {
            $('#progressDetail').html('<strong class="text-success">Proses import selesai!</strong>');
        }
        $('#btnImport').prop('disabled', false);
        $('#btnStop').prop('disabled', true);
        $('#ResetFormImport').prop('disabled', false);
        
        // Final update
        processedData = totalData;
        updateUI();
        updateReportTable();
        
        // Tampilkan summary
        showSummary();
    }

    // Update statistik dari response
    function updateStats(newStats) {
        for (const key in newStats) {
            if (stats.hasOwnProperty(key)) {
                if (Array.isArray(stats[key])) {
                    stats[key] = stats[key].concat(newStats[key] || []);
                } else {
                    stats[key] += newStats[key] || 0;
                }
            }
        }
    }

    // Update UI Progress
    function updateUI() {
        let percent = 0;
        
        if (totalData > 0) {
            percent = Math.round((processedData / totalData) * 100);
            // Pastikan tidak melebihi 100% selama proses berjalan
            if (percent > 99 && currentParser) {
                percent = 99;
            }
        }
        
        percent = Math.min(100, Math.max(0, percent));
        
        $("#progressBar").css("width", percent + "%").attr("aria-valuenow", percent);
        $("#progressText").text(percent + "%");
        
        // Update detail progress
        let progressText = `${processedData} / ${totalData} data diproses`;
        if (processedData > 0 && totalData > 0) {
            const remaining = totalData - processedData;
            progressText += ` (${remaining} data tersisa)`;
        }
        $("#progressDetail").text(progressText);
    }

    // Update Report Table
    function updateReportTable() {
        const reportData = [
            // Validasi
            { no: 1, process: "Kode Provinsi (BPS) Kosong", status: "Validasi Gagal", count: stats.empty_province_code, type: "validation" },
            { no: 2, process: "Kode Provinsi (Dapodik) Kosong", status: "Validasi Gagal", count: stats.empty_province_code_dapodik, type: "validation" },
            { no: 3, process: "Nama Provinsi Kosong", status: "Validasi Gagal", count: stats.empty_province_name, type: "validation" },
            { no: 4, process: "Kode Kab/Kota (BPS) Kosong", status: "Validasi Gagal", count: stats.empty_district_code, type: "validation" },
            { no: 5, process: "Kode Kab/Kota (Dapodik) Kosong", status: "Validasi Gagal", count: stats.empty_district_code_dapodik, type: "validation" },
            { no: 6, process: "Nama Kab/Kota Kosong", status: "Validasi Gagal", count: stats.empty_district_name, type: "validation" },
            
            // Operasi Database Province
            { no: 7, process: "Insert Data Provinsi", status: "Berhasil", count: stats.insert_province_success, type: "success" },
            { no: 8, process: "Insert Data Provinsi", status: "Gagal", count: stats.insert_province_failed, type: "failed" },
            { no: 9, process: "Update Data Provinsi", status: "Berhasil", count: stats.update_province_success, type: "success" },
            { no: 10, process: "Update Data Provinsi", status: "Gagal", count: stats.update_province_failed, type: "failed" },
            
            // Operasi Database District
            { no: 11, process: "Insert Data Kab/Kota", status: "Berhasil", count: stats.insert_district_success, type: "success" },
            { no: 12, process: "Insert Data Kab/Kota", status: "Gagal", count: stats.insert_district_failed, type: "failed" },
            { no: 13, process: "Update Data Kab/Kota", status: "Berhasil", count: stats.update_district_success, type: "success" },
            { no: 14, process: "Update Data Kab/Kota", status: "Gagal", count: stats.update_district_failed, type: "failed" }
        ];
        let tableBody = "";
        let hasData = false;
        
        reportData.forEach(item => {
            if (item.count > 0) {
                hasData = true;
                let rowClass = "";
                let statusClass = "";
                
                switch(item.type) {
                    case "success":
                        rowClass = "table-success";
                        statusClass = "text-success";
                        break;
                    case "failed":
                        rowClass = "table-danger";
                        statusClass = "text-danger";
                        break;
                    case "validation":
                        rowClass = "table-secondary";
                        statusClass = "text-secondary";
                        break;
                }
                
                tableBody += `
                    <tr class="${rowClass}">
                        <td class="text-center">${item.no}</td>
                        <td>${item.process}</td>
                        <td class="${statusClass} fw-bold text-center">${item.status}</td>
                        <td class="text-center fw-bold">${item.count.toLocaleString()}</td>
                    </tr>
                `;
            }
        });

        if (!hasData) {
            tableBody = `
                <tr>
                    <td colspan="4" class="text-center text-muted py-2">
                        <i class="bi bi-hourglass-split"></i> Belum ada data yang diproses
                    </td>
                </tr>
            `;
        }

        $("#reportTableBody").html(tableBody);
        $("#totalProcessed").text(processedData.toLocaleString());

        // Tampilkan detail error jika ada
        if (stats.error_details.length > 0) {
            $("#errorDetailsSection").show();
            let errorBody = "";
            const recentErrors = stats.error_details.slice(-10);
            
            recentErrors.forEach((error, index) => {
                errorBody += `
                    <tr>
                        <td class="text-center">${index + 1}</td>
                        <td>Error Proses</td>
                        <td class="text-danger small">${error}</td>
                    </tr>
                `;
            });
            
            if (stats.error_details.length > 10) {
                errorBody += `
                    <tr>
                        <td colspan="3" class="text-center text-muted small">
                            ... dan ${stats.error_details.length - 10} error lainnya
                        </td>
                    </tr>
                `;
            }
            
            $("#errorDetailsBody").html(errorBody);
        } else {
            $("#errorDetailsSection").hide();
        }
    }

    // Tampilkan summary setelah selesai
    function showSummary() {
        const totalSuccess = stats.insert_province_success + stats.update_district_success + stats.insert_district_success;
        const totalProcessed = stats.empty_province_bps + stats.empty_province_dapodik + stats.empty_province_name +
                            stats.empty_district_bps + stats.empty_district_dapodik + stats.empty_district_name +
                            stats.insert_province_success + stats.insert_province_failed +
                            stats.update_district_success + stats.update_district_failed +
                            stats.insert_district_success + stats.insert_district_failed;
        
        const successRate = totalProcessed > 0 ? Math.round((totalSuccess / totalProcessed) * 100) : 0;
        
        let summaryClass = "text-success";
        if (successRate < 50) summaryClass = "text-danger";
        else if (successRate < 80) summaryClass = "text-warning";
        
        if (!stopProcess) {
            $("#progressDetail").html(`
                <strong class="${summaryClass}">
                    <i class="bi bi-check-circle"></i> Proses selesai! 
                    Success Rate: ${successRate}% (${totalSuccess}/${totalProcessed} data berhasil)
                </strong>
            `);
        }
    }

    // Reset Counter
    function resetCounter() {
        stopProcess = false;
        totalData = 0;
        processedData = 0;
        currentParser = null;
        
        // Reset semua statistik
        for (const key in stats) {
            if (Array.isArray(stats[key])) {
                stats[key] = [];
            } else {
                stats[key] = 0;
            }
        }
        
        // Reset UI
        $("#progressBar").css("width", "0%").attr("aria-valuenow", 0);
        $("#progressText").text("0%");
        $("#progressDetail").text("Memproses data...");
        $("#reportTableBody").html(`
            <tr>
                <td colspan="4" class="text-center text-muted py-2">
                    <i class="bi bi-hourglass-split"></i> Belum ada data yang diproses
                </td>
            </tr>
        `);
        $("#totalProcessed").text("0");
        $("#errorDetailsSection").hide();
    }

    // Show Error
    function showError(message) {
        $('#progressDetail').html('<strong class="text-danger">' + message + '</strong>');
        $('#btnImport').prop('disabled', false);
        $('#btnStop').prop('disabled', true);
        $('#ResetFormImport').prop('disabled', false);
        stats.error_details.push(message);
        updateReportTable();
    }
});