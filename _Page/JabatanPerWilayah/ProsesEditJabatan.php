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
    if (empty($_POST['province'])) {
        echo '<div class="alert alert-danger"><small>Provinsi tidak boleh kosong!</small></div>';
        exit;
    }
    if (empty($_POST['regency'])) {
        echo '<div class="alert alert-danger"><small>Kabupaten/Kota tidak boleh kosong!</small></div>';
        exit;
    }
    if (empty($_POST['department'])) {
        echo '<div class="alert alert-danger"><small>Posisi/Jabatan tidak boleh kosong!</small></div>';
        exit;
    }

    // Sanitasi input
    $id_position_region = validateAndSanitizeInput($_POST['id_position_region']);
    $province           = validateAndSanitizeInput($_POST['province']);
    $regency            = validateAndSanitizeInput($_POST['regency']);
    $department         = validateAndSanitizeInput($_POST['department']);
    $workload           = validateAndSanitizeInput($_POST['workload'] ?? 0);
    $officials_public   = validateAndSanitizeInput($_POST['officials_public'] ?? 0);
    $officials_private  = validateAndSanitizeInput($_POST['officials_private'] ?? 0);
    $manpower_gap       = validateAndSanitizeInput($_POST['manpower_gap'] ?? 0);

    // Ambil data lama
    $QryOld = $Conn->prepare("SELECT province, regency, department FROM position_region WHERE id_position_region = ?");
    $QryOld->bind_param("s", $id_position_region);
    if(!$QryOld->execute()){
        echo '<div class="alert alert-danger"><small>Terjadi kesalahan membuka data lama!</small></div>';
        exit;
    }
    $ResultOld = $QryOld->get_result();
    $DataOld = $ResultOld->fetch_assoc();
    $QryOld->close();

    if(!$DataOld){
        echo '<div class="alert alert-danger"><small>Data lama tidak ditemukan!</small></div>';
        exit;
    }

    $old_province   = $DataOld['province'];
    $old_regency    = $DataOld['regency'];
    $old_department = $DataOld['department'];

    // Jika kombinasi province, regency, department berbeda → validasi duplikat
    if ($province !== $old_province || $regency !== $old_regency || $department !== $old_department) {
        $Qry = $Conn->prepare("SELECT id_position_region FROM position_region WHERE province = ? AND regency = ? AND department = ?");
        $Qry->bind_param("sss", $province, $regency, $department);
        if (!$Qry->execute()) {
            $error = $Conn->error;
            echo '<div class="alert alert-danger"><small>Kesalahan validasi duplikat!<br>Keterangan: '.$error.'</small></div>';
            exit;
        }
        $Result = $Qry->get_result();
        $Data = $Result->fetch_assoc();
        $Qry->close();

        if(!empty($Data['id_position_region'])){
            echo '<div class="alert alert-danger"><small>Data sudah terdaftar sebelumnya (Provinsi, Kabupaten, Jabatan sama)</small></div>';
            exit;
        }
    }

    // Proses Update
    $QryUpdate = $Conn->prepare("UPDATE position_region SET province=?, regency=?, department=?, workload=?, officials_public=?, officials_private=?, manpower_gap=? WHERE id_position_region=?");
    $QryUpdate->bind_param("ssssssss", $province, $regency, $department, $workload, $officials_public, $officials_private, $manpower_gap, $id_position_region);
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
