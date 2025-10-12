<?php
    // Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";

    // Zona Waktu
    date_default_timezone_set('Asia/Jakarta');

    // Tentukan Directory
    $directory_json = "abk_school_level_province.json";

    // Inisialisasi Variabel outputData
    $outputData = [];

    // Looping geo_region berdasarkan level_region
    $query_province = mysqli_query($Conn, "SELECT province_code, province_name FROM region WHERE category='Province' ORDER BY province_code ASC");
    while ($data_province = mysqli_fetch_array($query_province)) {
        $province_code = $data_province['province_code'];
        $province_name = $data_province['province_name'];

        // Inisialisasi agregat Level 1 (per-provinsi)
        $abk_1 = 0;
        $asn_1 = 0;
        $PPPK2024_1 = 0;
        $NonASN_sblmOkt2022_1 = 0;
        $NonASN_stlhOkt2022_1 = 0;
        $JmlGuru_1 = 0;
        $KurangGuru_1 = 0;
        $JmlASN_1 = 0;
        $KrngASN_1 = 0;

        // Array untuk menampung data per jenjang sekolah
        $arry_by_school_level = [];

        // Looping school_level secara Distinct
        $query_school_level = mysqli_query($Conn, "SELECT DISTINCT school_level FROM school ORDER BY school_level ASC");
        while ($data_school_level = mysqli_fetch_array($query_school_level)) {
            $school_level = $data_school_level['school_level'];

            // Inisialisasi agregat Level 2 (per jenjang sekolah)
            $abk_2 = 0;
            $asn_2 = 0;
            $PPPK2024_2 = 0;
            $NonASN_sblmOkt2022_2 = 0;
            $NonASN_stlhOkt2022_2 = 0;
            $JmlGuru_2 = 0;
            $KurangGuru_2 = 0;
            $JmlASN_2 = 0;
            $KrngASN_2 = 0;

            // Looping semua district berdasarkan province_code
            $query_district = mysqli_query($Conn, "SELECT id_region FROM region WHERE category='District' AND province_code='$province_code' ORDER BY district_code ASC");
            while ($data_district = mysqli_fetch_array($query_district)) {
                $id_region = $data_district['id_region'];

                // Looping semua school berdasarkan id_region dan school_level
                $query_school = mysqli_query($Conn, "SELECT id_school FROM school WHERE id_region='$id_region' AND school_level='$school_level' ORDER BY id_school ASC");
                while ($data_school = mysqli_fetch_array($query_school)) {
                    $id_school = $data_school['id_school'];

                    // Looping semua position_school berdasarkan id_school
                    $query_position_school = mysqli_query($Conn, "SELECT * FROM position_school WHERE id_school='$id_school' ORDER BY id_position_school ASC");
                    while ($data_position_school = mysqli_fetch_array($query_position_school)) {
                        $abk = (int)$data_position_school['abk'];
                        $asn = (int)$data_position_school['asn'];
                        $PPPK2024 = (int)$data_position_school['PPPK2024'];
                        $NonASN_sblmOkt2022 = (int)$data_position_school['NonASN_sblmOkt2022'];
                        $NonASN_stlhOkt2022 = (int)$data_position_school['NonASN_stlhOkt2022'];
                        $JmlGuru = (int)$data_position_school['JmlGuru'];
                        $KurangGuru = (int)$data_position_school['KurangGuru'];
                        $JmlASN = (int)$data_position_school['JmlASN'];
                        $KrngASN = (int)$data_position_school['KrngASN'];

                        // Agregat Level 2
                        $abk_2 += $abk;
                        $asn_2 += $asn;
                        $PPPK2024_2 += $PPPK2024;
                        $NonASN_sblmOkt2022_2 += $NonASN_sblmOkt2022;
                        $NonASN_stlhOkt2022_2 += $NonASN_stlhOkt2022;
                        $JmlGuru_2 += $JmlGuru;
                        $KurangGuru_2 += $KurangGuru;
                        $JmlASN_2 += $JmlASN;
                        $KrngASN_2 += $KrngASN;
                    }
                }
            }

            // Bentuk array aggregate per school_level
            $aggregate_school_level = [
                "abk" => $abk_2,
                "asn" => $asn_2,
                "pppk_2024" => $PPPK2024_2,
                "non_asn_sebelum_oktober_2022" => $NonASN_sblmOkt2022_2,
                "non_asn_setelah_oktober_2022" => $NonASN_stlhOkt2022_2,
                "jumlah_guru" => $JmlGuru_2,
                "kurang_guru" => $KurangGuru_2,
                "jumlah_asn" => $JmlASN_2,
                "kurang_asn" => $KrngASN_2
            ];

            // Simpan ke array jenjang sekolah
            $arry_by_school_level[] = [
                "school_level" => $school_level,
                "aggregate" => $aggregate_school_level
            ];

            // Tambahkan ke agregat Level 1 (provinsi)
            $abk_1 += $abk_2;
            $asn_1 += $asn_2;
            $PPPK2024_1 += $PPPK2024_2;
            $NonASN_sblmOkt2022_1 += $NonASN_sblmOkt2022_2;
            $NonASN_stlhOkt2022_1 += $NonASN_stlhOkt2022_2;
            $JmlGuru_1 += $JmlGuru_2;
            $KurangGuru_1 += $KurangGuru_2;
            $JmlASN_1 += $JmlASN_2;
            $KrngASN_1 += $KrngASN_2;
        }

        // Bentuk aggregate Level 1 (provinsi)
        $aggregate_1 = [
            "abk" => $abk_1,
            "asn" => $asn_1,
            "pppk_2024" => $PPPK2024_1,
            "non_asn_sebelum_oktober_2022" => $NonASN_sblmOkt2022_1,
            "non_asn_setelah_oktober_2022" => $NonASN_stlhOkt2022_1,
            "jumlah_guru" => $JmlGuru_1,
            "kurang_guru" => $KurangGuru_1,
            "jumlah_asn" => $JmlASN_1,
            "kurang_asn" => $KrngASN_1
        ];

        // Masukkan hasil ke outputData
        $outputData[] = [
            "province_code" => $province_code,
            "province_name" => $province_name,
            "aggregate" => $aggregate_1,
            "school_level" => $arry_by_school_level
        ];
    }

    // Simpan hasil ke file JSON
    file_put_contents($directory_json, json_encode($outputData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    // Output hasil
    echo "<pre>";
    print_r($outputData);
    echo "</pre>";
?>
