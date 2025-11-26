<?php
include "../../_Config/Connection.php";
include "../../_Config/GlobalFunction.php";

function refreshAggregatedData($Conn) {
    $updateQuery = "
        INSERT INTO stats_kabkota_aggregated (district_code, province_name, district_name, jumlah_sekolah, jumlah_kebutuhan_guru)
        SELECT 
            gr.district_code,
            gr.province_name,
            gr.district_name,
            COUNT(DISTINCT s.id_school) as jumlah_sekolah,
            COALESCE(SUM(ps.KurangGuru), 0) as jumlah_kebutuhan_guru
        FROM geo_region gr
        LEFT JOIN region r ON r.district_code = gr.district_code AND r.category = 'District'
        LEFT JOIN school s ON s.id_region = r.id_region
        LEFT JOIN position_school ps ON ps.id_school = s.id_school
        WHERE gr.level_region = 'District'
        GROUP BY gr.district_code, gr.province_name, gr.district_name
        ON DUPLICATE KEY UPDATE 
            province_name = VALUES(province_name),
            district_name = VALUES(district_name),
            jumlah_sekolah = VALUES(jumlah_sekolah),
            jumlah_kebutuhan_guru = VALUES(jumlah_kebutuhan_guru),
            last_updated = CURRENT_TIMESTAMP
    ";
    
    $result = mysqli_query($Conn, $updateQuery);
    if($result) {
        echo "Cache refreshed successfully! " . mysqli_affected_rows($Conn) . " records updated.";
    } else {
        echo "Error: " . mysqli_error($Conn);
    }
}

refreshAggregatedData($Conn);
?>