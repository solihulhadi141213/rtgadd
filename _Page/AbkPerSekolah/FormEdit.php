<?php
    //Zona Waktu
    date_default_timezone_set('Asia/Jakarta');

    //Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/SettingGeneral.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    //Inisiasi Variabel id_position_school
    $id_position_school="";

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
    //Tangkap id_position_school
    if(empty($_POST['id_position_school'])){
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
    $id_position_school=validateAndSanitizeInput($_POST['id_position_school']);

    //Buka Data Jabatan Per Wilayah
    $Qry = $Conn->prepare("SELECT * FROM position_school WHERE id_position_school = ?");
    $Qry->bind_param("s", $id_position_school);
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
        $id_position_school     = $Data['id_position_school'];
        $id_school              = $Data['id_school'];
        $id_position            = $Data['id_position'];
        $abk                    = $Data['abk'];
        $asn                    = $Data['asn'];
        $PPPK2024               = $Data['PPPK2024'];
        $NonASN_sblmOkt2022     = $Data['NonASN_sblmOkt2022'];
        $NonASN_stlhOkt2022     = $Data['NonASN_stlhOkt2022'];
        $JmlGuru                = $Data['JmlGuru'];
        $KurangGuru             = $Data['KurangGuru'];
        $JmlASN                 = $Data['JmlASN'];
        $KrngASN                = $Data['KrngASN'];

        //Buka id_region
        $id_region          = GetDetailData($Conn, 'school', 'id_school', $id_school, 'id_region');

        //Nama Sekolah
        $school_name        = GetDetailData($Conn, 'school', 'id_school', $id_school, 'school_name');

        //Buka Nama Provinsi Dan Kabupaten
        $province_name      = GetDetailData($Conn, 'region', 'id_region', $id_region, 'province_name');
        $district_name      = GetDetailData($Conn, 'region', 'id_region', $id_region, 'district_name');

        //Nama Jabatan
        $position_name      = GetDetailData($Conn, 'position', 'id_position', $id_position, 'position_name');
        //Tampilkan Data
        echo '
            <input type="hidden" name="id_position_school" value="'.$id_position_school.'">
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
                <div class="col-4"><small>Sekolah</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$school_name.'</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4"><small>Jabatan</small></div>
                <div class="col-1"><small>:</small></div>
                <div class="col-7">
                    <small class="text text-grayish">'.$position_name.'</small>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="abk_edit"><small>ABK</small></label>
                </div>
                <div class="col-md-8">
                    <input type="number" name="abk" id="abk_edit" class="form-control" value="'.$abk.'">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="asn_edit"><small>ASN</small></label>
                </div>
                <div class="col-md-8">
                    <input type="number" name="asn" id="asn_edit" class="form-control" value="'.$asn.'">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="PPPK2024_edit"><small>PPPK 2024</small></label>
                </div>
                <div class="col-md-8">
                    <input type="number" name="PPPK2024" id="PPPK2024_edit" class="form-control" value="'.$PPPK2024.'">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="NonASN_sblmOkt2022_edit"><small>Non ASN Sebelum Oktober 2022</small></label>
                </div>
                <div class="col-md-8">
                    <input type="number" name="NonASN_sblmOkt2022" id="NonASN_sblmOkt2022_edit" class="form-control" value="'.$NonASN_sblmOkt2022.'">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="NonASN_stlhOkt2022_edit"><small>Non ASN Setelah Oktober 2022</small></label>
                </div>
                <div class="col-md-8">
                    <input type="number" name="NonASN_stlhOkt2022" id="NonASN_stlhOkt2022_edit" class="form-control" value="'.$NonASN_stlhOkt2022.'">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="JmlGuru_edit"><small>Jumlah Guru</small></label>
                </div>
                <div class="col-md-8">
                    <input type="number" name="JmlGuru" id="JmlGuru_edit" class="form-control" value="'.$JmlGuru.'">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="KurangGuru_edit"><small>Kurang Guru</small></label>
                </div>
                <div class="col-md-8">
                    <input type="number" name="KurangGuru" id="KurangGuru_edit" class="form-control" value="'.$KurangGuru.'">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="JmlASN_edit"><small>Jumlah ASN</small></label>
                </div>
                <div class="col-md-8">
                    <input type="number" name="JmlASN" id="JmlASN_edit" class="form-control" value="'.$JmlASN.'">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="KrngASN_edit"><small>Kurang ASN</small></label>
                </div>
                <div class="col-md-8">
                    <input type="number" name="KrngASN" id="KrngASN_edit" class="form-control" value="'.$KrngASN.'">
                </div>
            </div>
        ';
    }
?>