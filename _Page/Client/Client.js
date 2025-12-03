//Fungsi Menampilkan Data
function filterAndLoadTable() {
    // Tangkap data form filter
    var ProsesFilter = $('#ProsesFilter').serialize();

    // Tampilkan loading fade-out tabel lama
    $('#TabelClient')
        .css({ opacity: 0, transition: 'opacity 0.3s ease' });

    // Proses AJAX
    $.ajax({
        type: 'POST',
        url: '_Page/Client/TabelClient.php',
        data: ProsesFilter,
        success: function(data) {

            // Masukkan hasil baru
            $('#TabelClient').html(data);

            // Delay sedikit agar transisi berjalan
            setTimeout(function() {
                $('#TabelClient').css({ opacity: 1 });
            }, 30);
        }
    });
}

// Fungsi generate random password
function generatePassword(length) {
    var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var result = '';
    var charactersLength = characters.length;
    for (var i = 0; i < length; i++) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
}

$(document).ready(function() {
    //Menampilkan Data Pada Saat Pertama Kali
    filterAndLoadTable();

    //Proses Filter/Pencarian
    $('#ProsesFilter').submit(function(){
        //Reset Ke halaman 1
        $('#page').val("1");

        //Tampilkan Ulang Tabel
        filterAndLoadTable();

        //Tutup Modal
        $('#ModalFilter').modal('hide');
    });

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

    //Ketika keyword_by diubah
    $('#KeywordBy').change(function(){
        var KeywordBy =$('#KeywordBy').val();
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Client/FormFilter.php',
            data        : {KeywordBy: KeywordBy},
            success     : function(data){
                $('#FormFilter').html(data);
            }
        });
    });

    // Ketika Modal Import Muncul
    $('#ModalImport').on('show.bs.modal', function (e) {

        //Kosongkan Notifikasi
        $('#NotifikasiImport').html("");

        // Reset Form
        $("#ProsesImport")[0].reset();
    });

    //Ketika Proses Import Di Submit
    $('#ProsesImport').submit(function(){

        //Loading Notifikasi
        $('#NotifikasiImport').html('Loading...');

        //Tangkap Data Dari Form
        var form = $('#ProsesImport')[0];
        var data = new FormData(form);
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Client/ProsesImport.php',
            data 	    :  data,
            cache       : false,
            processData : false,
            contentType : false,
            enctype     : 'multipart/form-data',
            success     : function(data){
                $('#NotifikasiImport').html(data);
            }
        });
    });

    // Hanya izinkan angka untuk kontak
    $(document).on('input', '#kontak_akses', function() {
        this.value = this.value.replace(/[^0-9]/g, ''); 
    });

    // Hanya izinkan angka untuk kontak
    $(document).on('input', '#kontak_akses_edit', function() {
        this.value = this.value.replace(/[^0-9]/g, ''); 
    });


    //Kondisi saat tampilkan password
    $('.form-check-input').click(function(){
        if($(this).is(':checked')){
            $('#password').attr('type','text');
        }else{
            $('#password').attr('type','password');
        }
    });

    // Event klik tombol generate
    $("#generate_password").on("click", function(){
        var newPass = generatePassword(15);
        $("#password").val(newPass);
    });

    //Kondisi Pada Saat Level Diubah
    $('#level').change(function(){
        var level =$('#level').val();

        //Apabila level kosong atau National
        if(level==""||level=="National"){

            //Kosongkan FormProvince dan FormDistrict
            $('#FormProvince').html("");
            $('#FormDistrict').html("");
        }

        //Apabila Level Province
        if(level=="Province"){

            //Tampilkan FormProvince
            $.ajax({
                type 	    : 'POST',
                url 	    : '_Page/Client/FormProvince.php',
                success     : function(data){
                    $('#FormProvince').html(data);
                }
            });

            //Kosongkan FormDistrict
            $('#FormDistrict').html("");
        }

        //Apabila Level District
        if(level=="District"){

            //Tampilkan FormProvince
            $.ajax({
                type 	    : 'POST',
                url 	    : '_Page/Client/FormProvince.php',
                success     : function(data){
                    $('#FormProvince').html(data);
                }
            });

            //Tampilkan FormDistrict
            $.ajax({
                type 	    : 'POST',
                url 	    : '_Page/Client/FormDistrict.php',
                success     : function(data){
                    $('#FormDistrict').html(data);
                }
            });
        }
    });

    // Event saat select province berubah
    $(document).on('change', '#province', function(){

        //Ambil informasi level
        var level = $('#level').val();

        // ambil id_region dari provinsi
        var province_id = $(this).val(); 
        if(level=="District"){
            $.ajax({
                type    : 'POST',
                url     : '_Page/Client/FormDistrict.php',
                data    : {province_id: province_id},
                success : function(data){
                    $('#FormDistrict').html(data);
                }
            });
        }
    });

    //Proses Tambah
    $('#ProsesTambah').submit(function(){
        $('#NotifikasiTambah').html('<div class="spinner-border text-secondary" role="status"><span class="sr-only"></span></div>');
        var form = $('#ProsesTambah')[0];
        var data = new FormData(form);
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Client/ProsesTambah.php',
            data 	    :  data,
            cache       : false,
            processData : false,
            contentType : false,
            enctype     : 'multipart/form-data',
            success     : function(data){
                $('#NotifikasiTambah').html(data);
                var NotifikasiTambahBerhasil=$('#NotifikasiTambahBerhasil').html();
                if(NotifikasiTambahBerhasil=="Berhasil"){
                    //Kosongkan Notifikasi
                    $('#NotifikasiTambah').html('');

                    //Kosongkan Form Province dan District
                    $('#FormProvince').html('');
                    $('#FormDistrict').html('');

                    //Reset Halaman Ke page 1
                    $('#page').val("1");

                    //Reset Semua Filter
                    $("#ProsesFilter")[0].reset();

                    //Reset Form Tambah
                    $("#ProsesTambah")[0].reset();

                    //Tutup Modal
                    $('#ModalTambah').modal('hide');

                    //Tampilkan Swal
                    Swal.fire(
                        'Success!',
                        'Tambah Akses Client Berhasil!',
                        'success'
                    )

                    //Menampilkan Ulang Data Tabel Client
                    filterAndLoadTable();
                }
            }
        });
    });

    //Detail Akses
    $('#ModalDetail').on('show.bs.modal', function (e) {
        var id_access = $(e.relatedTarget).data('id');
        $('#FormDetail').html("Loading...");
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Client/FormDetail.php',
            data        : {id_access: id_access},
            success     : function(data){
                $('#FormDetail').html(data);
            }
        });
    });

    //Edit Akses
    $('#ModalEditAkses').on('show.bs.modal', function (e) {
        var id_access = $(e.relatedTarget).data('id');
        $('#FormEditAkses').html("Loading...");
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Client/FormEditAkses.php',
            data        : {id_access: id_access},
            success     : function(data){
                $('#FormEditAkses').html(data);
            }
        });
    });

    //Proses Edit Akses
    $('#ProsesEditAkses').submit(function(){
        $('#NotifikasiEditAkses').html('<div class="spinner-border text-secondary" role="status"><span class="sr-only"></span></div>');
        var form = $('#ProsesEditAkses')[0];
        var data = new FormData(form);
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Client/ProsesEditAkses.php',
            data 	    :  data,
            cache       : false,
            processData : false,
            contentType : false,
            enctype     : 'multipart/form-data',
            success     : function(data){
                $('#NotifikasiEditAkses').html(data);
                var NotifikasiEditAksesBerhasil=$('#NotifikasiEditAksesBerhasil').html();
                if(NotifikasiEditAksesBerhasil=="Berhasil"){

                    //Bersihkan Notifikasi
                    $('#NotifikasiEditAkses').html('');

                    //Tutup Modal
                    $('#ModalEditAkses').modal('hide');

                    //Tampilkan Swal
                    Swal.fire(
                        'Success!',
                        'Ubah Informasi Akses Berhasil!',
                        'success'
                    )

                    //Menampilkan Data
                    filterAndLoadTable();
                }
            }
        });
    });

    //Edit Level
    $('#ModalEditLevel').on('show.bs.modal', function (e) {
        var id_access = $(e.relatedTarget).data('id');
        $('#FormEditLevel').html("Loading...");
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Client/FormEditLevel.php',
            data        : {id_access: id_access},
            success     : function(data){
                $('#FormEditLevel').html(data);

                //Kosongkan Notifikasi
                $('#NotifikasiEditLevel').html('');

                //Kosongkan FormProvince dan FormDistrict
                $('#FormProvinceEdit').html("");
                $('#FormDistrictEdit').html("");
            }
        });
    });

    //Kondisi Pada Saat Level Diubah
    $(document).on('change', '#level_edit', function(){
        var level =$('#level_edit').val();

        //Apabila level kosong atau National
        if(level==""||level=="National"){

            //Kosongkan FormProvince dan FormDistrict
            $('#FormProvinceEdit').html("");
            $('#FormDistrictEdit').html("");
        }

        //Apabila Level Province
        if(level=="Province"){

            //Tampilkan FormProvince
            $.ajax({
                type 	    : 'POST',
                url 	    : '_Page/Client/FormProvinceEdit.php',
                success     : function(data){
                    $('#FormProvinceEdit').html(data);
                }
            });

            //Kosongkan FormDistrict
            $('#FormDistrictEdit').html("");
        }

        //Apabila Level District
        if(level=="District"){

            //Tampilkan FormProvince
            $.ajax({
                type 	    : 'POST',
                url 	    : '_Page/Client/FormProvinceEdit.php',
                success     : function(data){
                    $('#FormProvinceEdit').html(data);
                }
            });

            //Tampilkan FormDistrict
            $.ajax({
                type 	    : 'POST',
                url 	    : '_Page/Client/FormDistrictEdit.php',
                success     : function(data){
                    $('#FormDistrictEdit').html(data);
                }
            });
        }
    });

    // Event saat select province berubah
    $(document).on('change', '#province_edit', function(){

        //Ambil informasi level
        var level = $('#level_edit').val();

        // ambil id_region dari provinsi
        var province_id = $(this).val(); 
        if(level=="District"){
            $.ajax({
                type    : 'POST',
                url     : '_Page/Client/FormDistrictEdit.php',
                data    : {province_id: province_id},
                success : function(data){
                    $('#FormDistrictEdit').html(data);
                }
            });
        }
    });

    //Proses Ubah Level
    $('#ProsesEditLevel').submit(function(){
        $('#NotifikasiEditLevel').html('<div class="spinner-border text-secondary" role="status"><span class="sr-only"></span></div>');
        var form = $('#ProsesEditLevel')[0];
        var data = new FormData(form);
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Client/ProsesEditLevel.php',
            data 	    :  data,
            cache       : false,
            processData : false,
            contentType : false,
            enctype     : 'multipart/form-data',
            success     : function(data){
                $('#NotifikasiEditLevel').html(data);
                var NotifikasiEditLevelBerhasil=$('#NotifikasiEditLevelBerhasil').html();
                if(NotifikasiEditLevelBerhasil=="Berhasil"){

                    //Bersihkan Notifikasi
                    $('#NotifikasiEditLevel').html('');

                    //Tutup Modal
                    $('#ModalEditLevel').modal('hide');

                    //Tampilkan Swal
                    Swal.fire(
                        'Success!',
                        'Ubah Level Akses Client Berhasil!',
                        'success'
                    )

                    //Menampilkan Ulang Tabel Data Akses
                    filterAndLoadTable();
                }
            }
        });
    });

    //Modal Ubah Password
    $('#ModalUbahPassword').on('show.bs.modal', function (e) {
        var id_access = $(e.relatedTarget).data('id');
        $('#FormUbahPassword').html("Loading...");
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Client/FormUbahPassword.php',
            data        : {id_access: id_access},
            success     : function(data){
                $('#FormUbahPassword').html(data);
            }
        });
    });

    //Proses Ubah Password
    $('#ProsesUbahPassword').submit(function(){
        $('#NotifikasiUbahPassword').html('<div class="spinner-border text-secondary" role="status"><span class="sr-only"></span></div>');
        var form = $('#ProsesUbahPassword')[0];
        var data = new FormData(form);
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Client/ProsesUbahPassword.php',
            data 	    :  data,
            cache       : false,
            processData : false,
            contentType : false,
            enctype     : 'multipart/form-data',
            success     : function(data){
                $('#NotifikasiUbahPassword').html(data);
                var NotifikasiUbahPasswordBerhasil=$('#NotifikasiUbahPasswordBerhasil').html();
                if(NotifikasiUbahPasswordBerhasil=="Success"){

                    //Bersihkan Notifikasi
                    $('#NotifikasiUbahPassword').html('');

                    //Tutup Modal
                    $('#ModalUbahPassword').modal('hide');

                    //Tampilkan Swal
                    Swal.fire(
                        'Success!',
                        'Ubah Foto Akses Berhasil!',
                        'success'
                    )

                    //Menampilkan Ulang Tabel Data Akses
                    filterAndLoadTable();
                }
            }
        });
    });


    //Modal Ubah Foto
    $('#ModalUbahFoto').on('show.bs.modal', function (e) {
        var id_access = $(e.relatedTarget).data('id');
        $('#FormUbahFoto').html("Loading...");
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Client/FormUbahFoto.php',
            data        : {id_access: id_access},
            success     : function(data){
                $('#FormUbahFoto').html(data);
            }
        });
    });
    //Proses Ubah Foto Profil
    $('#ProsesUbahFoto').submit(function(){
        $('#NotifikasiUbahFoto').html('<div class="spinner-border text-secondary" role="status"><span class="sr-only"></span></div>');
        var form = $('#ProsesUbahFoto')[0];
        var data = new FormData(form);
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Client/ProsesUbahFoto.php',
            data 	    :  data,
            cache       : false,
            processData : false,
            contentType : false,
            enctype     : 'multipart/form-data',
            success     : function(data){
                $('#NotifikasiUbahFoto').html(data);
                var NotifikasiUbahFotoBerhasil=$('#NotifikasiUbahFotoBerhasil').html();
                if(NotifikasiUbahFotoBerhasil=="Success"){
                    
                    //Kosongkan Notifikasi
                    $('#NotifikasiUbahFoto').html('');

                    //Tutup Modal
                    $('#ModalUbahFoto').modal('hide');

                    //Tampilkan Swal
                    Swal.fire(
                        'Success!',
                        'Ubah Foto Client Berhasil!',
                        'success'
                    )

                    //Menampilkan Ulang Data Tabel Client
                    filterAndLoadTable();
                }
            }
        });
    });

    //Modal Hapus
    $('#ModalHapus').on('show.bs.modal', function (e) {
        var id_access = $(e.relatedTarget).data('id');
        $('#FormHapus').html("Loading...");
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Client/FormHapus.php',
            data        : {id_access: id_access},
            success     : function(data){
                $('#FormHapus').html(data);

                //Enable tombol
                $('#ButtonHapus').prop('disabled', false);

                //Bersihkan Notifikasi
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
            url 	    : '_Page/Client/ProsesHapus.php',
            data 	    :  data,
            cache       : false,
            processData : false,
            contentType : false,
            enctype     : 'multipart/form-data',
            success     : function(data){
                $('#NotifikasiHapus').html(data);
                var NotifikasiHapusBerhasil=$('#NotifikasiHapusBerhasil').html();
                if(NotifikasiHapusBerhasil=="Success"){
                    //Bersihkan Notifikasi
                    $('#NotifikasiHapus').html('');

                    //Tutup Modal
                    $('#ModalHapus').modal('hide');

                    //Tampilkan Swal
                    Swal.fire(
                        'Success!',
                        'Hapus Akses Client Berhasil!',
                        'success'
                    )

                    //Menampilkan Data
                    filterAndLoadTable();
                }
            }
        });
    });

});