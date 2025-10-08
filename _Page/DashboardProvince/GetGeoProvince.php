<?php
    include "../../_Config/Connection.php";

    // Ambil province_code dari request
    $province_code = $_GET['province_code'] ?? '';

    if(empty($province_code)){
        echo json_encode(["error" => "province_code kosong"]);
        exit;
    }

    // Ambil data geo_region + SUM kurang_guru per district_code
    $sql = "
        SELECT 
            g.province_code,
            g.province_name,
            g.district_code,
            g.district_name,
            g.coordinates,
            r.id_region,
            IFNULL(SUM(pr.kurang_guru),0) as kurang_guru
        FROM geo_region g
        LEFT JOIN region r 
            ON g.province_code = r.province_code 
        AND g.district_code = r.district_code
        LEFT JOIN position_region pr 
            ON pr.id_region = r.id_region
        WHERE g.province_code = ?
        GROUP BY g.province_code, g.province_name, g.district_code, g.district_name, g.coordinates
    ";

    $stmt = $Conn->prepare($sql);
    $stmt->bind_param("s", $province_code);
    $stmt->execute();
    $result = $stmt->get_result();

    $features = [];
    while($row = $result->fetch_assoc()){
        $geo = json_decode($row['coordinates'], true);
        if($geo && isset($geo['features'])){
            foreach($geo['features'] as &$f){
                // Tambahkan properties custom
                $f['properties']['id_region'] = $row['id_region'];
                $f['properties']['province_name'] = $row['province_name'];
                $f['properties']['district_name'] = $row['district_name'];
                $f['properties']['district_code'] = $row['district_code'];
                $f['properties']['kurang_guru'] = (int)$row['kurang_guru'];
            }
            $features = array_merge($features, $geo['features']);
        }
    }

    $response = [
        "type" => "FeatureCollection",
        "features" => $features
    ];

    header("Content-Type: application/json");
    echo json_encode($response);