<?php
    header('Content-Type: application/json');

    // Koneksi & Session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // Validasi Sesi
    if (empty($SessionIdAccess)) {
        echo json_encode([
            "code" => 201,
            "message" => "Sesi akses sudah berakhir. Silahkan <b>login</b> ulang!",
            "metadata" => []
        ], JSON_PRETTY_PRINT);
        exit;
    }

    $metadata = [];

    // Loop semua provinsi
    $query_geo_region = mysqli_query($Conn, "SELECT province_code, province_name FROM geo_region WHERE level_region='Province'");
    while ($data_geo_region = mysqli_fetch_assoc($query_geo_region)) {
        $province_code  = $data_geo_region['province_code'];
        $province_name  = $data_geo_region['province_name'];

        // Inisialisasi akumulasi
        $abk = $asn = $jumlah_guru = $kurang_guru = $kurang_asn = 0;

        // Loop semua district di provinsi
        $query_region = mysqli_query($Conn, "SELECT id_region FROM region WHERE category='District' AND province_code='$province_code'");
        while ($data_region = mysqli_fetch_assoc($query_region)) {
            $id_region = $data_region['id_region'];

            // Loop posisi guru di district
            $query_position_region = mysqli_query($Conn, "SELECT abk, asn, jumlah_guru, kurang_guru, kurang_asn FROM position_region WHERE id_region='$id_region'");
            while ($data_position_region = mysqli_fetch_assoc($query_position_region)) {
                $abk            += (int)$data_position_region['abk'];
                $asn            += (int)$data_position_region['asn'];
                $jumlah_guru    += (int)$data_position_region['jumlah_guru'];
                $kurang_guru    += (int)$data_position_region['kurang_guru'];
                $kurang_asn     += (int)$data_position_region['kurang_asn'];
            }
        }

        // Push hasil tiap provinsi ke array
        $metadata[] = [
            "KODE_PROV"   => $province_code,
            "PROVINSI"    => $province_name,
            "ABK"         => $abk,
            "jumlah_guru" => $jumlah_guru,
            "kurang_guru" => $kurang_guru,
            "kurang_asn"  => $kurang_asn
        ];
    }

    // Output JSON
    echo json_encode([
        "code" => 200,
        "message" => "success",
        "metadata" => $metadata
    ], JSON_PRETTY_PRINT);
?>
