<?php
require_once "../../_Config/Connection.php";

// Log execution
file_put_contents('cron_log.txt', "Cache refresh started: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

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

if(mysqli_query($Conn, $updateQuery)) {
    file_put_contents('cron_log.txt', "Cache refresh completed: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
} else {
    file_put_contents('cron_log.txt', "Cache refresh failed: " . mysqli_error($Conn) . "\n", FILE_APPEND);
}
?>