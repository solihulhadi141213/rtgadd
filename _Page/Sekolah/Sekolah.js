//Fungsi Menampilkan Data
function filterAndLoadTable() {
    var ProsesFilter = $('#ProsesFilter').serialize();

    // Efek transisi: fadeOut dulu
    $('#TabelSekolah').fadeOut(200, function () {
        $.ajax({
            type    : 'POST',
            url     : '_Page/Sekolah/TabelSekolah.php',
            data    : ProsesFilter,
            success : function(data) {
                $('#TabelSekolah').html(data);

                //Uncheck checkbox utama
                $('input[name="check_all"]').prop('checked', false);

                // Setelah ganti konten → fadeIn lagi
                $('#TabelSekolah').fadeIn(200);
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

    //Submit Filter Data
    $('#ProsesFilter').submit(function(){

        //Reset Halaman Ke halaman 1
        $('#page').val("1");

        //Tampilkan Ulang Data
        filterAndLoadTable();

        //Tutup Modal Filter
        $('#ModalFilter').modal('hide');
    });

    //Ketika KeywordBy Diubah
    $('#KeywordBy').change(function(){
        var KeywordBy = $('#KeywordBy').val();
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Sekolah/FormFilter.php',
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
            url 	    : '_Page/Sekolah/FormSelectDistrict.php',
            data        : {province_code: province_code},
            success     : function(data){
                $('#district_code').html(data);
            }
        });
    });

    //Proses Tambah
    $('#ProsesTambah').submit(function(){

        //Loading Notifikasi
        $('#NotifikasiTambah').html('<div class="spinner-border text-secondary" role="status"><span class="sr-only"></span></div>');

        //Tangkap Data
        var form = $('#ProsesTambah')[0];
        var data = new FormData(form);
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Sekolah/ProsesTambah.php',
            data 	    :  data,
            cache       : false,
            processData : false,
            contentType : false,
            enctype     : 'multipart/form-data',
            success     : function(data){
                $('#NotifikasiTambah').html(data);
                var NotifikasiTambahBerhasil=$('#NotifikasiTambahBerhasil').html();

                //Jika Berhasil
                if(NotifikasiTambahBerhasil=="Berhasil"){
                   //Tutup Modal
                    $('#ModalTambah').modal('hide');

                    //Menampilkan Data
                    filterAndLoadTable();
                    Swal.fire(
                        'Success!',
                        'Tambah Referensi Sekolah Berhasil!',
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
        var id_school = $(e.relatedTarget).data('id');
        $('#FormDetail').html("Loading...");
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Sekolah/FormDetail.php',
            data        : {id_school: id_school},
            success     : function(data){
                $('#FormDetail').html(data);
            }
        });
    });

    //Modal Edit
    $('#ModalEdit').on('show.bs.modal', function (e) {
        var id_school = $(e.relatedTarget).data('id');
        $('#FormEdit').html("Loading...");
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Sekolah/FormEdit.php',
            data        : {id_school: id_school},
            success     : function(data){
                $('#FormEdit').html(data);

                //Kosongkan Notifikasi
                $('#NotifikasiEdit').html('');

                //Enable tombol
                $('#ButtonEdit').prop('disabled', false);
            }
        });
    });

    //Ketika memilih province_code_edit
    $(document).on('change', '#province_code_edit', function() {
        var province_code = $('#province_code_edit').val();
        
        //Reload Data Kab/Kota dengan ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Sekolah/FormSelectDistrict.php',
            data        : {province_code: province_code},
            success     : function(data){
                $('#district_code_edit').html(data);
            }
        });
    });

    //Proses Edit
    $('#ProsesEdit').submit(function(){
        //Loading Notifikasi
        $('#NotifikasiEdit').html('<div class="spinner-border text-secondary" role="status"><span class="sr-only"></span></div>');

        //Tangkap Data
        var form = $('#ProsesEdit')[0];
        var data = new FormData(form);

        //Kirim Data Ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Sekolah/ProsesEdit.php',
            data 	    :  data,
            cache       : false,
            processData : false,
            contentType : false,
            enctype     : 'multipart/form-data',
            success     : function(data){
                $('#NotifikasiEdit').html(data);

                //Tangkap Notifikasi Proses
                var NotifikasiEditBerhasil=$('#NotifikasiEditBerhasil').html();
                if(NotifikasiEditBerhasil=="Berhasil"){

                    //Kosongkan Notifikasi
                    $('#NotifikasiEdit').html('');

                    //Tutup Modal
                    $('#ModalEdit').modal('hide');
                    Swal.fire(
                        'Success!',
                        'Ubah Sekolah Berhasil!',
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
        var id_school = $(e.relatedTarget).data('id');
        $('#FormHapus').html("Loading...");
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Sekolah/FormHapus.php',
            data        : {id_school: id_school},
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
            url 	    : '_Page/Sekolah/ProsesHapus.php',
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
                        'Hapus Sekolah Berhasil!',
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
            url 	    : '_Page/Sekolah/FormExport.php',
            success     : function(data){
                $('#FormExport').html(data);
            }
        });
    });

    //Jika Reset Form Import
    $('#ResetFormImport').click(function(){

        //Reset Form Import
        $("#ProsesImport")[0].reset();

        //Kosongkan Table
        $('#NotifikasiImport').html('<tr><td colspan="6" class="text-center"><small class="text-danger">Belum Ada Proses Import</small></td></tr>');

        //Disable Button
        $('#ResetFormImport').prop('disabled', true);
    });

    //Proses Import
    //Proses Import
    $('#ProsesImport').submit(function(){
        $('#NotifikasiImport').html('<tr><td colspan="6" class="text-center"><small class="text-danger">Loading...</small></td></tr>');
        $('#progressSection').show();
        $('#btnImport').prop('disabled', true);
        
        var form = $('#ProsesImport')[0];
        var data = new FormData(form);
        
        console.log('Mengirim request import...');
        
        $.ajax({
            type: 'POST',
            url: '_Page/Sekolah/ProsesImport.php',
            data: data,
            cache: false,
            processData: false,
            contentType: false,
            enctype: 'multipart/form-data',
            success: function(response){
                console.log('Response received:', response);
                
                try {
                    var result = JSON.parse(response);
                    console.log('Parsed result:', result);
                    
                    if (result.status === 'success') {
                        if (result.total_batches > 1) {
                            console.log('Memulai proses batch...');
                            // Jika data besar, proses secara batch
                            processBatchImport(result.file_token, 1, result.total_batches, result.total_rows);
                        } else {
                            console.log('Proses single batch selesai');
                            // Jika data kecil, langsung tampilkan hasil
                            $('#NotifikasiImport').html(result.html);
                            $('#ResetFormImport').prop('disabled', false);
                            $('#btnImport').prop('disabled', false);
                            $('#progressSection').hide();
                            $("#ProsesFilter")[0].reset();
                            filterAndLoadTable();
                        }
                    } else {
                        console.log('Error:', result.message);
                        $('#NotifikasiImport').html('<tr><td colspan="6" class="text-center"><small class="text-danger">' + result.message + '</small></td></tr>');
                        $('#btnImport').prop('disabled', false);
                        $('#progressSection').hide();
                    }
                } catch (e) {
                    console.log('Error parsing JSON:', e);
                    console.log('Raw response:', response);
                    // Fallback untuk response non-JSON (error)
                    $('#NotifikasiImport').html(response);
                    $('#btnImport').prop('disabled', false);
                    $('#progressSection').hide();
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error:', error);
                $('#NotifikasiImport').html('<tr><td colspan="6" class="text-center"><small class="text-danger">Error: ' + error + '</small></td></tr>');
                $('#btnImport').prop('disabled', false);
                $('#progressSection').hide();
            }
        });
    });

    // Fungsi untuk proses batch
    function processBatchImport(fileToken, currentBatch, totalBatches, totalRows) {
        console.log('Processing batch:', currentBatch, 'of', totalBatches);
        console.log('File token:', fileToken);
        
        var progressPercentage = Math.round((currentBatch / totalBatches) * 100);
        $('#progressBar').css('width', progressPercentage + '%');
        $('#progressText').text(progressPercentage + '%');
        $('#progressDetail').text('Memproses batch ' + currentBatch + ' dari ' + totalBatches + ' (' + totalRows + ' total data)');
        
        $.ajax({
            type: 'POST',
            url: '_Page/Sekolah/ProsesImportBatch.php',
            data: {
                file_token: fileToken,
                batch: currentBatch,
                total_batches: totalBatches
            },
            success: function(response) {
                console.log('Batch response:', response);
                
                try {
                    var result = JSON.parse(response);
                    
                    if (result.status === 'success') {
                        // Tampilkan hasil batch saat ini
                        $('#NotifikasiImport').html(result.html);
                        
                        if (currentBatch < totalBatches) {
                            // Lanjut ke batch berikutnya
                            setTimeout(function() {
                                processBatchImport(fileToken, currentBatch + 1, totalBatches, totalRows);
                            }, 500); // Delay 0.5 detik antara batch
                        } else {
                            // Semua batch selesai
                            $('#NotifikasiImport').append('<tr><td colspan="6" class="text-center"><small class="text-success"><b>✅ SEMUA DATA BERHASIL DIPROSES</b></small></td></tr>');
                            $('#ResetFormImport').prop('disabled', false);
                            $('#btnImport').prop('disabled', false);
                            $('#progressSection').hide();
                            $("#ProsesFilter")[0].reset();
                            filterAndLoadTable();
                        }
                    } else {
                        $('#NotifikasiImport').html('<tr><td colspan="6" class="text-center"><small class="text-danger">Error pada batch ' + currentBatch + ': ' + result.message + '</small></td></tr>');
                        $('#btnImport').prop('disabled', false);
                        $('#progressSection').hide();
                    }
                } catch (e) {
                    console.log('Error parsing batch response:', e);
                    $('#NotifikasiImport').html('<tr><td colspan="6" class="text-center"><small class="text-danger">Error parsing response batch ' + currentBatch + '</small></td></tr>');
                    $('#btnImport').prop('disabled', false);
                    $('#progressSection').hide();
                }
            },
            error: function(xhr, status, error) {
                console.log('Batch AJAX Error:', error);
                $('#NotifikasiImport').html('<tr><td colspan="6" class="text-center"><small class="text-danger">Error pada batch ' + currentBatch + ': ' + error + '</small></td></tr>');
                $('#btnImport').prop('disabled', false);
                $('#progressSection').hide();
            }
        });
    }

    // Reset form
    $('#ResetFormImport').click(function(){
        $('#ProsesImport')[0].reset();
        $('#NotifikasiImport').html('<tr><td colspan="6" class="text-center"><small class="text-danger">Belum Ada Proses Import</small></td></tr>');
        $('#progressSection').hide();
        $(this).prop('disabled', true);
    });
    
});