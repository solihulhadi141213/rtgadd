<?php
    //Inisiasi Type File
    header('Content-Type: application/json');

    //Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    //Validasi Sesi akses
    if (empty($SessionIdAccess)) {
        $response = [
            "code" => 201,
            "message" => "Sesi akses sudah berakhir. Silahkan <b>login</b> ulang!",
            "count" => NULL
        ];
        echo json_encode($response, JSON_PRETTY_PRINT);
        exit;
    }

    //Validasi district_code
    if(empty($_POST['district_code'])){
        $response = [
            "code" => 201,
            "message" => "Parameter <b>Kode Kab/Kota</b> tidak boleh kosong!",
            "count" => NULL
        ];
        echo json_encode($response, JSON_PRETTY_PRINT);
        exit;
    }

    $district_code = mysqli_real_escape_string($Conn, $_POST['district_code']);

    // Query langsung join + agregasi
    $sql = "
        SELECT SUM(pr.kurang_guru) AS total_kurang
        FROM region r
        LEFT JOIN position_region pr ON r.id_region = pr.id_region
        WHERE r.category='District' AND r.district_code='$district_code'
    ";
    $query = mysqli_query($Conn, $sql);
    $data = mysqli_fetch_assoc($query);

    // Ambil hasil (jika null → 0)
    $kurang_guru = (int)($data['total_kurang'] ?? 0);

    //Buat Response
    $response = [
        "code" => 200,
        "message" => "Success",
        "count" => $kurang_guru
    ];
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
?>
