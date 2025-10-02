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

    //Jika Reset Form Import
    $('#ResetFormImport').click(function(){

        //Reset Form Import
        $("#ProsesImport")[0].reset();

        //Kosongkan Table
        $('#NotifikasiImport').html('<tr><td colspan="4" class="text-center"><small class="text-danger">Belum Ada Proses Import</small></td></tr>');

        //Disable Button
        $('#ResetFormImport').prop('disabled', true);
    });

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
            url: '_Page/AbkPerSekolah/ProsesImport.php',
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
            url: '_Page/AbkPerSekolah/ProsesImportBatch.php',
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