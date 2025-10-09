<?php
    // ProsesImportBatch.php
    header('Content-Type: text/html; charset=utf-8');

    if(!isset($_POST['batch'])){
        exit('<tr><td colspan="7" class="text-danger">Batch tidak ditemukan</td></tr>');
    }

    $batch = json_decode($_POST['batch'], true);
    if(!$batch || !is_array($batch)){
        exit('<tr><td colspan="7" class="text-danger">Format batch salah</td></tr>');
    }

    $no = 1;
    $html = "";

    foreach($batch as $row){
        // Pastikan jumlah kolom sesuai CSV
        $provinsi   = isset($row[0]) ? htmlspecialchars($row[0]) : '';
        $kabkota    = isset($row[1]) ? htmlspecialchars($row[1]) : '';
        $sekolah    = isset($row[2]) ? htmlspecialchars($row[2]) : '';
        $jenjang    = isset($row[3]) ? htmlspecialchars($row[3]) : '';
        $jabatan    = isset($row[4]) ? htmlspecialchars($row[4]) : '';
        $keterangan = isset($row[5]) ? htmlspecialchars($row[5]) : '';

        $html .= "
            <tr>
                <td>". $no++ ."</td>
                <td>$provinsi</td>
                <td>$kabkota</td>
                <td>$sekolah</td>
                <td>$jenjang</td>
                <td>$jabatan</td>
                <td>$keterangan</td>
            </tr>
        ";
    }

    echo $html;

?>