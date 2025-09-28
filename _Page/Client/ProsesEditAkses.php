<?php
    // Koneksi & dependensi
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    date_default_timezone_set('Asia/Jakarta');
    $now = date('Y-m-d H:i:s');

    // --- Validasi sesi ---
    if (empty($SessionIdAccess)) {
        echo '<div class="alert alert-danger"><small>Sesi akses sudah berakhir! Silahkan login ulang!</small></div>';
        exit;
    }

    // --- Validasi input wajib ---
    if(empty($_POST['id_access'])){
        echo '<div class="alert alert-danger"><small>ID Client Tidak Boleh Kosong</small></div>';
        exit;
    }
    if(empty($_POST['nama_akses'])){
        echo '<div class="alert alert-danger"><small>Nama Client Tidak Boleh Kosong</small></div>';
        exit;
    }
    if(empty($_POST['email_akses'])){
        echo '<div class="alert alert-danger"><small>Alamat Email Client Tidak Boleh Kosong</small></div>';
        exit;
    }

    // --- Sanitasi input ---
    $id_access          = validateAndSanitizeInput($_POST['id_access']);
    $nama_akses         = validateAndSanitizeInput($_POST['nama_akses']);
    $kontak_akses       = !empty($_POST['kontak_akses']) ? validateAndSanitizeInput($_POST['kontak_akses']) : "";
    $email_akses         = validateAndSanitizeInput($_POST['email_akses']);

    //Buka Data Lama
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
    $access_email_old       = $Data['access_email'];
    $access_contact_old     = $Data['access_contact'];

    //Apabila kontak diisi maka lakukan validasi duplikat
    if(!empty($kontak_akses)){

        //Apabila kontak lama dan baru tidak sama
        if($kontak_akses!==$access_contact_old){
            $validasi_kontak_duplikat = mysqli_num_rows(mysqli_query($Conn, "SELECT id_access FROM access WHERE access_contact='$kontak_akses'"));
            if(!empty($validasi_kontak_duplikat)){
                echo '<div class="alert alert-danger"><small>Nomor kontak yang anda masukan sudah terdaftar!</small></div>';
                exit;
            }
        }
    }

    //Apabila email baru dan email lama tidak sama lakukan validasi
    if($email_akses!==$access_email_old){
        $validasi_email_duplikat = mysqli_num_rows(mysqli_query($Conn, "SELECT id_access FROM access WHERE access_email='$email_akses'"));
        if(!empty($validasi_email_duplikat)){
            echo '<div class="alert alert-danger"><small>Email yang anda masukan sudah terdaftar!</small></div>';
            exit;
        }
    }

    //Update Data Akses
    $QryUpdate = $Conn->prepare("UPDATE access SET access_name=?, access_email=?, access_contact=? WHERE id_access=?");
    $QryUpdate->bind_param("sssi", $nama_akses, $email_akses, $kontak_akses, $id_access);
    if($QryUpdate->execute()){
        echo '
            <div class="alert alert-success">
                <small>Update data ke database <b id="NotifikasiEditAksesBerhasil">Berhasil</b></small>
            </div>
        ';
    }else{
        echo '
            <div class="alert alert-danger">
                <small>Terjadi Kesalahan Pada Saat Update Data Ke Database!</small>
            </div>
        ';
    }
?>
