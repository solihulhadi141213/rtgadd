<?php
    //Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";

    //Zona Waktu
    date_default_timezone_set('Asia/Jakarta');

    //Inisiasi variabel
    $process_datetime   = date('Y-m-d H:i:s');
    $process_name       = "Update Map Count";

    //Tentukan Directory GeoJson tingkat provinsi
    $directory_json_prov = "../../GeoJson/provinsi.json";

    //Tentukan Direktory File JSON yang akan di Update/Edit
    $directory_json_tujuan = "../../_Page/Dashboard/map_count.json";

    //Jika tidak ada file provinsi.json
    if (!file_exists($directory_json_prov)) {
        $simpan_log = CornJob($Conn, $process_datetime, $process_name, 'File JSON Provinsi tidak ditemukan');
        echo $simpan_log;
        exit;
    }
    
    //Baca file GeoJson Provinsi
    $jsonProvString = file_get_contents($directory_json_prov);
    $dataProv       = json_decode($jsonProvString, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        $error="Error parsing JSON: " . json_last_error_msg();
        $simpan_log = CornJob($Conn, $process_datetime, $process_name, $error);
        echo $simpan_log;
        exit;
    }

    $featuresProv = $dataProv['features'] ?? [];

    $outputData = [];

    foreach ($featuresProv as $prov) {
        $KODE_PROV  = $prov['properties']['KODE_PROV'] ?? '';
        $PROVINSI   = $prov['properties']['PROVINSI'] ?? '';

        if ($KODE_PROV != '') {
            //Ambil data agregat dari database
            $sql = "
                SELECT 
                    SUM(pr.abk) as ABK,
                    SUM(pr.jumlah_guru) as jumlah_guru,
                    SUM(pr.kurang_guru) as kurang_guru,
                    SUM(pr.kurang_asn) as kurang_asn
                FROM region r
                LEFT JOIN position_region pr ON r.id_region = pr.id_region
                WHERE r.province_code = ?
            ";
            $stmt = $Conn->prepare($sql);
            $stmt->bind_param("s", $KODE_PROV);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            $ABK          = (int)($result['ABK'] ?? 0);
            $jumlah_guru  = (int)($result['jumlah_guru'] ?? 0);
            $kurang_guru  = (int)($result['kurang_guru'] ?? 0);
            $kurang_asn   = (int)($result['kurang_asn'] ?? 0);

            $outputData[] = [
                "KODE_PROV"   => $KODE_PROV,
                "PROVINSI"    => $PROVINSI,
                "ABK"         => $ABK,
                "jumlah_guru" => $jumlah_guru,
                "kurang_guru" => $kurang_guru,
                "kurang_asn"  => $kurang_asn
            ];
        }
    }

    //Simpan ke file JSON tujuan
    $jsonOutput = json_encode($outputData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    if (file_put_contents($directory_json_tujuan, $jsonOutput)) {
        $simpan_log = CornJob($Conn, $process_datetime, $process_name, 'Berhasil update map_count.json');
        echo $simpan_log;
    } else {
        $simpan_log = CornJob($Conn, $process_datetime, $process_name, 'Gagal menyimpan file map_count.json');
        echo $simpan_log;
    }
?>
