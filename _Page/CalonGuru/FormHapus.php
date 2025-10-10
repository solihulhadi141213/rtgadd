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
    //Tangkap id_calon_guru
    if(empty($_POST['id_calon_guru'])){
         echo '
            <div class="alert alert-danger">
                <small>
                    ID Calon Guru (PPG) Tidak Boleh Kosong!
                </small>
            </div>
        ';
        exit;
    }

    //Buat variabel
    $id_calon_guru=validateAndSanitizeInput($_POST['id_calon_guru']);

    //Buka Data Jabatan Per Wilayah
    $Qry = $Conn->prepare("SELECT * FROM calon_guru WHERE id_calon_guru = ?");
    $Qry->bind_param("i", $id_calon_guru);
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
        $id_region          = isset($Data['id_region']) && $Data['id_region'] != '' ? $Data['id_region'] : 0;
        $ptkid              = isset($Data['ptkid']) && $Data['ptkid'] != '' ? $Data['ptkid'] : '-';
        $status_asn         = isset($Data['status_asn']) && $Data['status_asn'] != '' ? $Data['status_asn'] : '-';
        $instansi_kelulusan = isset($Data['instansi_kelulusan']) && $Data['instansi_kelulusan'] != '' ? $Data['instansi_kelulusan'] : '-';
        $jabatan_kelulusan  = isset($Data['jabatan_kelulusan']) && $Data['jabatan_kelulusan'] != '' ? $Data['jabatan_kelulusan'] : '-';
        $umur               = isset($Data['umur']) && $Data['umur'] != '' ? $Data['umur'] : '-';
        $pulau              = isset($Data['pulau']) && $Data['pulau'] != '' ? $Data['pulau'] : '-';
        $perguruan_tinggi_s1= isset($Data['perguruan_tinggi_s1']) && $Data['perguruan_tinggi_s1'] != '' ? $Data['perguruan_tinggi_s1'] : '-';
        $program_studi_s1   = isset($Data['program_studi_s1']) && $Data['program_studi_s1'] != '' ? $Data['program_studi_s1'] : '-';
        $bidang_studi_ppg   = isset($Data['bidang_studi_ppg']) && $Data['bidang_studi_ppg'] != '' ? $Data['bidang_studi_ppg'] : '-';
        $lptk               = isset($Data['lptk']) && $Data['lptk'] != '' ? $Data['lptk'] : '-';
        $ppg_blm_diangkat   = isset($Data['ppg_blm_diangkat']) && $Data['ppg_blm_diangkat'] != '' ? $Data['ppg_blm_diangkat'] : '-';

        //Buka Provinsi dan Kab/Kota
        $province_name      = GetDetailData($Conn, 'region', 'id_region', $id_region, 'province_name');
        $district_name      = GetDetailData($Conn, 'region', 'id_region', $id_region, 'district_name');

        //Tampilkan Data
        echo '
            <input type="hidden" name="id_calon_guru" value="'.$id_calon_guru.'">
            <div class="row mb-2">
                <div class="col-4"><small>PTKID</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$ptkid.'</small>
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
                <div class="col-4"><small>Pulau</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$pulau.' Tahun</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Instansi Keluusan</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$instansi_kelulusan.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Jabatan Kelulusan</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$jabatan_kelulusan.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Usia</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$umur.' Tahun</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Perguruan Tinggi (S1)</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$perguruan_tinggi_s1.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Program Studi</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$program_studi_s1.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Bidang Studi</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$bidang_studi_ppg.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>LPTK</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$lptk.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Status Pengangkatan ASN</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$ppg_blm_diangkat.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-12">
                    <div class="alert alert-warning">
                        <small>Apakah anda yakin akan menghapus data ini?</small>
                    </div>
                </div>
            </div>
        ';
    }
?>