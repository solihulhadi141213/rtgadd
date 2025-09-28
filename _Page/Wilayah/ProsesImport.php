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
                <td colspan="5" class="text-center">
                    <small class="text-danger">Sesi Akses Sudah Berakhir! Silahkan Login Ulang.</small>
                </td>
            </tr>
        ';
        exit;
    }
    
    // Validasi File
    if(empty($_FILES['data_wilayah']['name'])) {
        echo '
            <tr>
                <td colspan="5" class="text-center">
                    <small class="text-danger">Silahkan pilih file untuk di upload</small>
                </td>
            </tr>
        ';
        exit;
    }
    
    $nama_file = $_FILES['data_wilayah']['name'];
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
    
    if(isset($_FILES['data_wilayah']['name']) && in_array($_FILES['data_wilayah']['type'], $file_mimes)) {
        $arr_file = explode('.', $_FILES['data_wilayah']['name']);
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
            $spreadsheet = $reader->load($_FILES['data_wilayah']['tmp_name']);
            
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
                        <td colspan="5" class="text-center">
                            <small class="text-danger">Tidak ada data pada file excel yang anda upload</small>
                        </td>
                    </tr>
                ';
                exit;
            }
            
            $JumlahKodeValid = 0;
            for($i = 1; $i < $JumlahBaris; $i++) {
                // Validasi baris kosong
                if(empty($sheetData[$i][0]) && empty($sheetData[$i][1]) && empty($sheetData[$i][2])) {
                    continue; // Lewati baris kosong
                }
                
                if(empty($sheetData[$i][0])) {
                    echo '
                        <tr>
                            <td>'.$i.'</td>
                            <td colspan="4" class="text-center">
                                <small class="text-danger">Kode provinsi tidak boleh kosong</small>
                            </td>
                        </tr>
                    ';
                    continue;
                }
                
                if(empty($sheetData[$i][1])) {
                    echo '
                        <tr>
                            <td>'.$i.'</td>
                            <td colspan="4" class="text-center">
                                <small class="text-danger">Nama provinsi tidak boleh kosong</small>
                            </td>
                        </tr>
                    ';
                    continue;
                }
                
                if(empty($sheetData[$i][2])) {
                    echo '
                        <tr>
                            <td>'.$i.'</td>
                            <td>'.$sheetData[$i][0].'-'.$sheetData[$i][1].'</td>
                            <td colspan="3" class="text-center">
                                <small class="text-danger">Kode Kab/Kota tidak boleh kosong</small>
                            </td>
                        </tr>
                    ';
                    continue;
                }
                
                if(empty($sheetData[$i][3])) {
                    echo '
                        <tr>
                            <td>'.$i.'</td>
                            <td>'.$sheetData[$i][0].'-'.$sheetData[$i][1].'</td>
                            <td colspan="3" class="text-center">
                                <small class="text-danger">Nama Kab/Kota tidak boleh kosong</small>
                            </td>
                        </tr>
                    ';
                    continue;
                }
                
                $province_code      = mysqli_real_escape_string($Conn, $sheetData[$i][0]);
                $province_name      = mysqli_real_escape_string($Conn, $sheetData[$i][1]);
                $district_code      = mysqli_real_escape_string($Conn, $sheetData[$i][2]);
                $district_name      = mysqli_real_escape_string($Conn, $sheetData[$i][3]);
                
                if(empty($sheetData[$i][4])) {
                    $code_map = "";
                } else {
                    $code_map = mysqli_real_escape_string($Conn, $sheetData[$i][4]);
                }
                
                // Cek apakah kode provinsi sudah ada
                $id_region = GetDetailData($Conn, 'region', 'province_code', $province_code, 'id_region');

                //Apabila id_region belum ada maka insert sebagai Province
                if(empty($id_region)) {
                    // Insert Data Province
                    $category="Province";
                    $EntryProvince = "INSERT INTO region (
                        category,
                        province_code,
                        province_name,
                        district_code,
                        district_name,
                        code_map
                    ) VALUES (
                        '$category',
                        '$province_code',
                        '$province_name',
                        '',
                        '',
                        '$code_map'
                    )";
                    
                    $InputProvince = mysqli_query($Conn, $EntryProvince);
                    if($InputProvince) {
                        echo '
                            <tr>
                                <td>'.$i.'</td>
                                <td>'.$sheetData[$i][0].'-'.$sheetData[$i][1].'</td>
                                <td colspan="3" class="text-center">
                                    <small class="text-success">Data Provinsi Baru Berhasil Disimpan</small>
                                </td>
                            </tr>
                        ';
                    }else{
                        echo '
                            <tr>
                                <td>'.$i.'</td>
                                <td>'.$sheetData[$i][0].'-'.$sheetData[$i][1].'</td>
                                <td colspan="3" class="text-center">
                                    <small class="text-danger">Data Provinsi Baru Gagal Disimpan</small>
                                </td>
                            </tr>
                        ';
                    }
                }
                
                //Cek apakah data sudah ada berdasarkan kode provinsi dan kode kabupaten
                $validasi_duplikat=mysqli_num_rows(mysqli_query($Conn, "SELECT id_region  FROM region WHERE province_code='$province_code' AND district_code='$district_code' AND category='District'"));

                if(!empty($validasi_duplikat)){
                    
                    //Jika Sudah Ada Update Kabupaten
                    $category="District";
                    $sql = "UPDATE region SET province_name = ?, district_name = ?, code_map = ? WHERE province_code = ? AND district_code = ?";
                    $stmt = $Conn->prepare($sql);
                    if (!$stmt) {
                        echo '
                            <tr>
                                <td>'.$i.'</td>
                                <td>'.$sheetData[$i][0].'-'.$sheetData[$i][1].'</td>
                                <td>'.$sheetData[$i][2].'-'.$sheetData[$i][3].'</td>
                                <td>'.$sheetData[$i][4].'</td>
                                <td colspan="3" class="text-center">
                                    <small class="text-danger">Prepare gagal: '.htmlspecialchars($Conn->error).'</small>
                                </td>
                            </tr>
                        ';
                    }
                    $stmt->bind_param(
                        "sssss",
                        $province_name,
                        $district_name,
                        $code_map,
                        $province_code,
                        $district_code
                    );

                    $Input = $stmt->execute();
                    $err   = $stmt->error;
                    $stmt->close();
                    if ($Input) {
                        echo '
                            <tr>
                                <td>'.$i.'</td>
                                <td>'.$sheetData[$i][0].'-'.$sheetData[$i][1].'</td>
                                <td>'.$sheetData[$i][2].'-'.$sheetData[$i][3].'</td>
                                <td>'.$sheetData[$i][4].'</td>
                                <td class="text-center">
                                    <small class="text-success">Update Berhasil</small>
                                </td>
                            </tr>
                        ';
                    }else{
                        echo '
                            <tr>
                                <td>'.$i.'</td>
                                <td>'.$sheetData[$i][0].'-'.$sheetData[$i][1].'</td>
                                <td>'.$sheetData[$i][2].'-'.$sheetData[$i][3].'</td>
                                <td>'.$sheetData[$i][4].'</td>
                                <td class="text-center">
                                    <small class="text-danger">Update Gagal</small>
                                </td>
                            </tr>
                        ';
                    }
                }else{
                    $category="District";
                    $EntryDistrict = "INSERT INTO region (
                        category,
                        province_code,
                        province_name,
                        district_code,
                        district_name,
                        code_map
                    ) VALUES (
                        '$category',
                        '$province_code',
                        '$province_name',
                        '$district_code',
                        '$district_name',
                        '$code_map'
                    )";
                    
                    $InputDistrict = mysqli_query($Conn, $EntryDistrict);
                    if($InputDistrict) {
                        echo '
                            <tr>
                                <td>'.$i.'</td>
                                <td>'.$sheetData[$i][0].'-'.$sheetData[$i][1].'</td>
                                <td>'.$sheetData[$i][2].'-'.$sheetData[$i][3].'</td>
                                <td>'.$sheetData[$i][4].'</td>
                                <td class="text-center">
                                    <small class="text-primary">Insert Berhasil</small>
                                </td>
                            </tr>
                        ';
                    }else{
                        echo '
                            <tr>
                                <td>'.$i.'</td>
                                <td>'.$sheetData[$i][0].'-'.$sheetData[$i][1].'</td>
                                <td>'.$sheetData[$i][2].'-'.$sheetData[$i][3].'</td>
                                <td>'.$sheetData[$i][4].'</td>
                                <td class="text-center">
                                    <small class="text-danger">Insert Gagal</small>
                                </td>
                            </tr>
                        ';
                    }
                }
            }
            
            // Tampilkan ringkasan
            echo '
                <tr>
                    <td colspan="5" class="text-center">
                        <small class="text-info">Proses selesai. '.$JumlahKodeValid.' dari '.$JumlahValidator.' data berhasil diimpor.</small>
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
                    <td colspan="5" class="text-center">
                        <small class="text-danger">Error membaca file: '.$e->getMessage().'</small>
                    </td>
                </tr>
            ';
        }
        
    } else {
        echo '
            <tr>
                <td colspan="5" class="text-center">
                    <small class="text-danger">File tidak valid. Silahkan upload file Excel atau CSV.</small>
                </td>
            </tr>
        ';
    }
?>