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
            $jumlah_abk = 0;
            $jumlah_guru = 0;
            $kurang_guru = 0;
            $kurang_asn = 0;
            //Looping Tabel region
            $query_region = mysqli_query($Conn, "SELECT id_region, province_name FROM region WHERE category='District' AND province_code='$KODE_PROV'");
            while ($data_region = mysqli_fetch_array($query_region)) {
                $id_region = $data_region['id_region'];
                $province_name = $data_region['province_name'];
                
                //Looping school
                $query_school = mysqli_query($Conn, "SELECT id_school FROM school WHERE id_region='$id_region'");
                while ($data_school = mysqli_fetch_array($query_school)) {
                    $id_school = $data_school['id_school'];

                    //Looping position_school
                    $query_position_school = mysqli_query($Conn, "SELECT abk, JmlGuru, KrngASN, KurangGuru FROM position_school WHERE id_school='$id_school'");
                    while ($data_position_school = mysqli_fetch_array($query_position_school)) {
                        $abk = $data_position_school['abk'];
                        $JmlGuru = $data_position_school['JmlGuru'];
                        $KrngASN = $data_position_school['KrngASN'];
                        $KurangGuru = $data_position_school['KurangGuru'];

                        //Akumulasi
                        $jumlah_abk = $jumlah_abk+$abk;
                        $jumlah_guru = $jumlah_guru+$JmlGuru;
                        $kurang_guru = $kurang_guru+$KurangGuru;
                        $kurang_asn = $kurang_asn+$KrngASN;
                        
                    }
                }
            }
            if(empty($jumlah_abk)){
                $persentase_kebutuhan_guru = 0;
            }else{
                $persentase_kebutuhan_guru = ($kurang_guru/$jumlah_abk)*100;
            }
            
            $persentase_kebutuhan_guru_fix = round($persentase_kebutuhan_guru);

            $outputData[] = [
                "KODE_PROV"   => $KODE_PROV,
                "PROVINSI"    => $PROVINSI,
                "ABK"         => $jumlah_abk,
                "jumlah_guru" => $jumlah_guru,
                "kurang_guru" => $kurang_guru,
                "kurang_asn"  => $kurang_asn,
                "persentase_kebutuhan_guru"  => $persentase_kebutuhan_guru_fix,
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
