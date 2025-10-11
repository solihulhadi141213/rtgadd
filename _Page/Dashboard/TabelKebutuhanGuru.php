<?php
    //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    //Zona Waktu
    date_default_timezone_set("Asia/Jakarta");
    $JmlHalaman=0;
    $page=0;

    //Validasi Akses
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

    $directory_file = "../../_Page/Dashboard/map_count.json";
    if (!file_exists($directory_file)) {
        echo '
            <tr>
                <td colspan="6" class="text-center">
                    <small class="text-danger">File JSON tidak Ditemukan!</small>
                </td>
            </tr>
        ';
        exit;
    }

    $jsonData = file_get_contents($directory_file);
    $data = json_decode($jsonData, true);
    
    // Validasi data
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
        echo '
            <tr>
                <td colspan="6" class="text-center text-danger">
                    <small>Format data tidak valid</small>
                </td>
            </tr>
        ';
        return;
    }
    
    // Jika tidak ada data
    if (empty($data)) {
        echo '
            <tr>
                <td colspan="6" class="text-center">
                    <small>Tidak ada data</small>
                </td>
            </tr>
        ';
        return;
    }
    
    // Urutkan data dari kurang_guru terbesar ke terkecil
    usort($data, function($a, $b) {
        $kurangA = isset($a['kurang_guru']) ? $a['kurang_guru'] : 0;
        $kurangB = isset($b['kurang_guru']) ? $b['kurang_guru'] : 0;
        return $kurangB - $kurangA;
    });
    
    // Tampilkan data
    $no = 1;
    foreach ($data as $item) {
        $provinsi = isset($item['PROVINSI']) ? $item['PROVINSI'] : 'N/A';
        $kodeProv = isset($item['KODE_PROV']) ? $item['KODE_PROV'] : '';
        $abk = isset($item['ABK']) ? $item['ABK'] : 0;
        $jumlahGuru = isset($item['jumlah_guru']) ? $item['jumlah_guru'] : 0;
        $kurangGuru = isset($item['kurang_guru']) ? $item['kurang_guru'] : 0;
        
        // Tentukan class badge berdasarkan nilai kurang_guru
        $badgeClass = $kurangGuru > 0 ? 'badge-danger' : 'badge-secondary';
        
        // Format angka dengan pemisah ribuan
        $abkFormatted = number_format($abk, 0, ',', '.');
        $jumlahGuruFormatted = number_format($jumlahGuru, 0, ',', '.');
        $kurangGuruFormatted = number_format($kurangGuru, 0, ',', '.');
        
        echo '
            <tr>
                <td><small>' . $no . '</small></td>
                <td><small>' . htmlspecialchars($provinsi) . '</small></td>
                <td><small>' . $abkFormatted . '</small></td>
                <td><small>' . $jumlahGuruFormatted . '</small></td>
                <td><small>' . $kurangGuruFormatted . '</small></td>
                <td>
                    <button class="btn btn-sm btn-primary btn-floating" data-bs-toggle="modal" data-bs-target="#ModalDetailMap" data-id="'.$kodeProv.'">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </td>
            </tr>
        ';
        $no++;
    }
?>
<script>
    //Creat Javascript Variabel
    var data_count = <?php echo $no; ?>;
    
    //Put Into Pagging Element
    $('#data_count').html('Data : ' + data_count + ' Record');
</script>