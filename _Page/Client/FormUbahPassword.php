<?php
    //Zona Waktu
    date_default_timezone_set('Asia/Jakarta');

    //Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/SettingGeneral.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    //Validasi Sesi Akses
    if (empty($SessionIdAccess)) {
        echo '
            <div class="alert alert-danger">
                <small>
                    Sesi akses sudah berakhir. Silahkan <b>login</b> ulang!
                </small>
            </div>
        ';
        exit;
    }

    //Tangkap id_access
    if(empty($_POST['id_access'])){
         echo '
            <div class="alert alert-danger">
                <small>
                    ID Access Tidak Boleh Kosong!
                </small>
            </div>
        ';
        exit;
    }
    $id_access=validateAndSanitizeInput($_POST['id_access']);

    //Tampilkan Form
    echo '
        <input type="hidden" name="id_access" value="'.$id_access.'">
        <div class="row mb-3">
            <div class="col-12">
                <label for="password_edit">
                    <small>Password Baru</small>
                </label>
                <div class="input-group">
                    <input type="password" name="password" id="password_edit" class="form-control" required>
                    <span class="input-group-text" id="generate_password_edit" style="cursor:pointer;">
                        <small><i class="bi bi-arrow-clockwise"></i> Generate</small>
                    </span>
                </div>
                <small class="credit">
                    <small class="text-grayish">
                        Password minimal memiliki 6 karakter dan maksimal 20 karakter. Gunakan tombol generate untuk membuat password dari 15 karakter acak.
                    </small>
                </small>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="Tampilkan" id="TampilkanPasswordEdit" name="TampilkanPasswordEdit">
                    <label class="form-check-label" for="TampilkanPasswordEdit">
                        <small class="text text-dark">Tampilkan Password</small>
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" checked value="Ya" id="kirim_email_edit" name="kirim_email_edit">
                    <label class="form-check-label" for="kirim_email_edit">
                        <small class="text text-dark">Kirim perubahan password melalui email</small>
                    </label>
                </div>
            </div>
        </div>
    ';
?>
<script>
    //Kondisi saat tampilkan password
    $('#TampilkanPasswordEdit').click(function(){
        if($(this).is(':checked')){
            $('#password_edit').attr('type','text');
        }else{
            $('#password_edit').attr('type','password');
        }
    });

    // Event klik tombol generate
    $("#generate_password_edit").on("click", function(){
        var newPass = generatePassword(15);
        $("#password_edit").val(newPass);
    });
</script>