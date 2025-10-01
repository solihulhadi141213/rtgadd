//Fungsi Menampilkan Data
function filterAndLoadTableJabatan() {
    var ProsesFilter = $('#ProsesFilter').serialize();

    // Efek transisi: fadeOut dulu
    $('#TabelJabatanPerWilayah').fadeOut(200, function () {
        $.ajax({
            type    : 'POST',
            url     : '_Page/JabatanPerWilayah/TabelJabatanPerWilayah.php',
            data    : ProsesFilter,
            success : function(data) {
                $('#TabelJabatanPerWilayah').html(data);

                //Uncheck checkbox utama
                $('input[name="check_all"]').prop('checked', false);

                // Setelah ganti konten → fadeIn lagi
                $('#TabelJabatanPerWilayah').fadeIn(200);
            }
        });
    });
}

//Fungsi Cek Duplikasi Data
function CekDuplikasi() {
    var ProsesTambahJabatan = $('#ProsesTambahJabatan').serialize();
    $('#NotifikasiTambahJabatan').html('Loading...');
    
    //Menampilkan Hasil Pada 'NotifikasiTambahJabatan'
    $.ajax({
        type    : 'POST',
        url     : '_Page/JabatanPerWilayah/CekDuplikasi.php',
        data    : ProsesTambahJabatan,
        success : function(data) {
            $('#NotifikasiTambahJabatan').html(data);
        }
    });
}

