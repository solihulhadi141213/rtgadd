//Fungsi Menampilkan Data
function filterAndLoadTable() {
    var ProsesFilter = $('#ProsesFilter').serialize();

    // Efek transisi: fadeOut dulu
    $('#TabelAbkPerSekolah').fadeOut(200, function () {
        $.ajax({
            type    : 'POST',
            url     : '_Page/AbkPerSekolah/TabelAbkPerSekolah.php',
            data    : ProsesFilter,
            success : function(data) {
                $('#TabelAbkPerSekolah').html(data);

                // Setelah ganti konten → fadeIn lagi
                $('#TabelAbkPerSekolah').fadeIn(200);
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
            url 	    : '_Page/AbkPerSekolah/FormFilter.php',
            data        : {KeywordBy: KeywordBy},
            success     : function(data){
                $('#FormFilter').html(data);
            }
        });
    });

    //Ketika memilih province_code
    $('#province_code').change(function(){
        var province_code = $('#province_code').val();
        
        //Reload Data Kab/Kota dengan ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/AbkPerSekolah/FormSelectDistrict.php',
            data        : {province_code: province_code},
            success     : function(data){
                $('#district_code').html(data);
            }
        });
    });

    //Ketika memilih district_code
    $('#district_code').change(function(){
        var district_code = $('#district_code').val();
        $('#npsn').html('<option value="">Loading..</option>');
        //Reload Data Kab/Kota dengan ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/AbkPerSekolah/FormSelectNpsn.php',
            data        : {district_code: district_code},
            success     : function(data){
                $('#npsn').html(data);
            }
        });
    });

    //Proses Tambah
    $('#ProsesTambah').submit(function(){
        $('#NotifikasiTambah').html('<div class="spinner-border text-secondary" role="status"><span class="sr-only"></span></div>');
        var form = $('#ProsesTambah')[0];
        var data = new FormData(form);
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/AbkPerSekolah/ProsesTambah.php',
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

                    //Tampilkan swal
                    Swal.fire(
                        'Success!',
                        'Tambah ABK Per Sekolah Berhasil!',
                        'success'
                    );

                    //Reset Form
                    $("#ProsesTambah")[0].reset();

                    //Kosongkan Notifikasi
                    $('#NotifikasiTambah').html("");
                }
            }
        });
    });

    //Modal Detail
    $('#ModalDetail').on('show.bs.modal', function (e) {
        var id_position_school = $(e.relatedTarget).data('id');
        $('#FormDetail').html("Loading...");
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/AbkPerSekolah/FormDetail.php',
            data        : {id_position_school: id_position_school},
            success     : function(data){
                $('#FormDetail').html(data);
            }
        });
    });

    //Modal Edit
    $('#ModalEdit').on('show.bs.modal', function (e) {
        var id_position_school = $(e.relatedTarget).data('id');
        $('#FormEdit').html("Loading...");
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/AbkPerSekolah/FormEdit.php',
            data        : {id_position_school: id_position_school},
            success     : function(data){
                $('#FormEdit').html(data);
                $('#NotifikasiEdit').html('');

                //Enable tombol
                $('#ButtonEdit').prop('disabled', false);
            }
        });
    });

    //Ketika memilih province_code_edit
    $('#ModalEdit').on('change', '#province_code_edit', function(){
        var province_code_edit = $('#province_code_edit').val();
        
        //Reload Data Kab/Kota dengan ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/AbkPerSekolah/FormSelectDistrict.php',
            data        : {province_code: province_code_edit},
            success     : function(data){
                $('#district_code_edit').html(data);
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
            url 	    : '_Page/AbkPerSekolah/ProsesEdit.php',
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
                        'Ubah ABK Per Sekolah Berhasil!',
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
        var id_position_school = $(e.relatedTarget).data('id');
        $('#FormHapus').html("Loading...");
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/AbkPerSekolah/FormHapus.php',
            data        : {id_position_school: id_position_school},
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
            url 	    : '_Page/AbkPerSekolah/ProsesHapus.php',
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
                        'Hapus ABK Per Sekolah Berhasil!',
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
            url 	    : '_Page/AbkPerSekolah/FormExport.php',
            success     : function(data){
                $('#FormExport').html(data);

            }
        });
    });





    // INIT VAR
    // INIT VAR
    let stopProcess = false;
    let totalData = 0;
    let processedData = 0;
    let currentParser = null;

    // Object untuk menyimpan semua statistik
    let stats = {
        // Data validation
        empty_province_data: 0,
        empty_school_data: 0,
        empty_position_data: 0,
        
        // Insert operations
        insert_province_success: 0,
        insert_province_failed: 0,
        insert_district_success: 0,
        insert_district_failed: 0,
        insert_school_success: 0,
        insert_school_failed: 0,
        insert_position_success: 0,
        insert_position_failed: 0,
        
        // Main data operations
        registered_data: 0,
        update_success: 0,
        update_failed: 0,
        insert_success: 0,
        insert_failed: 0,
        
        // Error details
        error_details: []
    };

    $("#ProsesImportCsv").on("submit", function(e) {
        e.preventDefault();
        let file = $("#data_akb_per_sekolah_csv")[0].files[0];
        if (!file) return alert("Pilih file CSV terlebih dahulu!");

        resetCounter();
        $("#progressSection").show();
        $("#BtnStoProccess").prop("disabled", false);
        $("#btnImportCsv").prop("disabled", true);
        $("#ResetFormImportCsv").prop("disabled", true);

        // HITUNG TOTAL DATA DENGAN CARA LAIN
        countTotalRows(file).then(totalRows => {
            totalData = totalRows;
            console.log("Total data yang akan diproses:", totalData);
            updateUI();
            
            // Mulai parsing setelah mengetahui total data
            startParsing(file);
        }).catch(error => {
            console.error("Error counting rows:", error);
            // Fallback: gunakan estimasi
            totalData = 1000; // Nilai default
            updateUI();
            startParsing(file);
        });
    });

    // Fungsi untuk menghitung total baris dalam file CSV
    function countTotalRows(file) {
        return new Promise((resolve, reject) => {
            let rowCount = 0;
            
            Papa.parse(file, {
                header: true,
                skipEmptyLines: true,
                chunkSize: 1000,
                step: function(results, parser) {
                    rowCount++;
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

    // Fungsi untuk memulai parsing yang sebenarnya
    function startParsing(file) {
        Papa.parse(file, {
            header: true,
            skipEmptyLines: true,
            chunkSize: 200, // proses per 200 baris
            chunk: function(results, parser) {
                if (stopProcess) {
                    parser.abort();
                    $("#progressDetail").text("Proses dihentikan!");
                    return;
                }

                currentParser = parser;
                let batch = results.data;
                
                // Pause parser sementara menunggu AJAX selesai
                parser.pause();

                // Proses batch secara synchronous
                processBatch(batch).then(() => {
                    if (!stopProcess) {
                        // Lanjutkan parsing setelah batch selesai
                        parser.resume();
                    }
                }).catch((error) => {
                    console.error("Error in batch processing:", error);
                    if (!stopProcess) {
                        parser.resume();
                    }
                });
            },
            complete: function() {
                $("#progressDetail").text("Proses selesai.");
                $("#BtnStoProccess").prop("disabled", true);
                $("#btnImportCsv").prop("disabled", false);
                $("#ResetFormImportCsv").prop("disabled", false);
                currentParser = null;
                
                // Final update - pastikan 100%
                processedData = totalData;
                updateUI();
                updateReportTable();
            },
            error: function(error) {
                console.error("Error parsing CSV:", error);
                $("#progressDetail").text("Error parsing file CSV!");
                $("#BtnStoProccess").prop("disabled", true);
                $("#btnImportCsv").prop("disabled", false);
                $("#ResetFormImportCsv").prop("disabled", false);
            }
        });
    }

    // Fungsi untuk memproses batch dengan async/await
    async function processBatch(batch) {
        try {
            const response = await $.ajax({
                url: "_Page/AbkPerSekolah/ProsesImportCsv.php",
                type: "POST",
                data: { 
                    batch: JSON.stringify(batch) 
                },
                dataType: "json"
            });

            // Update data setelah AJAX selesai
            processedData += batch.length;
            
            // Update semua statistik dari response
            for (const key in response) {
                if (stats.hasOwnProperty(key)) {
                    if (Array.isArray(stats[key])) {
                        // Untuk array (error_details), gabungkan
                        stats[key] = stats[key].concat(response[key] || []);
                    } else {
                        stats[key] += response[key] || 0;
                    }
                }
            }
            
            updateUI();
            updateReportTable();
            
        } catch (error) {
            console.error("Error processing batch:", error);
            processedData += batch.length;
            stats.insert_failed += batch.length;
            stats.error_details.push(`Error AJAX: ${error.message}`);
            updateUI();
            updateReportTable();
        }
    }

    // UPDATE UI Progress
    function updateUI() {
        let percent = 0;
        
        if (totalData > 0) {
            percent = Math.round((processedData / totalData) * 100);
            // Pastikan tidak melebihi 100% selama proses berjalan
            if (percent > 100 && currentParser) {
                percent = 99; // Tetap 99% sampai complete
            }
        }
        
        percent = Math.min(100, Math.max(0, percent));
        
        $("#progressBar").css("width", percent + "%").attr("aria-valuenow", percent);
        $("#progressText").text(percent + "%");
        $("#progressDetail").text(processedData + " / " + totalData + " data diproses");
    }

    // UPDATE REPORT TABLE
    function updateReportTable() {
        const reportData = [
            // Data Validation
            { no: 1, process: "Data Provinsi Kosong", status: "Validasi Gagal", count: stats.empty_province_data, type: "validation" },
            { no: 2, process: "Data Sekolah Kosong", status: "Validasi Gagal", count: stats.empty_school_data, type: "validation" },
            { no: 3, process: "Data Jabatan Kosong", status: "Validasi Gagal", count: stats.empty_position_data, type: "validation" },
            
            // Insert Province
            { no: 4, process: "Insert Data Provinsi", status: "Berhasil", count: stats.insert_province_success, type: "success" },
            { no: 5, process: "Insert Data Provinsi", status: "Gagal", count: stats.insert_province_failed, type: "failed" },
            
            // Insert District
            { no: 6, process: "Insert Data Kab/Kota", status: "Berhasil", count: stats.insert_district_success, type: "success" },
            { no: 7, process: "Insert Data Kab/Kota", status: "Gagal", count: stats.insert_district_failed, type: "failed" },
            
            // Insert School
            { no: 8, process: "Insert Data Sekolah", status: "Berhasil", count: stats.insert_school_success, type: "success" },
            { no: 9, process: "Insert Data Sekolah", status: "Gagal", count: stats.insert_school_failed, type: "failed" },
            
            // Insert Position
            { no: 10, process: "Insert Data Jabatan", status: "Berhasil", count: stats.insert_position_success, type: "success" },
            { no: 11, process: "Insert Data Jabatan", status: "Gagal", count: stats.insert_position_failed, type: "failed" },
            
            // Main Data Operations
            { no: 12, process: "Update Data Utama", status: "Berhasil", count: stats.update_success, type: "success" },
            { no: 13, process: "Update Data Utama", status: "Gagal", count: stats.update_failed, type: "failed" },
            { no: 14, process: "Insert Data Utama", status: "Berhasil", count: stats.insert_success, type: "success" },
            { no: 15, process: "Insert Data Utama", status: "Gagal", count: stats.insert_failed, type: "failed" },
            
            // Already Registered
            { no: 16, process: "Data Sudah Terdaftar", status: "Update", count: stats.registered_data, type: "warning" }
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
                    case "warning":
                        rowClass = "table-warning";
                        statusClass = "text-warning";
                        break;
                    case "validation":
                        rowClass = "table-secondary";
                        statusClass = "text-secondary";
                        break;
                }
                
                tableBody += `
                    <tr class="${rowClass}">
                        <td>${item.no}</td>
                        <td>${item.process}</td>
                        <td class="${statusClass} fw-bold">${item.status}</td>
                        <td class="text-center">${item.count}</td>
                    </tr>
                `;
            }
        });

        // Jika belum ada data, tampilkan pesan
        if (!hasData) {
            tableBody = `
                <tr>
                    <td colspan="4" class="text-center text-muted">
                        <i>Belum ada data yang diproses</i>
                    </td>
                </tr>
            `;
        }

        $("#reportTableBody").html(tableBody);
        $("#totalProcessed").text(processedData);

        // Tampilkan detail error jika ada
        if (stats.error_details.length > 0) {
            $("#errorDetailsSection").show();
            let errorBody = "";
            stats.error_details.forEach((error, index) => {
                errorBody += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>Error Proses</td>
                        <td class="text-danger">${error}</td>
                    </tr>
                `;
            });
            $("#errorDetailsBody").html(errorBody);
        } else {
            $("#errorDetailsSection").hide();
        }
    }

    // RESET COUNTER
    function resetCounter() {
        stopProcess = false;
        totalData = 0;
        processedData = 0;
        
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
                <td colspan="4" class="text-center text-muted">
                    <i>Belum ada data yang diproses</i>
                </td>
            </tr>
        `);
        $("#totalProcessed").text("0");
        $("#errorDetailsSection").hide();
    }

    $("#BtnStoProccess").on("click", function() {
        stopProcess = true;
        $(this).prop("disabled", true);
        
        if (currentParser) {
            currentParser.abort();
        }
        
        $("#progressDetail").text("Proses dihentikan!");
        $("#btnImportCsv").prop("disabled", false);
        $("#ResetFormImportCsv").prop("disabled", false);
    });

    $("#ResetFormImportCsv").on("click", function() {
        resetCounter();
        $("#progressSection").hide();
        $("#btnImportCsv").prop("disabled", false);
        $("#BtnStoProccess").prop("disabled", true);
        $(this).prop("disabled", true);
        $("#data_akb_per_sekolah_csv").val("");
    });

    
    
});