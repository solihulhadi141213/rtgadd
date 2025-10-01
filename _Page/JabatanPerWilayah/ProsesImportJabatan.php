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

                //Skip data kosong
                if(empty($sheetData[$i][0]) && empty($sheetData[$i][1]) && empty($sheetData[$i][2])) {
                    continue;
                }

                //Validasi Data Wajib
                if(empty($sheetData[$i][0])) {
                    echo '
                        <tr>
                            <td>'.$i.'</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><small class="text-danger">Kode Provinsi (BPS) kosong</small></td>
                        </tr>
                    ';
                    continue;
                }

                if(empty($sheetData[$i][1])) {
                    echo '
                        <tr>
                            <td>'.$i.'</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><small class="text-danger">Kode Provinsi (DAPODIK) kosong</small></td>
                        </tr>
                    ';
                    continue;
                }
                if(empty($sheetData[$i][2])) {
                    echo '
                        <tr>
                            <td>'.$i.'</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><small class="text-danger">Nama Provinsi kosong</small></td>
                        </tr>
                    ';
                    continue;
                }
                if(empty($sheetData[$i][3])) {
                    echo '
                        <tr>
                            <td>'.$i.'</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><small class="text-danger">Kode Kab/Kota (BPS) kosong</small></td>
                        </tr>
                    ';
                    continue;
                }
                if(empty($sheetData[$i][4])) {
                    echo '
                        <tr>
                            <td>'.$i.'</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><small class="text-danger">Kode Kab/Kota (DAPODIK) kosong</small></td>
                        </tr>
                    ';
                    continue;
                }
                if(empty($sheetData[$i][5])) {
                    echo '
                        <tr>
                            <td>'.$i.'</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><small class="text-danger">Nama Kab/Kota kosong</small></td>
                        </tr>
                    ';
                    continue;
                }
                if(empty($sheetData[$i][6])) {
                    echo '
                        <tr>
                            <td>'.$i.'</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><small class="text-danger">Kode Jabatan kosong</small></td>
                        </tr>
                    ';
                    continue;
                }
                if(empty($sheetData[$i][7])) {
                    echo '
                        <tr>
                            <td>'.$i.'</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><small class="text-danger">Nama Jabatan kosong</small></td>
                        </tr>
                    ';
                    continue;
                }
                
                $province_code          = trim($sheetData[$i][0]);
                $province_code_dapodik  = trim($sheetData[$i][1]);
                $province_name          = trim($sheetData[$i][2]);
                $district_code          = trim($sheetData[$i][3]);
                $district_code_dapodik  = trim($sheetData[$i][4]);
                $district_name          = trim($sheetData[$i][5]);
                $position_code          = trim($sheetData[$i][6]);
                $position_name          = trim($sheetData[$i][7]);
                
                //Variabel Tidak wajib
                $abk                    = !empty($sheetData[$i][8]) ? (int)$sheetData[$i][8] : 0;
                $asn                    = !empty($sheetData[$i][9]) ? (int)$sheetData[$i][9] : 0;
                $asn_di_negeri          = !empty($sheetData[$i][10]) ? (int)$sheetData[$i][10] : 0;
                $asn_di_swasta          = !empty($sheetData[$i][11]) ? (int)$sheetData[$i][11] : 0;
                $NonASN_sblmOkt2022     = !empty($sheetData[$i][12]) ? (int)$sheetData[$i][12] : 0;
                $NonASN_stlhOkt2022     = !empty($sheetData[$i][13]) ? (int)$sheetData[$i][13] : 0;
                $pppk2024               = !empty($sheetData[$i][14]) ? (int)$sheetData[$i][14] : 0;
                $jumlah_guru            = !empty($sheetData[$i][15]) ? (int)$sheetData[$i][15] : 0;
                $kurang_guru            = !empty($sheetData[$i][16]) ? (int)$sheetData[$i][16] : 0;
                $jumlah_asn             = !empty($sheetData[$i][17]) ? (int)$sheetData[$i][17] : 0;
                $kurang_asn             = !empty($sheetData[$i][18]) ? (int)$sheetData[$i][18] : 0;

                //Cari id_region (province)
                $id_region_province = GetDetailData($Conn, 'region', 'province_code', $province_code, 'id_region');
                
                if(empty($id_region_province)){
                    //Jika tidak ada Insert Provinsi baru
                    $category = "Province";
                    $insert_province = $Conn->prepare("INSERT INTO region (category, province_code, province_code_dapodik, province_name) VALUES (?, ?, ?, ?)");
                    $insert_province->bind_param("ssss", $category, $province_code, $province_code_dapodik, $province_name);
                    if($insert_province->execute()){
                        echo '
                            <tr>
                                <td>'.$i.'</td>
                                <td>'.$province_name.'</td>
                                <td></td>
                                <td></td>
                                <td><small class="text-info">Insert Provinsi Baru</small></td>
                            </tr>
                        ';
                    }else{
                        //Jika Gagal Hentikan proses dan lanjut ke baris berikutnya
                        echo '
                            <tr>
                                <td>'.$i.'</td>
                                <td>'.$province_name.'</td>
                                <td></td>
                                <td></td>
                                <td><small class="text-danger">Insert Provinsi Gagal</small></td>
                            </tr>
                        ';
                        continue;
                    }
                }

                //Cari id_region (district)
                $id_region = GetDetailData($Conn, 'region', 'district_code', $district_code, 'id_region');
                
                if(empty($id_region)){
                    //Jika tidak ada Insert Kab/kota baru
                    $category = "District";
                    $insert_district = $Conn->prepare("INSERT INTO region (category, province_code, province_code_dapodik, province_name, district_code, district_code_dapodik, district_name) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $insert_district->bind_param("sssssss", $category, $province_code, $province_code_dapodik, $province_name, $district_code, $district_code_dapodik, $district_name);
                    if($insert_district->execute()){
                        $id_region = $Conn->insert_id;
                        echo '
                            <tr>
                                <td>'.$i.'</td>
                                <td>'.$province_name.'</td>
                                <td>'.$district_name.'</td>
                                <td></td>
                                <td><small class="text-info">Insert Kab/Kota Baru</small></td>
                            </tr>
                        ';
                    }else{
                        //Jika Gagal Hentikan proses dan lanjut ke baris berikutnya
                        $id_region=0;
                        echo '
                            <tr>
                                <td>'.$i.'</td>
                                <td>'.$province_name.'</td>
                                <td>'.$district_name.'</td>
                                <td></td>
                                <td><small class="text-danger">Insert Kab/Kota Gagal</small></td>
                            </tr>
                        ';
                        continue;
                    }
                }

                //Cari id_position
                $id_position = GetDetailData($Conn, 'position', 'position_code', $position_code, 'id_position');
                
                if(empty($id_position)){
                    //Jika tidak ada 
                    $insert_position = $Conn->prepare("INSERT INTO position (position_code, position_name) VALUES (?, ?)");
                    $insert_position->bind_param("ss", $position_code, $position_name);
                    if($insert_position->execute()){
                        $id_position = $Conn->insert_id;
                        echo '
                            <tr>
                                <td>'.$i.'</td>
                                <td>'.$province_name.'</td>
                                <td>'.$district_name.'</td>
                                <td>'.$position_name.'</td>
                                <td><small class="text-info">Insert Jabatan Baru</small></td>
                            </tr>
                        ';
                    }else{
                        //Jika Gagal Hentikan proses dan lanjut ke baris berikutnya
                        echo '
                            <tr>
                                <td>'.$i.'</td>
                                <td>'.$province_name.'</td>
                                <td>'.$district_name.'</td>
                                <td>'.$position_name.'</td>
                                <td><small class="text-danger">Insert Jabatan Gagal</small></td>
                            </tr>
                        ';
                        continue;
                    }
                }

                
                // Cek Duplikat id_position_region
                $cek = $Conn->prepare("SELECT id_position_region FROM position_region WHERE id_region=? AND id_position=?");
                $cek->bind_param("ii", $id_region, $id_position);
                $cek->execute();
                $result = $cek->get_result();
                
                if($result->num_rows > 0){
                    // UPDATE jika duplikat
                    $update = $Conn->prepare("UPDATE position_region SET 
                        abk=?, 
                        asn=?, 
                        asn_di_negeri=?, 
                        asn_di_swasta=?, 
                        NonASN_sblmOkt2022=?, 
                        NonASN_stlhOkt2022=?, 
                        pppk2024=?, 
                        jumlah_guru=?, 
                        kurang_guru=?, 
                        jumlah_asn=?, 
                        kurang_asn=?
                    WHERE id_region=? AND id_position=?");
                    $update->bind_param("iiiiiiiiiiiii", 
                        $abk, 
                        $asn, 
                        $asn_di_negeri, 
                        $asn_di_swasta, 
                        $NonASN_sblmOkt2022, 
                        $NonASN_stlhOkt2022, 
                        $pppk2024, 
                        $jumlah_guru, 
                        $kurang_guru, 
                        $jumlah_asn, 
                        $kurang_asn, 
                        $id_region, 
                        $id_position
                    );
                    if($update->execute()){
                        echo '
                            <tr>
                                <td>'.$i.'</td>
                                <td>'.$province_name.'</td>
                                <td>'.$district_name.'</td>
                                <td>'.$position_name.'</td>
                                <td><small class="text-success">Update Berhasil</small></td>
                            </tr>
                        ';
                        $JumlahKodeValid++;
                    } else {
                        //Jika Gagal Hentikan proses dan lanjut ke baris berikutnya
                        echo '
                            <tr>
                                <td>'.$i.'</td>
                                <td>'.$province_name.'</td>
                                <td>'.$district_name.'</td>
                                <td>'.$position_name.'</td>
                                <td><small class="text-danger">Update Gagal '.$Conn->error.'</small></td>
                            </tr>
                        ';
                        continue;
                    }
                    $update->close();
                } else {
                    // INSERT jika belum ada
                    $insert = $Conn->prepare("INSERT INTO position_region (
                        id_position, 
                        id_region, 
                        abk, 
                        asn, 
                        asn_di_negeri, 
                        asn_di_swasta, 
                        NonASN_sblmOkt2022, 
                        NonASN_stlhOkt2022, 
                        pppk2024, 
                        jumlah_guru, 
                        kurang_guru, 
                        jumlah_asn, 
                        kurang_asn 
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $insert->bind_param(
                        "iiiiiiiiiiiii", 
                        $id_position, 
                        $id_region, 
                        $abk, 
                        $asn, 
                        $asn_di_negeri, 
                        $asn_di_swasta, 
                        $NonASN_sblmOkt2022, 
                        $NonASN_stlhOkt2022, 
                        $pppk2024, 
                        $jumlah_guru, 
                        $kurang_guru, 
                        $jumlah_asn, 
                        $kurang_asn
                    );
                    if($insert->execute()){
                        echo '
                            <tr>
                                <td>'.$i.'</td>
                                <td>'.$province_name.'</td>
                                <td>'.$district_name.'</td>
                                <td>'.$position_name.'</td>
                                <td><small class="text-success">Insert Berhasil</small></td>
                            </tr>
                        ';
                        $JumlahKodeValid++;
                    } else {
                        echo '
                            <tr>
                                <td>'.$i.'</td>
                                <td>'.$province_name.'</td>
                                <td>'.$district_name.'</td>
                                <td>'.$position_name.'</td>
                                <td><small class="text-danger">Insert Gagal</small></td>
                            </tr>
                        ';
                        continue;
                    }
                    $insert->close();
                }
                $cek->close();
            }
            
            echo "<tr><td colspan='5' class='text-center'><small class='text-info'>Proses selesai. $JumlahKodeValid dari $JumlahValidator data berhasil diproses.</small></td></tr>";
            
        } catch (Exception $e) {
            if (PHP_VERSION_ID < 80000) {
                libxml_disable_entity_loader($entityLoaderDisabled);
            }
            echo "<tr><td colspan='5' class='text-center'><small class='text-danger'>Error membaca file: ".$e->getMessage()."</small></td></tr>";
        }
    } else {
        echo "<tr><td colspan='5' class='text-center'><small class='text-danger'>File tidak valid. Silahkan upload file Excel atau CSV.</small></td></tr>";
    }
?>
