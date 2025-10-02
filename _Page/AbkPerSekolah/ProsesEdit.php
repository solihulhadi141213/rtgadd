<?php
    //Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    //Time Zone
    date_default_timezone_set('Asia/Jakarta');
    $now = date('Y-m-d H:i:s');

    // --- Validasi singkat ---
    if (empty($SessionIdAccess)) {
        echo '<div class="alert alert-danger"><small>Sesi Akses Sudah Berakhir! Silahkan Login Ulang!</small></div>';
        exit;
    }

    //Validasi Wajib
    if (empty($_POST['id_position_school'])) {
        echo '<div class="alert alert-danger"><small>ID Jabatan Per Wilayah tidak boleh kosong!</small></div>';
        exit;
    }
    // Sanitasi input
    $id_position_school     = validateAndSanitizeInput($_POST['id_position_school']);
    $abk                    = validateAndSanitizeInput($_POST['abk'] ?? 0);
    $asn                    = validateAndSanitizeInput($_POST['asn'] ?? 0);
    $PPPK2024               = validateAndSanitizeInput($_POST['PPPK2024'] ?? 0);
    $NonASN_sblmOkt2022     = validateAndSanitizeInput($_POST['NonASN_sblmOkt2022'] ?? 0);
    $NonASN_stlhOkt2022     = validateAndSanitizeInput($_POST['NonASN_stlhOkt2022'] ?? 0);
    $JmlGuru                = validateAndSanitizeInput($_POST['JmlGuru'] ?? 0);
    $KurangGuru             = validateAndSanitizeInput($_POST['KurangGuru'] ?? 0);
    $JmlASN                 = validateAndSanitizeInput($_POST['JmlASN'] ?? 0);
    $KrngASN                = validateAndSanitizeInput($_POST['KrngASN'] ?? 0);

    // Proses Update
    $QryUpdate = $Conn->prepare("UPDATE position_school SET 
    abk=?, 
    asn=?, 
    PPPK2024=?, 
    NonASN_sblmOkt2022=?, 
    NonASN_stlhOkt2022=?, 
    JmlGuru=?, 
    KurangGuru=?,
    JmlASN=?,
    KrngASN=?
    WHERE id_position_school=?");
    $QryUpdate->bind_param(
        "iiiiiiiiii", 
        $abk, 
        $asn, 
        $PPPK2024, 
        $NonASN_sblmOkt2022, 
        $NonASN_stlhOkt2022, 
        $JmlGuru, 
        $KurangGuru, 
        $JmlASN, 
        $KrngASN, 
        $id_position_school 
    );
    if(!$QryUpdate->execute()){
        echo '<div class="alert alert-danger"><small>Terjadi kesalahan saat update data ke database!</small></div>';
        exit;
    }
    $QryUpdate->close();

    // Sukses
    echo '
        <div class="alert alert-success">
            <small>Update data <b id="NotifikasiEditBerhasil">Berhasil</b> dilakukan!</small>
        </div>
    ';
?>
