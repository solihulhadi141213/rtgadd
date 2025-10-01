<?php
    //Zona Waktu
    date_default_timezone_set('Asia/Jakarta');

    //Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/SettingGeneral.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    //Inisiasi Variabel id_position_region
    $id_position_region="";

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
    //Tangkap id_position_region
    if(empty($_POST['id_position_region'])){
         echo '
            <div class="alert alert-danger">
                <small>
                    ID Jabatan Per Wilayah Tidak Boleh Kosong!
                </small>
            </div>
        ';
        exit;
    }

    //Buat variabel
    $id_position_region=validateAndSanitizeInput($_POST['id_position_region']);

    //Buka Data Jabatan Per Wilayah
    $Qry = $Conn->prepare("SELECT * FROM position_region WHERE id_position_region = ?");
    $Qry->bind_param("s", $id_position_region);
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
        $id_region          = $Data['id_region'];
        $id_position        = $Data['id_position'];
        $abk                = $Data['abk'];
        $asn                = $Data['asn'];
        $asn_di_negeri      = $Data['asn_di_negeri'];
        $asn_di_swasta      = $Data['asn_di_swasta'];
        $NonASN_sblmOkt2022 = $Data['NonASN_sblmOkt2022'];
        $NonASN_stlhOkt2022 = $Data['NonASN_stlhOkt2022'];
        $pppk2024           = $Data['pppk2024'];
        $jumlah_guru        = $Data['jumlah_guru'];
        $kurang_guru        = $Data['kurang_guru'];
        $jumlah_asn         = $Data['jumlah_asn'];
        $kurang_asn         = $Data['kurang_asn'];

        //Buka Provinsi dan Kab/Kota
        $province_name      = GetDetailData($Conn, 'region', 'id_region', $id_region, 'province_name');
        $district_name      = GetDetailData($Conn, 'region', 'id_region', $id_region, 'district_name');

        //Buka Ddata Jabatan
        $position_name      = GetDetailData($Conn, 'position', 'id_position', $id_position, 'position_name');
        //Tampilkan Data
        echo '
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
                <div class="col-4"><small>Jabatan</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$position_name.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>AKB</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$abk.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>ASN</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$asn.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>ASN-Sekolah Negeri</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$asn_di_negeri.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>ASN-Sekolah Swasta</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$asn_di_swasta.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Non ASN Sebelum Oktober 2022</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$NonASN_sblmOkt2022.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Non ASN Sesudah Oktober 2022</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$NonASN_stlhOkt2022.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>PPPK 2024</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$pppk2024.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Jumlah Guru</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$jumlah_guru.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Kurang Guru</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$kurang_guru.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Jumlah ASN</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$jumlah_asn.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Kekurangan ASN</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$kurang_asn.'</small>
                </div>
            </div>
        ';
    }
?>