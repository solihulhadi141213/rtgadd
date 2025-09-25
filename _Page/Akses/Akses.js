//Fungsi Menampilkan Data
function filterAndLoadTable() {
    var ProsesFilter = $('#ProsesFilter').serialize();
    $.ajax({
        type: 'POST',
        url: '_Page/Akses/TabelAkses.php',
        data: ProsesFilter,
        success: function(data) {
            $('#TabelAkses').html(data);
        }
    });
}

$(document).ready(function() {
    //Menampilkan Data Pada Saat Pertama Kali
    filterAndLoadTable();

    //Proses Filter/Pencarian
    $('#ProsesFilter').submit(function(){
        $('#page').val("1");
        filterAndLoadTable();
        $('#ModalFilterAkses').modal('hide');
    });

    //Ketika keyword_by diubah
    $('#KeywordBy').change(function(){
        var KeywordBy =$('#KeywordBy').val();
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Akses/FormFilter.php',
            data        : {KeywordBy: KeywordBy},
            success     : function(data){
                $('#FormFilter').html(data);
            }
        });
    });

    //Kondisi saat tampilkan password
    $('.form-check-input').click(function(){
        if($(this).is(':checked')){
            $('#password1').attr('type','text');
            $('#password2').attr('type','text');
        }else{
            $('#password1').attr('type','password');
            $('#password2').attr('type','password');
        }
    });

    //Proses Tambah Akses
    $('#ProsesTambahAkses').submit(function(){
        $('#NotifikasiTambahAkses').html('<div class="spinner-border text-secondary" role="status"><span class="sr-only"></span></div>');
        var form = $('#ProsesTambahAkses')[0];
        var data = new FormData(form);
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Akses/ProsesTambahAkses.php',
            data 	    :  data,
            cache       : false,
            processData : false,
            contentType : false,
            enctype     : 'multipart/form-data',
            success     : function(data){
                $('#NotifikasiTambahAkses').html(data);
                var NotifikasiTambahAksesBerhasil=$('#NotifikasiTambahAksesBerhasil').html();
                if(NotifikasiTambahAksesBerhasil=="Success"){
                    $('#NotifikasiTambahAkses').html('');
                    $('#page').val("1");
                    $("#ProsesFilter")[0].reset();
                    $("#ProsesTambahAkses")[0].reset();
                    $('#ModalTambahAkses').modal('hide');
                    Swal.fire(
                        'Success!',
                        'Tambah Akses Berhasil!',
                        'success'
                    )
                    //Menampilkan Data
                    filterAndLoadTable();
                }
            }
        });
    });

    //Detail Akses
    $('#ModalDetailAkses').on('show.bs.modal', function (e) {
        var id_access = $(e.relatedTarget).data('id');
        $('#FormDetailAkses').html("Loading...");
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Akses/FormDetailAkses.php',
            data        : {id_access: id_access},
            success     : function(data){
                $('#FormDetailAkses').html(data);
            }
        });
    });

    //Edit Akses
    $('#ModalEditAkses').on('show.bs.modal', function (e) {
        var id_access = $(e.relatedTarget).data('id');
        $('#FormEditAkses').html("Loading...");
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Akses/FormEditAkses.php',
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
            url 	    : '_Page/Akses/ProsesEditAkses.php',
            data 	    :  data,
            cache       : false,
            processData : false,
            contentType : false,
            enctype     : 'multipart/form-data',
            success     : function(data){
                $('#NotifikasiEditAkses').html(data);
                var NotifikasiEditAksesBerhasil=$('#NotifikasiEditAksesBerhasil').html();
                if(NotifikasiEditAksesBerhasil=="Success"){
                    $('#NotifikasiEditAkses').html('');
                    $('#ModalEditAkses').modal('hide');
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

    //Modal Ubah Password
    $('#ModalUbahPassword').on('show.bs.modal', function (e) {
        var id_access = $(e.relatedTarget).data('id');
        $('#FormUbahPassword').html("Loading...");
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Akses/FormUbahPassword.php',
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
            url 	    : '_Page/Akses/ProsesUbahPassword.php',
            data 	    :  data,
            cache       : false,
            processData : false,
            contentType : false,
            enctype     : 'multipart/form-data',
            success     : function(data){
                $('#NotifikasiUbahPassword').html(data);
                var NotifikasiUbahPasswordBerhasil=$('#NotifikasiUbahPasswordBerhasil').html();
                if(NotifikasiUbahPasswordBerhasil=="Success"){
                    $('#NotifikasiUbahFotoAksesBerhasil').html('');
                    $('#NotifikasiUbahPassword').html('');
                    $('#ModalUbahPassword').modal('hide');
                    Swal.fire(
                        'Success!',
                        'Ubah Foto Akses Berhasil!',
                        'success'
                    )
                    //Menampilkan Data
                    filterAndLoadTable();
                }
            }
        });
    });


    //Modal Ubah Foto
    $('#ModalUbahFotoAkses').on('show.bs.modal', function (e) {
        var id_access = $(e.relatedTarget).data('id');
        $('#FormUbahFotoAkses').html("Loading...");
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Akses/FormUbahFotoAkses.php',
            data        : {id_access: id_access},
            success     : function(data){
                $('#FormUbahFotoAkses').html(data);
            }
        });
    });
    //Proses Ubah Foto Profil
    $('#ProsesUbahFotoAkses').submit(function(){
        $('#NotifikasiUbahFotoAkses').html('<div class="spinner-border text-secondary" role="status"><span class="sr-only"></span></div>');
        var form = $('#ProsesUbahFotoAkses')[0];
        var data = new FormData(form);
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Akses/ProsesUbahFotoAkses.php',
            data 	    :  data,
            cache       : false,
            processData : false,
            contentType : false,
            enctype     : 'multipart/form-data',
            success     : function(data){
                $('#NotifikasiUbahFotoAkses').html(data);
                var NotifikasiUbahFotoAksesBerhasil=$('#NotifikasiUbahFotoAksesBerhasil').html();
                if(NotifikasiUbahFotoAksesBerhasil=="Success"){
                    $('#NotifikasiUbahFotoAksesBerhasil').html('');
                    $('#NotifikasiUbahFotoAkses').html('');
                    $('#ModalUbahFotoAkses').modal('hide');
                    Swal.fire(
                        'Success!',
                        'Ubah Foto Akses Berhasil!',
                        'success'
                    )
                    //Menampilkan Data
                    filterAndLoadTable();
                }
            }
        });
    });

    //Modal Ubah Izin Akses
    $('#ModalUbahIzinAkses').on('show.bs.modal', function (e) {
        var id_access = $(e.relatedTarget).data('id');
        $('#FormUbahIzinAkses').html("Loading...");
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Akses/FormUbahIzinAkses.php',
            data        : {id_access: id_access},
            success     : function(data){
                $('#FormUbahIzinAkses').html(data);
                $('#NotifikasiUbahIzinAkses').html('');
            }
        });
    });
    //Proses Ubah Izin Akses
    $('#ProsesUbahIzinAkses').submit(function(){
        $('#NotifikasiUbahIzinAkses').html('<div class="spinner-border text-secondary" role="status"><span class="sr-only"></span></div>');
        var form = $('#ProsesUbahIzinAkses')[0];
        var data = new FormData(form);
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Akses/ProsesUbahIzinAkses.php',
            data 	    :  data,
            cache       : false,
            processData : false,
            contentType : false,
            enctype     : 'multipart/form-data',
            success     : function(data){
                $('#NotifikasiUbahIzinAkses').html(data);
                var NotifikasiUbahIzinAksesBerhasil=$('#NotifikasiUbahIzinAksesBerhasil').html();
                if(NotifikasiUbahIzinAksesBerhasil=="Success"){
                    $('#NotifikasiUbahIzinAksesBerhasil').html('');
                    $('#ModalUbahIzinAkses').modal('hide');
                    Swal.fire(
                        'Success!',
                        'Ubah Izin Akses Berhasil!',
                        'success'
                    )
                    //Menampilkan Data
                    filterAndLoadTable();
                }
            }
        });
    });

    //Modal Hapus Akses
    $('#ModalHapusAkses').on('show.bs.modal', function (e) {
        var id_access = $(e.relatedTarget).data('id');
        $('#FormHapusAkses').html("Loading...");
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Akses/FormHapusAkses.php',
            data        : {id_access: id_access},
            success     : function(data){
                $('#FormHapusAkses').html(data);
            }
        });
    });

    //Proses Hapus Akses
    $('#ProsesHapusAkses').submit(function(){
        $('#NotifikasiHapusAkses').html('<div class="spinner-border text-secondary" role="status"><span class="sr-only"></span></div>');
        var form = $('#ProsesHapusAkses')[0];
        var data = new FormData(form);
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/Akses/ProsesHapusAkses.php',
            data 	    :  data,
            cache       : false,
            processData : false,
            contentType : false,
            enctype     : 'multipart/form-data',
            success     : function(data){
                $('#NotifikasiHapusAkses').html(data);
                var NotifikasiHapusAksesBerhasil=$('#NotifikasiHapusAksesBerhasil').html();
                if(NotifikasiHapusAksesBerhasil=="Success"){
                    $('#NotifikasiHapusAkses').html('');
                    $('#ModalHapusAkses').modal('hide');
                    Swal.fire(
                        'Success!',
                        'Hapus Data Akses Berhasil!',
                        'success'
                    )
                    //Menampilkan Data
                    filterAndLoadTable();
                }
            }
        });
    });

    //Modal Detail Group
    $('#ModalDetailGroup').on('show.bs.modal', function (e) {
        var id_access_group = $(e.relatedTarget).data('id');
        $('#FormDetailGroup').html("Loading...");
        $.ajax({
            type 	    : 'POST',
            url 	    : '_Page/AksesEntitas/FormDetailEntitias.php',
            data        : {id_access_group: id_access_group},
            success     : function(data){
                $('#FormDetailGroup').html(data);
            }
        });
    });

});