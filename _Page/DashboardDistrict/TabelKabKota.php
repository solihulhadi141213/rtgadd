<?php
// Koneksi & dependensi
include "../../_Config/Connection.php";
include "../../_Config/GlobalFunction.php";
include "../../_Config/Session.php";

// Validasi Akses
if(empty($SessionIdAccess)){
    echo '
        <tr>
            <td colspan="6" class="text-center">
                <small class="text-danger">Sesi Akses Sudah Berakhir! Silahkan Login Ulang!</small>
            </td>
        </tr>
    ';
    exit;
}

// PROSES A: Setup Tabel Aggregated (hanya dijalankan sekali)
setupAggregatedTable($Conn);

// PROSES B: Auto-refresh cache jika data lebih dari 1 jam
autoRefreshCache($Conn);

// Parameter request
$batas = !empty($_POST['batas']) ? intval($_POST['batas']) : 5;
$page = !empty($_POST['page']) ? intval($_POST['page']) : 1;
$posisi = ($page - 1) * $batas;
$ShortBy = !empty($_POST['ShortBy']) ? mysqli_real_escape_string($Conn, $_POST['ShortBy']) : "DESC";
$OrderBy = !empty($_POST['OrderBy']) ? mysqli_real_escape_string($Conn, $_POST['OrderBy']) : "jumlah_kebutuhan_guru";
$keyword = !empty($_POST['keyword']) ? mysqli_real_escape_string($Conn, $_POST['keyword']) : "";

// Validasi OrderBy
$allowedOrderColumns = ['province_name', 'district_name', 'jumlah_sekolah', 'jumlah_kebutuhan_guru'];
if(!in_array($OrderBy, $allowedOrderColumns)){
    $OrderBy = "jumlah_kebutuhan_guru";
}

// Query dari tabel aggregated
$baseQuery = "SELECT * FROM stats_kabkota_aggregated WHERE 1=1";

// Filter keyword
if(!empty($keyword)){
    $baseQuery .= " AND (province_name LIKE '%$keyword%' OR district_name LIKE '%$keyword%')";
}

// Hitung total data
$countQuery = "SELECT COUNT(*) as jml FROM ($baseQuery) as counted";
$resultCount = mysqli_query($Conn, $countQuery);
if(!$resultCount) die("Error counting data: " . mysqli_error($Conn));

$rowCount = mysqli_fetch_assoc($resultCount);
$jml_data = $rowCount['jml'];
$JmlHalaman = ceil($jml_data / $batas);

// Jika Data Tidak Ada
if(empty($jml_data)){
    echo '
        <tr>
            <td colspan="6" class="text-center">
                <small class="text-danger">Tidak Ada Data Yang Ditampilkan!</small>
            </td>
        </tr>
    ';
    exit;
}

// Query utama
$query = "$baseQuery ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas";
$result = mysqli_query($Conn, $query);

if(!$result) die("Error fetching data: " . mysqli_error($Conn));

// Looping Data
$no = 1 + $posisi;
while ($data = mysqli_fetch_assoc($result)) {
    $province_name = htmlspecialchars($data['province_name']);
    $district_name = htmlspecialchars($data['district_name']);
    $district_code = htmlspecialchars($data['district_code']);
    $jumlah_sekolah = $data['jumlah_sekolah'];
    $jumlah_kebutuhan_guru = $data['jumlah_kebutuhan_guru'];

    echo '
    <tr>
        <td><small>'.$no.'</small></td>
        <td><small>'.$province_name.'</small></td>
        <td><small>'.$district_name.'</small></td>
        <td><small>'.number_format($jumlah_sekolah, 0, ',', '.').'</small></td>
        <td><small>'.number_format($jumlah_kebutuhan_guru, 0, ',', '.').'</small></td>
        <td>
            <button type="button" class="btn btn-sm btn-primary btn-floating" 
                    data-bs-toggle="modal" data-bs-target="#ModalDetailKabKot" 
                    data-id="'.$district_code.'" title="Lihat Detail Kab/Kota">
                <i class="bi bi-chevron-right"></i>
            </button>
        </td>
    </tr>
    ';
    $no++;
}

// JavaScript pagination
?>
<script>
//Creat Javascript Variabel
var data_count = <?php echo $jml_data; ?>; //Jumlah Total Semua Data
var page_count = <?php echo $JmlHalaman; ?>; //Jumlah Halaman
var curent_page = <?php echo $page; ?>;   //Posisi Halaman Sekarang

//Put Into Pagging Element
$('#data_count_kabkot').html('Data : ' + data_count + ' Record');
$('#page_info_kabkot').html('Page ' + curent_page + ' / ' + page_count + '');

//Set Pagging Button
$('#prev_button_kabkot').prop('disabled', curent_page == 1);
$('#next_button_kabkot').prop('disabled', page_count <= curent_page);
</script>

<?php
// FUNGSI BANTU

function setupAggregatedTable($Conn) {
    $createTableQuery = "
        CREATE TABLE IF NOT EXISTS stats_kabkota_aggregated (
            id INT PRIMARY KEY AUTO_INCREMENT,
            district_code VARCHAR(255) UNIQUE,
            province_name VARCHAR(255),
            district_name VARCHAR(255),
            jumlah_sekolah INT DEFAULT 0,
            jumlah_kebutuhan_guru INT DEFAULT 0,
            last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_province (province_name),
            INDEX idx_district (district_name),
            INDEX idx_kebutuhan (jumlah_kebutuhan_guru),
            INDEX idx_district_code (district_code)
        ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
    ";
    
    if(!mysqli_query($Conn, $createTableQuery)) {
        error_log("Error creating aggregated table: " . mysqli_error($Conn));
    }
}

function autoRefreshCache($Conn) {
    // Cek kapan terakhir update
    $checkQuery = "SELECT MAX(last_updated) as last_update FROM stats_kabkota_aggregated";
    $result = mysqli_query($Conn, $checkQuery);
    
    $needsRefresh = false;
    
    if($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        $lastUpdate = strtotime($data['last_update']);
        $oneHourAgo = time() - 3600; // 1 jam yang lalu
        
        if($lastUpdate < $oneHourAgo) {
            $needsRefresh = true;
        }
    } else {
        // Tabel kosong, perlu diisi
        $needsRefresh = true;
    }
    
    // Refresh jika diperlukan
    if($needsRefresh) {
        refreshAggregatedData($Conn);
    }
}

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
    
    if(!mysqli_query($Conn, $updateQuery)) {
        error_log("Error refreshing aggregated data: " . mysqli_error($Conn));
    }
}
?>