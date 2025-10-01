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
    if (empty($_POST['id_position_region'])) {
        echo '<div class="alert alert-danger"><small>ID Jabatan Per Wilayah tidak boleh kosong!</small></div>';
        exit;
    }
    // Sanitasi input
    $id_position_region     = validateAndSanitizeInput($_POST['id_position_region']);
    $abk                    = validateAndSanitizeInput($_POST['abk'] ?? 0);
    $asn                    = validateAndSanitizeInput($_POST['asn'] ?? 0);
    $asn_di_negeri          = validateAndSanitizeInput($_POST['asn_di_negeri'] ?? 0);
    $asn_di_swasta          = validateAndSanitizeInput($_POST['asn_di_swasta'] ?? 0);
    $NonASN_sblmOkt2022     = validateAndSanitizeInput($_POST['NonASN_sblmOkt2022'] ?? 0);
    $NonASN_stlhOkt2022     = validateAndSanitizeInput($_POST['NonASN_stlhOkt2022'] ?? 0);
    $pppk2024               = validateAndSanitizeInput($_POST['pppk2024'] ?? 0);
    $jumlah_guru            = validateAndSanitizeInput($_POST['jumlah_guru'] ?? 0);
    $kurang_guru            = validateAndSanitizeInput($_POST['kurang_guru'] ?? 0);
    $jumlah_asn             = validateAndSanitizeInput($_POST['jumlah_asn'] ?? 0);
    $kurang_asn             = validateAndSanitizeInput($_POST['kurang_asn'] ?? 0);

    // Proses Update
    $QryUpdate = $Conn->prepare("UPDATE position_region SET 
    abk=?, 
    asn=?, 
    asn_di_negeri=?, 
    asn_di_swasta=?, 
    NonASN_sblmOkt2022=?, 
    NonASN_stlhOkt2022=?, 
    pppk2024=?, 
    jumlah_guru=?, 
    kurang_guru=?, 
    jumlah_asn=?, 
    kurang_asn=? 
    WHERE id_position_region=?");
    $QryUpdate->bind_param(
        "iiiiiiiiiiii", 
        $abk, 
        $asn, 
        $asn_di_negeri, 
        $asn_di_swasta, 
        $NonASN_sblmOkt2022, 
        $NonASN_stlhOkt2022, 
        $pppk2024, 
        $jumlah_guru, 
        $kurang_guru, 
        $jumlah_asn, 
        $kurang_asn, 
        $id_position_region 
    );
    if(!$QryUpdate->execute()){
        echo '<div class="alert alert-danger"><small>Terjadi kesalahan saat update data ke database!</small></div>';
        exit;
    }
    $QryUpdate->close();

    // Sukses
    echo '
        <div class="alert alert-success">
            <small>Update data <b id="NotifikasiEditJabatanBerhasil">Berhasil</b> dilakukan!</small>
        </div>
    ';
?>
