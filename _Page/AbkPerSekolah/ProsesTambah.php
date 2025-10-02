<?php
    //Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    //Validasi Session Akses
    if(empty($SessionIdAccess)){
        echo '
            <div class="alert alert-danger">
                <small>Sesi akses sudah berakhir. Silahkan <b>Login</b> ulang!</small>
            </div>
        ';
        exit;
    }

    //Validasi Form Wajib Diisi
    if(empty($_POST['province_code'])){
        echo '
            <div class="alert alert-danger">
                <small>Provinsi Tidak Boleh Kosong. Silahkan pilih terlebih dulu!</small>
            </div>
        ';
        exit;
    }
    if(empty($_POST['district_code'])){
        echo '
            <div class="alert alert-danger">
                <small>Provinsi Tidak Boleh Kosong. Silahkan pilih terlebih dulu!</small>
            </div>
        ';
        exit;
    }
    if(empty($_POST['npsn'])){
        echo '
            <div class="alert alert-danger">
                <small>Sekolah Tidak Boleh Kosong. Silahkan Isi Terlebih Dulu!</small>
            </div>
        ';
        exit;
    }
    if(empty($_POST['position_code'])){
        echo '
            <div class="alert alert-danger">
                <small>Jabatan Tidak Boleh Kosong. Silahkan Isi Terlebih Dulu!</small>
            </div>
        ';
        exit;
    }

    //Buat Variabel Dan Sanitasi
    $province_code      = validateAndSanitizeInput($_POST['province_code']);
    $district_code      = validateAndSanitizeInput($_POST['district_code']);
    $npsn               = validateAndSanitizeInput($_POST['npsn']);
    $position_code      = validateAndSanitizeInput($_POST['position_code']);

    //Data tidak wajib
    $abk                    = validateAndSanitizeInput($_POST['abk'] ?? 0);
    $asn                    = validateAndSanitizeInput($_POST['asn'] ?? 0);
    $PPPK2024               = validateAndSanitizeInput($_POST['PPPK2024'] ?? 0);
    $NonASN_sblmOkt2022     = validateAndSanitizeInput($_POST['NonASN_sblmOkt2022'] ?? 0);
    $NonASN_stlhOkt2022     = validateAndSanitizeInput($_POST['NonASN_stlhOkt2022'] ?? 0);
    $JmlGuru                = validateAndSanitizeInput($_POST['JmlGuru'] ?? 0);
    $KurangGuru             = validateAndSanitizeInput($_POST['KurangGuru'] ?? 0);
    $JmlASN                 = validateAndSanitizeInput($_POST['JmlASN'] ?? 0);
    $KrngASN                = validateAndSanitizeInput($_POST['KrngASN'] ?? 0);

    //id_school dari npsn
    $id_school= GetDetailData($Conn, 'school','npsn', $npsn, 'id_school');

    // id_position dari position_code
    $id_position= GetDetailData($Conn, 'position','position_code', $position_code, 'id_position');

    //Validasi Duplikat
    $Qry = $Conn->prepare("SELECT * FROM position_school WHERE id_school = ? AND id_position = ?");
    $Qry->bind_param("ii", $id_school, $id_position);
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
    if(!empty($Data['id_position_school'])){

    //Jika Sudah Ada Lakukan Update data
        $update = $Conn->prepare("UPDATE position_school SET 
            abk=?, 
            asn=?, 
            PPPK2024=?, 
            NonASN_sblmOkt2022=?, 
            NonASN_stlhOkt2022=?, 
            JmlGuru=?, 
            KurangGuru=?,
            JmlASN=?,
            KrngASN=?
        WHERE id_school=? AND id_position=?");
        $update->bind_param("iiiiiiiiiii", 
            $abk, 
            $asn, 
            $PPPK2024, 
            $NonASN_sblmOkt2022, 
            $NonASN_stlhOkt2022, 
            $JmlGuru, 
            $KurangGuru, 
            $id_school, 
            $id_position,
            $JmlASN,
            $KrngASN
        );
        if($update->execute()){
            echo '
                <div class="alert alert-success">
                    <small>Insert data <b id="NotifikasiTambahBerhasil">Berhasil</span></small>
                </div>
            ';
            exit;
        }else{
            echo '
                <div class="alert alert-danger">
                    <small>Terjadi kesalahan pada saat update data</small>
                </div>
            ';
            exit;
        }
    }else{
        //Jika Belum Ada Lakukan Insert
        $insert = $Conn->prepare("INSERT INTO position_school (
            id_school, 
            id_position, 
            abk, 
            asn, 
            PPPK2024, 
            NonASN_sblmOkt2022, 
            NonASN_stlhOkt2022, 
            JmlGuru, 
            KurangGuru,
            JmlASN,
            KrngASN
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insert->bind_param(
            "iiiiiiiiiii", 
            $id_school, 
            $id_position, 
            $abk, 
            $asn, 
            $PPPK2024, 
            $NonASN_sblmOkt2022, 
            $NonASN_stlhOkt2022, 
            $JmlGuru, 
            $KurangGuru,
            $JmlASN,
            $KrngASN
        );
        if($insert->execute()){
            echo '
                <div class="alert alert-success">
                    <small>Insert data <b id="NotifikasiTambahBerhasil">Berhasil</span></small>
                </div>
            ';
            exit;
        }else{
            echo '
                <div class="alert alert-danger">
                    <small>Terjadi kesalahan pada saat insert data</small>
                </div>
            ';
            exit;
        }
    }
?>
