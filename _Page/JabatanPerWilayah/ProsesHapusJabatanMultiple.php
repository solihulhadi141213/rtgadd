<?php
    //Zona Waktu
    date_default_timezone_set('Asia/Jakarta');

    //Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/SettingGeneral.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    //Validasi Sesi Akses
    if (empty($SessionIdAccess)) {
        echo '
            <div class="alert alert-danger">
                <small>Sesi Akses Sudah Berakhir. Silahkan <b>Login</b> ulang!</small>
            </div>
        ';
        exit;
    }

    //Tangkap id_position_region
    if (empty($_POST['id_position_region']) || count($_POST['id_position_region']) == 0) {
        echo '
            <div class="alert alert-danger">
                <small>Tidak ada data yang dipilih. Silahkan pilih data yang ingin dihapus terlebih dulu.</small>
            </div>
        ';
        exit;
    }

    $jumlah_data = count($_POST['id_position_region']);

    try {
        // Mulai transaksi
        $Conn->begin_transaction();

        foreach ($_POST['id_position_region'] as $id_position_region) {
            // Cek dulu apakah data ada
            $Qry = $Conn->prepare("SELECT id_position_region FROM position_region WHERE id_position_region = ?");
            $Qry->bind_param("s", $id_position_region);
            $Qry->execute();
            $Result = $Qry->get_result();
            $Qry->close();

            if ($Result->num_rows == 0) {
                // Jika data tidak ada → rollback
                throw new Exception("Data dengan ID $id_position_region tidak ditemukan.");
            }

            // Hapus data
            $QryDel = $Conn->prepare("DELETE FROM position_region WHERE id_position_region = ?");
            $QryDel->bind_param("s", $id_position_region);
            if (!$QryDel->execute()) {
                throw new Exception("Gagal menghapus data dengan ID $id_position_region");
            }
            $QryDel->close();
        }

        // Jika semua berhasil → commit
        $Conn->commit();
        echo '
            <div class="alert alert-success">
                <small>Hapus data jabatan per wilayah <b id="NotifikasiHapusJabatanMultipleBerhasil">Berhasil</b> dilakukan!</small>
            </div>
        ';

    } catch (Exception $e) {
        // Jika ada error → rollback
        $Conn->rollback();
        echo '
            <div class="alert alert-danger">
                <small>Hapus data jabatan per wilayah gagal dilakukan!<br>Keterangan: '.$e->getMessage().'</small>
            </div>
        ';
    }
?>
