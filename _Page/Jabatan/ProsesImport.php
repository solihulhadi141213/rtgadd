<?php
    require '../../vendor/autoload.php';
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Reader\Csv;
    use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    
    // Time Zone
    date_default_timezone_set('Asia/Jakarta');
    
    // Time Now Tmp
    $now = date('Y-m-d H:i:s');
    
    // Validasi Akses
    if (empty($SessionIdAccess)) {
        echo '
            <tr>
                <td colspan="4" class="text-center">
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
                <td colspan="4" class="text-center">
                    <small class="text-danger">Silahkan pilih file untuk di upload</small>
                </td>
            </tr>
        ';
        exit;
    }
    
    $nama_file = $_FILES['data_jabatan']['name'];
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
        $extension = end($arr_file);
        
        if('csv' == $extension) {
            $reader = new Csv();
        } else {
            $reader = new Xlsx();
        }
        
        // Mengatasi deprecated function dengan menonaktifkan entity loader secara kondisional
        if (PHP_VERSION_ID < 80000) {
            $entityLoaderDisabled = libxml_disable_entity_loader(true);
        }
        
        try {
            $spreadsheet = $reader->load($_FILES['data_jabatan']['tmp_name']);
            
            // Mengembalikan entity loader ke keadaan semula untuk PHP < 8.0
            if (PHP_VERSION_ID < 80000) {
                libxml_disable_entity_loader($entityLoaderDisabled);
            }
            
            $sheetData = $spreadsheet->getActiveSheet()->toArray();
            $JumlahBaris = count($sheetData);
            $JumlahValidator = $JumlahBaris - 1;
            
            if(empty($JumlahValidator)) {
                echo '
                    <tr>
                        <td colspan="4" class="text-center">
                            <small class="text-danger">Tidak ada data pada file excel yang anda upload</small>
                        </td>
                    </tr>
                ';
                exit;
            }
            
            $jumlah_berhasil = 0;
            for($i = 1; $i < $JumlahBaris; $i++) {
                // Validasi baris kosong
                if(empty($sheetData[$i][0]) && empty($sheetData[$i][1]) && empty($sheetData[$i][2])) {
                    continue; // Lewati baris kosong
                }
                
                if(empty($sheetData[$i][0])) {
                    echo '
                        <tr>
                            <td>'.$i.'</td>
                            <td>-</td>
                            <td>-</td>
                            <td>
                                <small class="text-danger">Kode Jabatan tidak boleh kosong</small>
                            </td>
                        </tr>
                    ';
                    continue;
                }
                
                if(empty($sheetData[$i][1])) {
                    echo '
                        <tr>
                            <td>'.$i.'</td>
                            <td>-</td>
                            <td>-</td>
                            <td>
                                <small class="text-danger">Nama Jabatan tidak boleh kosong</small>
                            </td>
                        </tr>
                    ';
                    continue;
                }
                
                $position_code  = mysqli_real_escape_string($Conn, $sheetData[$i][0]);
                $position_name  = mysqli_real_escape_string($Conn, $sheetData[$i][1]);
                
                // Cek apakah kode jabatan sudah ada
                $id_position = GetDetailData($Conn, 'position', 'position_code', $position_code, 'id_position');

                //apabila belum ada insert
                if(empty($id_position)){
                    $EntryPosition = "INSERT INTO position (
                        position_code,
                        position_name
                    ) VALUES (
                        '$position_code',
                        '$position_name'
                    )";
                    
                    $InputPosition = mysqli_query($Conn, $EntryPosition);
                    if($InputPosition) {
                        $jumlah_berhasil=$jumlah_berhasil+1;
                        echo '
                            <tr>
                                <td>'.$i.'</td>
                                <td>'.$position_code.'</td>
                                <td>'.$position_name.'</td>
                                <td>
                                    <small class="text-primary">Insert Berhasil</small>
                                </td>
                            </tr>
                        ';
                    }else{
                        echo '
                            <tr>
                                <td>'.$i.'</td>
                                <td>'.$position_code.'</td>
                                <td>'.$position_name.'</td>
                                <td>
                                    <small class="text-danger">Insert Gagal</small>
                                </td>
                            </tr>
                        ';
                    }
                }else{
                    //Jika Sudah Ada Update
                    $QryUpdate = $Conn->prepare("UPDATE position SET position_code=?, position_name=? WHERE id_position=?");
                    $QryUpdate->bind_param("ssi", $position_code, $position_name, $id_position);
                    if($QryUpdate->execute()){
                        $jumlah_berhasil=$jumlah_berhasil+1;
                        echo '
                            <tr>
                                <td>'.$i.'</td>
                                <td>'.$position_code.'</td>
                                <td>'.$position_name.'</td>
                                <td>
                                    <small class="text-success">Update Berhasil</small>
                                </td>
                            </tr>
                        ';
                    }else{
                        echo '
                            <tr>
                                <td>'.$i.'</td>
                                <td>'.$position_code.'</td>
                                <td>'.$position_name.'</td>
                                <td>
                                    <small class="text-warning">Update Gagal</small>
                                </td>
                            </tr>
                        ';
                    }
                }
            }
            
            // Tampilkan ringkasan
            echo '
                <tr>
                    <td colspan="4" class="text-center">
                        <small class="text-info">Proses selesai. '.$jumlah_berhasil.' dari '.$JumlahValidator.' data berhasil diimpor.</small>
                    </td>
                </tr>
            ';
            
        } catch (Exception $e) {
            // Mengembalikan entity loader ke keadaan semula untuk PHP < 8.0 jika terjadi error
            if (PHP_VERSION_ID < 80000) {
                libxml_disable_entity_loader($entityLoaderDisabled);
            }
            
            echo '
                <tr>
                    <td colspan="4" class="text-center">
                        <small class="text-danger">Error membaca file: '.$e->getMessage().'</small>
                    </td>
                </tr>
            ';
        }
        
    } else {
        echo '
            <tr>
                <td colspan="4" class="text-center">
                    <small class="text-danger">File tidak valid. Silahkan upload file Excel atau CSV.</small>
                </td>
            </tr>
        ';
    }
?>