<?php
    // Koneksi
    include "../../_Config/Connection.php";

    // Set header JSON
    header('Content-Type: application/json');

    // Siapkan variabel default
    $response = [
        "client" => "200",
        "school" => "4.500",
        "teacher" => "20.000",
        "position" => "1.000"
    ];

    // Output JSON
    echo json_encode($response);

?>