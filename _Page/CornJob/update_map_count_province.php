<?php
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";

    date_default_timezone_set('Asia/Jakarta');
    $process_datetime   = date('Y-m-d H:i:s');
    $process_name       = "Update Map Count Province";
    $directory_json_tujuan = "../../_Page/DashboardProvince/map_count_by_province.json";

    // Query region
    $output=[];
    $query_province = mysqli_query($Conn, "SELECT*FROM region WHERE category='Province'");
    while ($data_province = mysqli_fetch_array($query_province)) {
            $id_region_province     = $data_province['id_region'];
            $province_code          = $data_province['province_code'];
            $province_code_dapodik  = $data_province['province_code_dapodik'];
            $province_name          = $data_province['province_name'];

            //Sekarang looping District
            $abk_province = 0;
            $asn_province = 0;
            $district_arry=[];
            $query_district = mysqli_query($Conn, "SELECT*FROM region WHERE category='District' AND province_code='$province_code'");
            while ($data_district = mysqli_fetch_array($query_district)) {
                $id_region_district     = $data_district['id_region'];
                $district_code          = $data_district['district_code'];
                $district_code_dapodik  = $data_district['district_code_dapodik'];
                $district_name          = $data_district['district_name'];
                $code_map               = $data_district['code_map'];

                //Mencari nilai abk, asn dll
                $abk_district = (int) GetDetailData($Conn, 'position_region', 'id_region', $id_region_district, 'abk');
                $asn_district = (int) GetDetailData($Conn, 'position_region', 'id_region', $id_region_district, 'asn');

                //Plus-plus
                $abk_province = $abk_province+$abk_district;
                $asn_province = $asn_province+$asn_district;

                $district_arry[]=[
                    "OBJECTID" => $code_map,
                    "district_code" => $district_code,
                    "district_code_dapodik" => $district_code_dapodik,
                    "district_name" => $district_name,
                    "abk" => $abk_district,
                    "asn" => $asn_district,
                ];
            }


            $output[]=[
                "KODE_PROV" => $province_code,
                "province_code" => $province_code,
                "province_code_dapodik" => $province_code_dapodik,
                "province_name" => $province_name,
                "abk" => $abk_province,
                "asn" => $asn_province,
                "district" => $district_arry
            ];
    }

    file_put_contents($directory_json_tujuan, json_encode($output, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
    $simpan_log = CornJob($Conn, $process_datetime, $process_name, "Update JSON sukses. Total provinsi: ".count($output));
    echo $simpan_log;
?>