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

    // Sanitasi input dan buat variabelnya
    $province           = validateAndSanitizeInput($_POST['province']);
    $regency            = validateAndSanitizeInput($_POST['regency']);
    $department         = validateAndSanitizeInput($_POST['department']);
    $workload           = validateAndSanitizeInput($_POST['workload'] ?? 0);
    $officials_public   = validateAndSanitizeInput($_POST['officials_public'] ?? 0);
    $officials_private  = validateAndSanitizeInput($_POST['officials_private'] ?? 0);
    $manpower_gap       = validateAndSanitizeInput($_POST['manpower_gap'] ?? 0);
    
    //Validasi Data Duplikat
    $Qry = $Conn->prepare("SELECT * FROM position_region WHERE province = ? AND regency = ? AND department = ?");
    $Qry->bind_param("sss", $province, $regency, $department);
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
    if(!empty($Data['id_position_region'])){
        echo '
            <div class="alert alert-danger">
                <small>Data Yang Anda Input Sudah Terdaftar Sebelumnya (Provini, Kabupaten dan Jabatan Terdaftar)</small>
            </div>
        ';
        exit;
    }
    
    //Insert Data
    $stmt = $Conn->prepare("INSERT INTO position_region (province, regency, department, workload, officials_public, officials_private, manpower_gap) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $province, $regency, $department, $workload, $officials_public, $officials_private, $manpower_gap);
    if(!$stmt->execute()){
        echo '
            <div class="alert alert-danger">
                <small>Terjadi kesalahan pada saat insert data ke database</small>
            </div>
        ';
        exit;
    }
    $stmt->close();
    echo '
        <div class="alert alert-success">
            <small>Insert data <b id="NotifikasiTambahJabatanBerhasil">Success</span></small>
        </div>
    ';
?>
