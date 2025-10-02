<?php
    //Zona Waktu
    date_default_timezone_set('Asia/Jakarta');

    //Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/SettingGeneral.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // Ambil protokol (http/https)
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' 
            || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

    // Ambil host (domain atau IP + port)
    $host = $_SERVER['HTTP_HOST'];

    // Ambil folder root project (level pertama setelah domain)
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $projectRoot = explode('/', trim($scriptName, '/'))[0]; // "PaySiswa"

    // Satukan jadi base URL root project
    $base_url_with_path = rtrim($protocol . $host . '/' . $projectRoot, '/');

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

    //Buat variabel
    $id_access=validateAndSanitizeInput($_POST['id_access']);

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
    }else{
        $Result = $Qry->get_result();
        $Data = $Result->fetch_assoc();
        $Qry->close();

        //Buat Variabel
        $access_name        =$Data['access_name'];
        $access_email       =$Data['access_email'];
        $access_foto_saya   =$Data['access_foto'];

        if(empty($Data['access_contact'])){
            $access_contact = "-";
        }else{
            $kontak = $Data['access_contact'];
            // Ganti 3 digit terakhir dengan ***
            if(strlen($kontak) > 3){
                $access_contact = substr($kontak, 0, -3) . '***';
            }else{
                // Jika panjang nomor <= 3, sembunyikan semuanya
                $access_contact = str_repeat('*', strlen($kontak));
            }
        }
        
        //Buka access_client
        $level          = GetDetailData($Conn, 'access_client', 'id_access', $id_access, 'level');
        $id_region      = GetDetailData($Conn, 'access_client', 'id_access', $id_access, 'id_region');
        $keterangan     = GetDetailData($Conn, 'access_client', 'id_access', $id_access, 'keterangan');
        if(empty($keterangan)){
            $keterangan="-";
        }

        //Buka region
        if(!empty($id_region)){
            $category       = GetDetailData($Conn, 'region', 'id_region', $id_region, 'category');
            $province_name  = GetDetailData($Conn, 'region', 'id_region', $id_region, 'province_name');
            $district_name  = GetDetailData($Conn, 'region', 'id_region', $id_region, 'district_name');
            if(empty($district_name)){
                $district_name  = "-";
            }
        }else{
            $category       = "-";
            $province_name  = "-";
            $district_name  = "-";
        }

        //Routing Level Label
        $level_label='<span class="badge bg-danger">None</span>';
        if($level=="National"){
            $level_label='<span class="badge bg-primary">Nasional</span>';
        }
        if($level=="Province"){
            $level_label='<span class="badge bg-info">Provinsi</span>';
        }
        if($level=="District"){
            $level_label='<span class="badge bg-success">Kab/Kota</span>';
        }

        //Routing URL image
        if(!empty($access_foto_saya)){
            $image_url= ''.$base_url_with_path.'/image_proxy.php?dir=User&filename='.$access_foto_saya;
        }else{
            $image_url= ''.$base_url_with_path.'/image_proxy.php?dir=User&filename=No-Image.png';
        }

        //Tampilkan Data
        echo '
            <div class="row mb-3">
                <div class="col-12 mb-3 text-center">
                    <img src="'.$image_url.'" alt="'.$access_foto_saya.'" width="70%" class="rounded-circle">
                </div>
            </div>
        ';
        echo '
            <div class="row mb-2">
                <div class="col-4"><small>Nama</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$access_name.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Email</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$access_email.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Kontak</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$access_contact.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Level</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$level_label.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Provinsi</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$province_name.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Kab/Kota</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$district_name.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Keterangan</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$keterangan.'</small>
                </div>
            </div>
        ';
    }
?>