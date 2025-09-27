<?php
    require '../../vendor/autoload.php';
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Reader\Csv;
    use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

    //Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    // Time Zone
    date_default_timezone_set('Asia/Jakarta');
    $now = date('Y-m-d H:i:s');

    // Validasi Akses
    if (empty($SessionIdAccess)) {
        echo '
            <tr>
                <td colspan="9" class="text-center">
                    <small class="text-danger">Sesi Akses Sudah Berakhir! Silahkan Login Ulang.</small>
                </td>
            </tr>
        ';
        exit;
    }

    // Validasi File
    if(empty($_FILES['data_jabatan']['name'])) {
        echo '
            <tr>
                <td colspan="9" class="text-center">
                    <small class="text-danger">Silahkan pilih file untuk di upload</small>
                </td>
            </tr>
        ';
        exit;
    }

    $file_mimes = array(
        'application/octet-stream', 
        'application/vnd.ms-excel', 
        'application/x-csv', 
        'text/x-csv', 
        'text/csv', 
        'application/csv', 
        'application/excel', 
        'application/vnd.msexcel', 
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.oasis.opendocument.spreadsheet'
    );

    if(isset($_FILES['data_jabatan']['name']) && in_array($_FILES['data_jabatan']['type'], $file_mimes)) {
        $arr_file = explode('.', $_FILES['data_jabatan']['name']);
        $extension = strtolower(end($arr_file));
        
        $reader = ($extension == 'csv') ? new Csv() : new Xlsx();
        
        if (PHP_VERSION_ID < 80000) {
            $entityLoaderDisabled = libxml_disable_entity_loader(true);
        }
        
        try {
            $spreadsheet = $reader->load($_FILES['data_jabatan']['tmp_name']);
            if (PHP_VERSION_ID < 80000) {
                libxml_disable_entity_loader($entityLoaderDisabled);
            }
            
            $sheetData = $spreadsheet->getActiveSheet()->toArray();
            $JumlahBaris = count($sheetData);
            $JumlahValidator = $JumlahBaris - 1;
            
            if(empty($JumlahValidator)) {
                echo '
                    <tr>
                        <td colspan="9" class="text-center">
                            <small class="text-danger">Tidak ada data pada file excel yang anda upload</small>
                        </td>
                    </tr>
                ';
                exit;
            }
            
            $JumlahKodeValid = 0;
            for($i = 1; $i < $JumlahBaris; $i++) {
                if(empty($sheetData[$i][0]) && empty($sheetData[$i][1]) && empty($sheetData[$i][2])) {
                    continue;
                }
                if(empty($sheetData[$i][0])) {
                    echo "<tr><td>$i</td><td colspan='8' class='text-center'><small class='text-danger'>Provinsi tidak boleh kosong</small></td></tr>";
                    continue;
                }
                if(empty($sheetData[$i][1])) {
                    echo "<tr><td>$i</td><td>{$sheetData[$i][0]}</td><td colspan='7' class='text-center'><small class='text-danger'>Kabupaten tidak boleh kosong</small></td></tr>";
                    continue;
                }
                if(empty($sheetData[$i][2])) {
                    echo "<tr><td>$i</td><td>{$sheetData[$i][0]}</td><td>{$sheetData[$i][1]}</td><td colspan='6' class='text-center'><small class='text-danger'>Jabatan tidak boleh kosong</small></td></tr>";
                    continue;
                }
                
                $province   = trim($sheetData[$i][0]);
                $regency    = trim($sheetData[$i][1]);
                $department = trim($sheetData[$i][2]);
                $workload           = !empty($sheetData[$i][3]) ? (int)$sheetData[$i][3] : 0;
                $officials_public   = !empty($sheetData[$i][4]) ? (int)$sheetData[$i][4] : 0;
                $officials_private  = !empty($sheetData[$i][5]) ? (int)$sheetData[$i][5] : 0;
                $manpower_gap       = !empty($sheetData[$i][6]) ? (int)$sheetData[$i][6] : 0;
                
                // Cek Duplikat
                $cek = $Conn->prepare("SELECT id_position_region FROM position_region WHERE province=? AND regency=? AND department=?");
                $cek->bind_param("sss", $province, $regency, $department);
                $cek->execute();
                $result = $cek->get_result();
                
                if($result->num_rows > 0){
                    // UPDATE jika duplikat
                    $update = $Conn->prepare("UPDATE position_region SET workload=?, officials_public=?, officials_private=?, manpower_gap=? WHERE province=? AND regency=? AND department=?");
                    $update->bind_param("iiiisss", $workload, $officials_public, $officials_private, $manpower_gap, $province, $regency, $department);
                    if($update->execute()){
                        echo "
                            <tr>
                                <td>$i</td>
                                <td>$province</td>
                                <td>$regency</td>
                                <td>$department</td>
                                <td>$workload</td>
                                <td>$officials_public</td>
                                <td>$officials_private</td>
                                <td>$manpower_gap</td>
                                <td class='text-center'>
                                    <small class='text-warning'>Success (Update)</small>
                                </td>
                            </tr>
                        ";
                        $JumlahKodeValid++;
                    } else {
                        echo "<tr><td>$i</td><td colspan='8' class='text-center'><small class='text-danger'>Gagal update: ".$Conn->error."</small></td></tr>";
                    }
                    $update->close();
                } else {
                    // INSERT jika belum ada
                    $insert = $Conn->prepare("INSERT INTO position_region (province, regency, department, workload, officials_public, officials_private, manpower_gap) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $insert->bind_param("sssiiii", $province, $regency, $department, $workload, $officials_public, $officials_private, $manpower_gap);
                    if($insert->execute()){
                        echo "
                            <tr>
                                <td>$i</td>
                                <td>$province</td>
                                <td>$regency</td>
                                <td>$department</td>
                                <td>$workload</td>
                                <td>$officials_public</td>
                                <td>$officials_private</td>
                                <td>$manpower_gap</td>
                                <td class='text-center'>
                                    <small class='text-success'>Success (Insert)</small>
                                </td>
                            </tr>
                        ";
                        $JumlahKodeValid++;
                    } else {
                        echo "<tr><td>$i</td><td colspan='8' class='text-center'><small class='text-danger'>Gagal insert: ".$Conn->error."</small></td></tr>";
                    }
                    $insert->close();
                }
                $cek->close();
            }
            
            echo "<tr><td colspan='9' class='text-center'><small class='text-info'>Proses selesai. $JumlahKodeValid dari $JumlahValidator data berhasil diproses.</small></td></tr>";
            
        } catch (Exception $e) {
            if (PHP_VERSION_ID < 80000) {
                libxml_disable_entity_loader($entityLoaderDisabled);
            }
            echo "<tr><td colspan='9' class='text-center'><small class='text-danger'>Error membaca file: ".$e->getMessage()."</small></td></tr>";
        }
    } else {
        echo "<tr><td colspan='9' class='text-center'><small class='text-danger'>File tidak valid. Silahkan upload file Excel atau CSV.</small></td></tr>";
    }
?>
