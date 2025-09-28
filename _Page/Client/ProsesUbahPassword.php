<?php
    //Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    include "../../_Config/SettingEmail.php";
    include "../../_Config/SettingGeneral.php";

    //Time Zone
    date_default_timezone_set('Asia/Jakarta');

    //Time Now Tmp
    $now=date('Y-m-d H:i:s');

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

    //Tangkap password
    if(empty($_POST['password'])){
         echo '
            <div class="alert alert-danger">
                <small>
                    Password Baru Tidak Boleh Kosong!
                </small>
            </div>
        ';
        exit;
    }

    //Sanitasi Variabel
    $id_access  = validateAndSanitizeInput($_POST['id_access']);
    $password   = validateAndSanitizeInput($_POST['password']);

    if(empty($_POST['kirim_email_edit'])){
        $kirim_email_edit   = "";
    }else{
        $kirim_email_edit   = validateAndSanitizeInput($_POST['kirim_email_edit']);
    }

    // Validasi panjang dan karakter password
    if(strlen($password) < 6 || strlen($password) > 20 || !preg_match("/^[a-zA-Z0-9]*$/", $password)){
        echo '<div class="alert alert-danger"><small>Password harus 6-20 karakter huruf/angka!</small></div>';
        exit;
    }

    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    //Buka Data access
    $Qry = $Conn->prepare("SELECT * FROM access WHERE id_access = ?");
    $Qry->bind_param("i", $id_access);
    if (!$Qry->execute()) {
        $error=$Conn->error;
        echo '
            <div class="alert alert-danger">
                <small>Terjadi kesalahan pada saat membuka data dari database!<br>Keterangan : '.$error.'</small>
            </div>
        ';
        exit;
    }
    $Result = $Qry->get_result();
    $Data = $Result->fetch_assoc();
    $Qry->close();

    //Buat Variabel
    $access_name        =$Data['access_name'];
    $access_email       =$Data['access_email'];

    //Update Password  
    $UpdateAkses = mysqli_query($Conn,"UPDATE access SET 
        access_password='$password_hash'
    WHERE id_access='$id_access'") or die(mysqli_error($Conn)); 
    if($UpdateAkses){

        //Apabila Kirim Email
        if($kirim_email_edit=="Ya"){
            $nama_tujuan    = $access_name;
            $email_tujuan   = $access_email;
            $subjek         = "Perubahan Password - $app_title";
            $pesan          = '
            Kepada YTH. <b>'.$nama_akses.'</b> <br> 
            Berikut ini kami sampaikan perubahan password akses ke aplikasi <b>'.$app_title.'</b> untuk dapat melakukan login dan mengubah password standar yang sudah ada.
            <p>
                <b>Email : </b> '.$access_email.'<br>
                <b>Password : </b> '.$password.'<br>
                <b>URL Aplikasi : </b> '.$app_base_url.'<br>
            </p>
            ';

            $kirim_email=SendEmail($nama_tujuan,$email_tujuan,$subjek,$pesan,$email_gateway,$password_gateway,$url_provider,$nama_pengirim,$port_gateway,$url_service);
        }
        echo '<small class="text-success" id="NotifikasiUbahPasswordBerhasil">Success</small>';
    }else{
        echo '<div class="alert alert-danger"><small>Terjadi kesalahan pada saat update password!</small></div>';
    }
?>