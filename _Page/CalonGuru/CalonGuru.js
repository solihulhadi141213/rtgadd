//Fungsi Menampilkan Data
function filterAndLoadTable() {
    var ProsesFilter = $('#ProsesFilter').serialize();

    // Simpan posisi scroll saat ini
    var currentScroll = $(window).scrollTop();

    // Efek transisi: fadeOut lembut
    $('#TabelCalonGuru').fadeTo(400, 0.3, function () {
        $.ajax({
            type    : 'POST',
            url     : '_Page/CalonGuru/TabelCalonGuru.php',
            data    : ProsesFilter,
            success : function(data) {
                // Ganti konten
                $('#TabelCalonGuru').html(data);

                // Efek transisi fadeIn lembut
                $('#TabelCalonGuru').fadeTo(400, 1);

                // Kembalikan posisi scroll agar layar tidak bergerak
                $(window).scrollTop(currentScroll);
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
            url 	    : '_Page/CalonGuru/FormFilter.php',
            data        : {KeywordBy: KeywordBy},
            success     : function(data){
                $('#FormFilter').html(data);
            }
        });
    });

    //Modal Detail
    $('#ModalDetail').on('show.bs.modal', function (e) {
        var id_calon_guru = $(e.relatedTarget).data('id');
        $('#FormDetail').html("Loading...");
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/CalonGuru/FormDetail.php',
            data        : {id_calon_guru: id_calon_guru},
            success     : function(data){
                $('#FormDetail').html(data);
            }
        });
    });

    //Modal Hapus
    $('#ModalHapus').on('show.bs.modal', function (e) {
        var id_calon_guru = $(e.relatedTarget).data('id');
        $('#FormHapusJabatan').html("Loading...");
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/CalonGuru/FormHapus.php',
            data        : {id_calon_guru: id_calon_guru},
            success     : function(data){
                $('#FormHapus').html(data);
                $('#NotifikasiHapus').html('');
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
            url 	    : '_Page/CalonGuru/ProsesHapus.php',
            data 	    :  data,
            cache       : false,
            processData : false,
            contentType : false,
            enctype     : 'multipart/form-data',
            success     : function(data){
                $('#NotifikasiHapus').html(data);
                var NotifikasiHapusBerhasil=$('#NotifikasiHapusBerhasil').html();
                if(NotifikasiHapusBerhasil=="Berhasil"){
                    $('#NotifikasiHapus').html('');

                    //Tutup Modal
                    $('#ModalHapus').modal('hide');

                    //Tampilkan Swal
                     Swal.fire(
                        'Success!',
                        'Hapus Daftar PPG Calon Guru Berhasil!',
                        'success'
                    )
                    //Menampilkan Data
                    filterAndLoadTable();
                }
            }
        });
    });

    // INIT VAR
    let stopProcess = false;
    let totalData = 0;
    let processedData = 0;
    let currentParser = null;

    // Object untuk menyimpan semua statistik
    let stats = {
        // Data validation
        empty_province_data: 0,
       
        // Insert operations
        kode_province_tidak_ada: 0,
        kode_district_tidak_ada: 0,
       
        // Main data operations
        update_success: 0,
        update_failed: 0,
        insert_success: 0,
        insert_failed: 0,
        
        // Error details
        error_details: []
    };

    $("#ProsesImportCsv").on("submit", function(e) {
        e.preventDefault();
        let file = $("#data_ppg_calon_guru_lulusan")[0].files[0];
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
                delimiter: ";", // TAMBAHKAN INI JUGA
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
            delimiter: ";", // TAMBAHKAN INI - gunakan titik koma sebagai delimiter
            chunkSize: 200,
            chunk: function(results, parser) {
                if (stopProcess) {
                    parser.abort();
                    $("#progressDetail").text("Proses dihentikan!");
                    return;
                }

                currentParser = parser;
                let batch = results.data;
                
                // DEBUG: Lihat struktur data batch pertama
                if (processedData === 0 && batch.length > 0) {
                    console.log("Struktur data pertama setelah parsing:", batch[0]);
                    console.log("ppg_blm_diangkat value:", batch[0].ppg_blm_diangkat);
                }
                
                parser.pause();

                processBatch(batch).then(() => {
                    if (!stopProcess) {
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
                url: "_Page/CalonGuru/ProsesImportCsv.php",
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
            // Insert Province
            { no: 2, process: "Kode Provinsi Tidak Valid", status: "Gagal", count: stats.kode_province_tidak_ada, type: "failed" },
            { no: 3, process: "Kode Kab/Kota Tidak Valid", status: "Gagal", count: stats.kode_district_tidak_ada, type: "failed" },
            
            // Main Data Operations
            { no: 4, process: "Update Data Utama", status: "Berhasil", count: stats.update_success, type: "success" },
            { no: 5, process: "Update Data Utama", status: "Gagal", count: stats.update_failed, type: "failed" },
            { no: 6, process: "Insert Data Utama", status: "Berhasil", count: stats.insert_success, type: "success" },
            { no: 7, process: "Insert Data Utama", status: "Gagal", count: stats.insert_failed, type: "failed" },
            
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
        $("#data_ppg_calon_guru_lulusan").val("");
    });

    
    
});