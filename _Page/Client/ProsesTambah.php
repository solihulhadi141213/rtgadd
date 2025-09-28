<?php
    //Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    include "../../_Config/SettingEmail.php";
    include "../../_Config/SettingGeneral.php";

    //Time Zone
    date_default_timezone_set('Asia/Jakarta');
    $now = date('Y-m-d H:i:s');

    // --- Validasi Sesi Akses Tidak Boleh Kosong ---
    if (empty($SessionIdAccess)) {
        echo '<div class="alert alert-danger"><small>Sesi Akses Sudah Berakhir! Silahkan Login Ulang!</small></div>';
        exit;
    }

    //--- Validasi Form Tidak Boleh Kosong ---
    if(empty($_POST['nama_akses'])){
        echo '<div class="alert alert-danger"><small>Nama Client Tidak Boleh Kosong</small></div>';
        exit;
    }
    if(empty($_POST['email_akses'])){
        echo '<div class="alert alert-danger"><small>Alamat Email Client Tidak Boleh Kosong</small></div>';
        exit;
    }
    if(empty($_POST['password'])){
        echo '<div class="alert alert-danger"><small>Password Client Tidak Boleh Kosong</small></div>';
        exit;
    }
    if(empty($_POST['level'])){
        echo '<div class="alert alert-danger"><small>Level Akses Client Tidak Boleh Kosong</small></div>';
        exit;
    }

    // Buat Variabelnya dan Sanitasi input
    $nama_akses         = validateAndSanitizeInput($_POST['nama_akses']);
    $email_akses        = validateAndSanitizeInput($_POST['email_akses']);
    $password           = validateAndSanitizeInput($_POST['password']);
    $level              = validateAndSanitizeInput($_POST['level']);
    $id_access_group    = null; // access_group
    $access_client      = 1;    // access_client

    //Variabel tidak wajib
    $kontak_akses   = !empty($_POST['kontak_akses']) ? validateAndSanitizeInput($_POST['kontak_akses']) : "";
    $keterangan     = !empty($_POST['keterangan']) ? validateAndSanitizeInput($_POST['keterangan']) : "";
    $kirim_email    = !empty($_POST['kirim_email']) ? validateAndSanitizeInput($_POST['kirim_email']) : "";

    // Validasi panjang password
    if(strlen($password) < 6 || strlen($password) > 20 || !preg_match("/^[a-zA-Z0-9]*$/", $password)){
        echo '<div class="alert alert-danger"><small>Password harus 6-20 karakter huruf/angka!</small></div>';
        exit;
    }

    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    //Validasi Province dan District Berdasarkan Level
    $id_region = null;
    if($level=="District"){
        if(empty($_POST['province'])){
            echo '<div class="alert alert-danger"><small>Informasi Provinsi Tidak Boleh Kosong</small></div>';
            exit;
        }
        if(empty($_POST['district'])){
            echo '<div class="alert alert-danger"><small>Informasi Kab/Kota Tidak Boleh Kosong</small></div>';
            exit;
        }
        $id_region=$_POST['district'];
    } elseif($level=="Province"){
        if(empty($_POST['province'])){
            echo '<div class="alert alert-danger"><small>Informasi Provinsi Tidak Boleh Kosong</small></div>';
            exit;
        }
        $id_region=$_POST['province'];
    } elseif($level=="National"){
        $id_region=null;
    }

    // Apabila Ada File Upload gambar (opsional)
    $access_foto = "";
    $path = "";
    if(!empty($_FILES['image_akses']['name'])){
        $nama_gambar    =$_FILES['image_akses']['name'];
        $ukuran_gambar  =$_FILES['image_akses']['size'];
        $tipe_gambar    =$_FILES['image_akses']['type'];
        $tmp_gambar     =$_FILES['image_akses']['tmp_name'];

        $ext            = pathinfo($nama_gambar, PATHINFO_EXTENSION);
        $access_foto    = generateRandomString(36).'.'.$ext;
        $path           ="../../assets/img/User/".$access_foto;

        if(in_array($tipe_gambar, ["image/jpeg","image/jpg","image/png","image/gif"])){
            if($ukuran_gambar < 2000000){
                if(!move_uploaded_file($tmp_gambar, $path)){
                    echo '<div class="alert alert-danger"><small>Upload file gagal!</small></div>';
                    exit;
                }
            }else{
                echo '<div class="alert alert-danger"><small>File gambar maksimal 2MB!</small></div>';
                exit;
            }
        }else{
            echo '<div class="alert alert-danger"><small>Tipe file tidak valid!</small></div>';
            exit;
        }
    }

    //Validasi Email Tidak Boleh Duplikat
    $validasi_email_duplikat = mysqli_num_rows(mysqli_query($Conn, "SELECT id_access FROM access WHERE access_email='$email_akses'"));
    if(!empty($validasi_email_duplikat)){
        if($path != "" && file_exists($path)) unlink($path);
        echo '<div class="alert alert-danger"><small>Email sudah terdaftar!</small></div>';
        exit;
    }

    //Jika Kontak Diisi lakukan validasi duplikat
    if(!empty($kontak_akses)){
        $validasi_kontak_duplikat = mysqli_num_rows(mysqli_query($Conn, "SELECT id_access FROM access WHERE access_contact='$kontak_akses'"));
        if(!empty($validasi_kontak_duplikat)){
            if($path != "" && file_exists($path)) unlink($path);
            echo '<div class="alert alert-danger"><small>Nomor kontak sudah terdaftar!</small></div>';
            exit;
        }
    }

    // === Gunakan Transaction untuk rollback jika gagal ===
    $Conn->begin_transaction();

    try {
        //Insert ke table access
        $stmt = $Conn->prepare("INSERT INTO access (id_access_group, access_name, access_email, access_contact, access_password, access_foto, access_client) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssss", $id_access_group, $nama_akses, $email_akses, $kontak_akses, $password_hash, $access_foto, $access_client);

        if(!$stmt->execute()){
            if($path != "" && file_exists($path)) unlink($path);
            throw new Exception("Gagal menyimpan data akses: ".$stmt->error);
        }

        $id_access = $stmt->insert_id; 
        $stmt->close();

        //Insert ke table access_client
        $stmt2 = $Conn->prepare("INSERT INTO access_client (id_access, id_region, level, keterangan) VALUES (?, ?, ?, ?)");
        $stmt2->bind_param("iiss", $id_access, $id_region, $level, $keterangan);

        if(!$stmt2->execute()){
            if($path != "" && file_exists($path)) unlink($path);
            throw new Exception("Gagal menyimpan data access_client: ".$stmt2->error);
        }

        $stmt2->close();

        //Jika semua berhasil → Commit
        $Conn->commit();

        //Kirim email jika 'Ya'
        if($kirim_email=="Ya"){
            $nama_tujuan    = $nama_akses;
            $email_tujuan   = $email_akses;
            $subjek         = "Credential Login - $app_title";
            $pesan          = '
            Kepada YTH. <b>'.$nama_akses.'</b> <br> 
            Berikut ini kami sampaikan credential akses ke aplikasi <b>'.$app_title.'</b> untuk dapat melakukan login dan mengubah password standar yang sudah ada.
            <p>
                <b>Email : </b> '.$email_akses.'<br>
                <b>Password : </b> '.$password.'<br>
                <b>URL Aplikasi : </b> '.$app_base_url.'<br>
            </p>
            ';

            $kirim_email=SendEmail($nama_tujuan,$email_tujuan,$subjek,$pesan,$email_gateway,$password_gateway,$url_provider,$nama_pengirim,$port_gateway,$url_service);
        }
        
        echo '<div class="alert alert-success"><small>Data Client <b id="NotifikasiTambahBerhasil">Berhasil</b> Disimpan</small></div>';

    } catch (Exception $e) {
        $Conn->rollback();
        echo '<div class="alert alert-danger"><small>'.$e->getMessage().'</small></div>';
    }
?>
