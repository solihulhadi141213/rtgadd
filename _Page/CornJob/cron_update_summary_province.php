<?php
    /**
     * CRON JOB — Update Summary Province
     * ----------------------------------
     * File ini:
     * 1. Membuat tabel summary_province jika belum ada
     * 2. Mengisi ulang datanya berdasarkan perhitungan terbaru
     */

    // Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";

    // Zona Waktu
    date_default_timezone_set('Asia/Jakarta');

    echo "=== START UPDATE SUMMARY PROVINCE ===\n";

    // 1. BUAT TABEL JIKA BELUM ADA
    $sql_create = "
    CREATE TABLE IF NOT EXISTS summary_province (
        province_code VARCHAR(10) PRIMARY KEY,
        province_name VARCHAR(255),
        jumlah_kabkota INT DEFAULT 0,
        jumlah_sekolah INT DEFAULT 0,
        total_kebutuhan_guru INT DEFAULT 0,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP 
            ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";

    if (!mysqli_query($Conn, $sql_create)) {
        die("ERROR Membuat Tabel: " . mysqli_error($Conn) . "\n");
    }

    echo "✔ Tabel summary_province OK\n";

    // 2. KOSONGKAN TABEL (REBUILD TOTAL)
    $sql_truncate = "TRUNCATE TABLE summary_province";

    if (!mysqli_query($Conn, $sql_truncate)) {
        die("ERROR Truncate: " . mysqli_error($Conn) . "\n");
    }

    echo "✔ summary_province dikosongkan\n";

    // 3. QUERY SUMMARY (OPTIMIZED JOIN)
    $sql_insert = "
    INSERT INTO summary_province 
    (
        province_code,
        province_name,
        jumlah_kabkota,
        jumlah_sekolah,
        total_kebutuhan_guru
    )
    SELECT 
        p.province_code,
        p.province_name,
        COUNT(DISTINCT d.district_code) AS jumlah_kabkota,
        COUNT(DISTINCT s.id_school) AS jumlah_sekolah,
        COALESCE(SUM(ps.KurangGuru), 0) AS total_kebutuhan_guru
    FROM geo_region p
    LEFT JOIN geo_region d 
        ON d.province_code = p.province_code 
        AND d.level_region = 'District'
    LEFT JOIN region r 
        ON r.district_code = d.district_code 
        AND r.category = 'District'
    LEFT JOIN school s 
        ON s.id_region = r.id_region
    LEFT JOIN position_school ps 
        ON ps.id_school = s.id_school
    WHERE p.level_region = 'Province'
    GROUP BY 
        p.province_code,
        p.province_name
    ORDER BY total_kebutuhan_guru DESC
    ";

    if (!mysqli_query($Conn, $sql_insert)) {
        die("ERROR Insert Data: " . mysqli_error($Conn) . "\n");
    }

    echo "✔ Data summary berhasil diperbarui\n";

    // 4. HITUNG TOTAL BARIS
    $total = mysqli_affected_rows($Conn);
    echo "✔ Total baris masuk: {$total}\n";

    echo "=== SELESAI UPDATE ===\n";

?>