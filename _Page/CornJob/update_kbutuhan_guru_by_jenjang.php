<?php
    // Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";

    // Zona Waktu
    date_default_timezone_set('Asia/Jakarta');

    // Inisiasi variabel
    $process_datetime   = date('Y-m-d H:i:s');
    $process_name       = "Update Kebutuhan Guru By Jenjang";

    // Tentukan Directory Tujuan
    $json_tujuan = "../../_Page/DashboardProvince/kebutuhan_guru_by_jenjang.json";

    // Query ambil kebutuhan guru per provinsi & jenjang
    $sql = "
        SELECT 
            r.province_code,
            r.province_name,
            s.school_level AS jenjang,
            SUM(ps.KurangGuru) AS kebutuhan
        FROM region r
        JOIN school s ON r.id_region = s.id_region
        JOIN position_school ps ON s.id_school = ps.id_school
        GROUP BY r.province_code, r.province_name, s.school_level
        ORDER BY r.province_code, s.school_level
    ";
    $result = mysqli_query($Conn, $sql);

    $data = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $province_code = $row['province_code'];
            $province_name = $row['province_name'];
            $jenjang       = $row['jenjang'];
            $kebutuhan     = (int)$row['kebutuhan'];

            // Cek apakah provinsi sudah ada di array
            if (!isset($data[$province_code])) {
                $data[$province_code] = [
                    "province_code" => $province_code,
                    "province_name" => $province_name,
                    "kebutuhan_guru_by_jenjang" => []
                ];
            }

            // Tambahkan jenjang ke dalam provinsi
            $data[$province_code]["kebutuhan_guru_by_jenjang"][] = [
                "jenjang" => $jenjang,
                "kebutuhan" => $kebutuhan
            ];
        }
    }

    // Ubah ke array numerik
    $output = array_values($data);

    // Encode ke JSON dengan format rapi
    $json_data = json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    // Simpan ke file
    if (file_put_contents($json_tujuan, $json_data)) {
        echo "JSON berhasil diperbarui di: " . $json_tujuan;
    } else {
        echo "Gagal menyimpan file JSON.";
    }
?>