//Menampilkan Data Pertama Kali
$(document).ready(function() {
    filterAndLoadTableJabatan();

    //Pagging
    $(document).on('click', '#next_button', function() {
        var page_now = parseInt($('#page').val(), 10); // Pastikan nilai diambil sebagai angka
        var next_page = page_now + 1;
        $('#page').val(next_page);
        filterAndLoadTableJabatan(0);
    });
    $(document).on('click', '#prev_button', function() {
        var page_now = parseInt($('#page').val(), 10); // Pastikan nilai diambil sebagai angka
        var next_page = page_now - 1;
        $('#page').val(next_page);
        filterAndLoadTableJabatan(0);
    });

    //Filter Data
    $('#ProsesFilter').submit(function(){
        $('#page').val("1");
        filterAndLoadTableJabatan();
        $('#ModalFilterJabatan').modal('hide');
    });

    //Ketika KeywordBy Diubah
    $('#KeywordBy').change(function(){
        var KeywordBy = $('#KeywordBy').val();
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/JabatanPerWilayah/FormFilter.php',
            data        : {KeywordBy: KeywordBy},
            success     : function(data){
                $('#FormFilter').html(data);
            }
        });
    });

    //Ketika Modal Tambah Fitur Muncul
    $('#ModalTambahJabatan').on('show.bs.modal', function (e) {
        $('#NotifikasiTambahJabatan').html('');
    });

    //Ketika memilih province_code
    $('#province_code').change(function(){
        var province_code = $('#province_code').val();
        
        //Reload Data Kab/Kota dengan ajax
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/JabatanPerWilayah/FormSelectDistrict.php',
            data        : {province_code: province_code},
            success     : function(data){
                $('#district_code').html(data);
            }
        });

        CekDuplikasi();
    });

    $('#district_code').change(function(){
        CekDuplikasi();
    });
    $('#id_position').change(function(){
        CekDuplikasi();
    });

    //Proses Tambah Jabatan
    $('#ProsesTambahJabatan').submit(function(){
        $('#NotifikasiTambahJabatan').html('<div class="spinner-border text-secondary" role="status"><span class="sr-only"></span></div>');
        var form = $('#ProsesTambahJabatan')[0];
        var data = new FormData(form);
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/JabatanPerWilayah/ProsesTambahJabatan.php',
            data 	    :  data,
            cache       : false,
            processData : false,
            contentType : false,
            enctype     : 'multipart/form-data',
            success     : function(data){
                $('#NotifikasiTambahJabatan').html(data);
                var NotifikasiTambahJabatanBerhasil=$('#NotifikasiTambahJabatanBerhasil').html();
                if(NotifikasiTambahJabatanBerhasil=="Success"){
                   //Tutup Modal
                    $('#ModalTambahJabatan').modal('hide');

                    //Menampilkan Data
                    filterAndLoadTableJabatan();
                    Swal.fire(
                        'Success!',
                        'Tambah Jabatan Per Wilayah Berhasil!',
                        'success'
                    );
                    //Reset Form
                    $("#ProsesTambahJabatan")[0].reset();
                }
            }
        });
    });

    //Modal Detail Jabatan Per Wilayah
    $('#ModalDetailJabatan').on('show.bs.modal', function (e) {
        var id_position_region = $(e.relatedTarget).data('id');
        $('#FormDetailJabatan').html("Loading...");
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/JabatanPerWilayah/FormDetailJabatan.php',
            data        : {id_position_region: id_position_region},
            success     : function(data){
                $('#FormDetailJabatan').html(data);
            }
        });
    });

    //Modal Edit Jabatan
    $('#ModalEditJabatan').on('show.bs.modal', function (e) {
        var id_position_region = $(e.relatedTarget).data('id');
        $('#FormEditJabatan').html("Loading...");
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/JabatanPerWilayah/FormEditJabatan.php',
            data        : {id_position_region: id_position_region},
            success     : function(data){
                $('#FormEditJabatan').html(data);
                $('#NotifikasiEditJabatan').html('');
            }
        });
    });

    //Proses Edit Jabatan
    $('#ProsesEditJabatan').submit(function(){
        $('#NotifikasiEditJabatan').html('<div class="spinner-border text-secondary" role="status"><span class="sr-only"></span></div>');
        var form = $('#ProsesEditJabatan')[0];
        var data = new FormData(form);
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/JabatanPerWilayah/ProsesEditJabatan.php',
            data 	    :  data,
            cache       : false,
            processData : false,
            contentType : false,
            enctype     : 'multipart/form-data',
            success     : function(data){
                $('#NotifikasiEditJabatan').html(data);
                var NotifikasiEditJabatanBerhasil=$('#NotifikasiEditJabatanBerhasil').html();
                if(NotifikasiEditJabatanBerhasil=="Berhasil"){
                    $('#NotifikasiEditJabatan').html('');

                    //Tutup Modal
                    $('#ModalEditJabatan').modal('hide');

                    //Tampilkan Swal
                    Swal.fire(
                        'Success!',
                        'Ubah Data Jabatan Per Wilayah Berhasil!',
                        'success'
                    );

                    //Menampilkan Data
                    filterAndLoadTableJabatan();
                }
            }
        });
    });

    //Modal Hapus Jabatan
    $('#ModalHapusJabatan').on('show.bs.modal', function (e) {
        var id_position_region = $(e.relatedTarget).data('id');
        $('#FormHapusJabatan').html("Loading...");
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/JabatanPerWilayah/FormHapusJabatan.php',
            data        : {id_position_region: id_position_region},
            success     : function(data){
                $('#FormHapusJabatan').html(data);
                $('#NotifikasiHapusJabatan').html('');
            }
        });
    });

    //Proses Hapus Jabatan
    $('#ProsesHapusJabatan').submit(function(){
        $('#NotifikasiHapusJabatan').html('<div class="spinner-border text-secondary" role="status"><span class="sr-only"></span></div>');
        var form = $('#ProsesHapusJabatan')[0];
        var data = new FormData(form);
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/JabatanPerWilayah/ProsesHapusJabatan.php',
            data 	    :  data,
            cache       : false,
            processData : false,
            contentType : false,
            enctype     : 'multipart/form-data',
            success     : function(data){
                $('#NotifikasiHapusJabatan').html(data);
                var NotifikasiHapusJabatanBerhasil=$('#NotifikasiHapusJabatanBerhasil').html();
                if(NotifikasiHapusJabatanBerhasil=="Success"){
                    $('#NotifikasisHapus').html('');

                    //Tutup Modal
                    $('#ModalHapusJabatan').modal('hide');

                    //Tampilkan Swal
                     Swal.fire(
                        'Success!',
                        'Hapus Jabatan Per Wilayah Berhasil!',
                        'success'
                    )
                    //Menampilkan Data
                    filterAndLoadTableJabatan();
                }
            }
        });
    });

    //Jika Reset Form Import
    $('#ResetFormImportJabatan').submit(function(){

        //Reset Form Import
        $("#ProsesImportJabatan")[0].reset();

        //Kosongkan Table
        $('#NotifikasiImportJabatan').html('<tr><td colspan="9" class="text-center"><small class="text-danger">Belum Ada Proses Import</small></td></tr>');

        //Disable Button
        $('#ResetFormImportJabatan').prop('disabled', true);
    });

    //Proses Import Data Jabatan Per Wilayah
    $('#ProsesImportJabatan').submit(function(){
        var form = $('#ProsesImportJabatan')[0];
        var data = new FormData(form);
        $('#NotifikasiImportJabatan').html('<tr><td colspan="5" class="text-center">Loading...</td></tr>');
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/JabatanPerWilayah/ProsesImportJabatan.php',
            data 	    :  data,
            cache       : false,
            processData : false,
            contentType : false,
            enctype     : 'multipart/form-data',
            success     : function(data){
                $('#NotifikasiImportJabatan').html(data);
                $('#ResetFormImportJabatan').prop('disabled', false);
            }
        });
    });

    //Modal Export Jabatan
    $('#ModalExportJabatan').on('show.bs.modal', function (e) {
        $('#FormExportJabatan').html("Loading...");
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/JabatanPerWilayah/FormExportJabatan.php',
            success     : function(data){
                $('#FormExportJabatan').html(data);
            }
        });
    });

    //Ketika Modal Hapus Jabatan (Multiple) Muncul
    $('#ModalHapusJabatanMultiple').on('show.bs.modal', function (e) {
        $('#FormHapusJabatanMultiple').html('<tr><td class="text-center" colspan="4"><small>Loading...</small></td></tr>');
        var ProsesMultipleJabatan = $('#ProsesMultipleJabatan').serialize();
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/JabatanPerWilayah/FormHapusJabatanMultiple.php',
            data 	    :  ProsesMultipleJabatan,
            success     : function(data){
                $('#FormHapusJabatanMultiple').html(data);
            }
        });
    });

    //Proses Hapus Jabatan (Multiple)
    $('#ProsesHapusJabatanMultiple').submit(function(){
        $('#NotifikasiHapusJabatanMultiple').html('Loading...');
        var form = $('#ProsesHapusJabatanMultiple')[0];
        var data = new FormData(form);
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/JabatanPerWilayah/ProsesHapusJabatanMultiple.php',
            data 	    :  data,
            cache       : false,
            processData : false,
            contentType : false,
            enctype     : 'multipart/form-data',
            success     : function(data){
                $('#NotifikasiHapusJabatanMultiple').html(data);

                var NotifikasiHapusJabatanMultipleBerhasil=$('#NotifikasiHapusJabatanMultipleBerhasil').html();
                if(NotifikasiHapusJabatanMultipleBerhasil=="Berhasil"){
                    $('#NotifikasiHapusJabatanMultiple').html('');

                    //Tutup Modal
                    $('#ModalHapusJabatanMultiple').modal('hide');

                    //Tampilkan Swal
                     Swal.fire(
                        'Success!',
                        'Hapus Data Jabatan Per Wilayah Berhasil!',
                        'success'
                    )
                    //Menampilkan Data
                    filterAndLoadTableJabatan();
                }
            }
        });
    });
    
});