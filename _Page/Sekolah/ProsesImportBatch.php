<?php
include "../../_Config/Connection.php";
include "../../_Config/GlobalFunction.php";
include "../../_Config/Session.php";

// Time Zone
date_default_timezone_set('Asia/Jakarta');
$now = date('Y-m-d H:i:s');

// Validasi Akses
if (empty($SessionIdAccess)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Sesi Akses Sudah Berakhir! Silahkan Login Ulang.'
    ]);
    exit;
}

// Validasi parameter
if(empty($_POST['file_token']) || empty($_POST['batch']) || empty($_POST['total_batches'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Parameter tidak valid'
    ]);
    exit;
}

$file_token = $_POST['file_token'];
$current_batch = intval($_POST['batch']);
$total_batches = intval($_POST['total_batches']);
$batch_size = 100;

// Ambil data dari session
if(!isset($_SESSION['import_data_' . $file_token])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Data import tidak ditemukan. Silahkan upload ulang file.'
    ]);
    exit;
}

$sheetData = $_SESSION['import_data_' . $file_token];
$JumlahBaris = count($sheetData);

// Hitung range data untuk batch ini
$start_row = 1 + (($current_batch - 1) * $batch_size);
$end_row = min($start_row + $batch_size - 1, $JumlahBaris - 1);

$html_output = '';
$JumlahKodeValid = 0;

for($i = $start_row; $i <= $end_row; $i++) {
    // Validasi baris kosong
    if(empty($sheetData[$i][0]) && empty($sheetData[$i][1]) && empty($sheetData[$i][2])) {
        continue; // Lewati baris kosong
    }

    if(empty($sheetData[$i][0])) {
        $html_output .= '
        <tr>
            <td>'.$i.'</td>
            <td colspan="5" class="text-center">
                <small class="text-danger">Kode provinsi tidak boleh kosong</small>
            </td>
        </tr>';
        continue;
    }

    if(empty($sheetData[$i][1])) {
        $html_output .= '
        <tr>
            <td>'.$i.'</td>
            <td colspan="5" class="text-center">
                <small class="text-danger">Nama provinsi tidak boleh kosong</small>
            </td>
        </tr>';
        continue;
    }

    if(empty($sheetData[$i][2])) {
        $html_output .= '
        <tr>
            <td>'.$i.'</td>
            <td>'.$sheetData[$i][0].'-'.$sheetData[$i][1].'</td>
            <td colspan="4" class="text-center">
                <small class="text-danger">Kode Kab/Kota tidak boleh kosong</small>
            </td>
        </tr>';
        continue;
    }

    if(empty($sheetData[$i][3])) {
        $html_output .= '
        <tr>
            <td>'.$i.'</td>
            <td>'.$sheetData[$i][0].'-'.$sheetData[$i][1].'</td>
            <td colspan="4" class="text-center">
                <small class="text-danger">Nama Kab/Kota tidak boleh kosong</small>
            </td>
        </tr>';
        continue;
    }

    if(empty($sheetData[$i][4])) {
        $html_output .= '
        <tr>
            <td>'.$i.'</td>
            <td>'.$sheetData[$i][0].'-'.$sheetData[$i][1].'</td>
            <td>'.$sheetData[$i][2].'-'.$sheetData[$i][3].'</td>
            <td colspan="3" class="text-center">
                <small class="text-danger">Kode Sekolah Tidak Boleh Kosong</small>
            </td>
        </tr>';
        continue;
    }

    if(empty($sheetData[$i][5])) {
        $html_output .= '
        <tr>
            <td>'.$i.'</td>
            <td>'.$sheetData[$i][0].'-'.$sheetData[$i][1].'</td>
            <td>'.$sheetData[$i][2].'-'.$sheetData[$i][3].'</td>
            <td>'.$sheetData[$i][4].'</td>
            <td colspan="2" class="text-center">
                <small class="text-danger">Nama Sekolah Tidak Boleh Kosong</small>
            </td>
        </tr>';
        continue;
    }

    $province_code = mysqli_real_escape_string($Conn, $sheetData[$i][0]);
    $province_name = mysqli_real_escape_string($Conn, $sheetData[$i][1]);
    $district_code = mysqli_real_escape_string($Conn, $sheetData[$i][2]);
    $district_name = mysqli_real_escape_string($Conn, $sheetData[$i][3]);
    $npsn = mysqli_real_escape_string($Conn, $sheetData[$i][4]);
    $school_name = mysqli_real_escape_string($Conn, $sheetData[$i][5]);
    $code_map = "";

    // Cek apakah kode provinsi sudah ada
    $id_region = GetDetailData($Conn, 'region', 'province_code', $province_code, 'id_region');
    
    //Apabila id_region belum ada maka insert sebagai Province
    if(empty($id_region)) {
        // Insert Data Province
        $category = "Province";
        $EntryProvince = "INSERT INTO region (category, province_code, province_name, district_code, district_name, code_map) VALUES ('$category', '$province_code', '$province_name', '', '', '$code_map')";
        $InputProvince = mysqli_query($Conn, $EntryProvince);
        
        if($InputProvince) {
            $html_output .= '
            <tr>
                <td>'.$i.'</td>
                <td>'.$sheetData[$i][0].'-'.$sheetData[$i][1].'</td>
                <td colspan="4" class="text-center">
                    <small class="text-success">Data Provinsi Baru Berhasil Disimpan</small>
                </td>
            </tr>';
        } else {
            $html_output .= '
            <tr>
                <td>'.$i.'</td>
                <td>'.$sheetData[$i][0].'-'.$sheetData[$i][1].'</td>
                <td colspan="4" class="text-center">
                    <small class="text-danger">Data Provinsi Baru Gagal Disimpan</small>
                </td>
            </tr>';
        }
    }

    // Cek apakah kode kabupaten sudah ada
    $id_region = GetDetailData($Conn, 'region', 'district_code', $district_code, 'id_region');
    
    //Apabila id_region belum ada maka insert sebagai District
    if(empty($id_region)) {
        // Insert Data District
        $category = "District";
        $EntryDistrict = "INSERT INTO region (category, province_code, province_name, district_code, district_name, code_map) VALUES ('$category', '$province_code', '$province_name', '$district_code', '$district_name', '$code_map')";
        $InputDistrict = mysqli_query($Conn, $EntryDistrict);
        
        if($InputDistrict) {
            $html_output .= '
            <tr>
                <td>'.$i.'</td>
                <td>'.$sheetData[$i][0].'-'.$sheetData[$i][1].'</td>
                <td>'.$sheetData[$i][2].'-'.$sheetData[$i][3].'</td>
                <td colspan="3" class="text-center">
                    <small class="text-success">Data Kab/Kota Baru Berhasil Disimpan</small>
                </td>
            </tr>';
        } else {
            $html_output .= '
            <tr>
                <td>'.$i.'</td>
                <td>'.$sheetData[$i][0].'-'.$sheetData[$i][1].'</td>
                <td>'.$sheetData[$i][2].'-'.$sheetData[$i][3].'</td>
                <td colspan="3" class="text-center">
                    <small class="text-danger">Data Kab/Kota Baru Gagal Disimpan</small>
                </td>
            </tr>';
        }
    }

    //Cari id_region berdasarkan district_code
    $id_region = GetDetailData($Conn, 'region','district_code', $district_code, 'id_region');
    
    //Validasi Sekolah Duplikat berdasarkan npsn
    $id_school = GetDetailData($Conn, 'school','npsn', $npsn, 'id_school');
    
    if(!empty($id_school)){
        //Jika Sudah Ada Update sekolah
        $sql = "UPDATE school SET id_region = ?, npsn = ?, school_name = ? WHERE id_school = ?";
        $stmt = $Conn->prepare($sql);
        
        if (!$stmt) {
            $html_output .= '
            <tr>
                <td>'.$i.'</td>
                <td>'.$sheetData[$i][0].'-'.$sheetData[$i][1].'</td>
                <td>'.$sheetData[$i][2].'-'.$sheetData[$i][3].'</td>
                <td colspan="3" class="text-center">
                    <small class="text-danger">Prepare gagal: '.htmlspecialchars($Conn->error).'</small>
                </td>
            </tr>';
            continue;
        }
        
        $stmt->bind_param("issi", $id_region, $npsn, $school_name, $id_school);
        $Input = $stmt->execute();
        $stmt->close();
        
        if ($Input) {
            $html_output .= '
            <tr>
                <td>'.$i.'</td>
                <td>'.$sheetData[$i][0].'-'.$sheetData[$i][1].'</td>
                <td>'.$sheetData[$i][2].'-'.$sheetData[$i][3].'</td>
                <td>'.$sheetData[$i][4].'</td>
                <td>'.$sheetData[$i][5].'</td>
                <td class="text-center">
                    <small class="text-success">Update Berhasil</small>
                </td>
            </tr>';
            $JumlahKodeValid++;
        } else {
            $html_output .= '
            <tr>
                <td>'.$i.'</td>
                <td>'.$sheetData[$i][0].'-'.$sheetData[$i][1].'</td>
                <td>'.$sheetData[$i][2].'-'.$sheetData[$i][3].'</td>
                <td>'.$sheetData[$i][4].'</td>
                <td>'.$sheetData[$i][5].'</td>
                <td class="text-center">
                    <small class="text-danger">Update Gagal</small>
                </td>
            </tr>';
        }
    } else {
        //Jika Belum Ada Lakukan Insert
        $EntrySchool = "INSERT INTO school (id_region, npsn, school_name) VALUES ('$id_region', '$npsn', '$school_name')";
        $InputSchool = mysqli_query($Conn, $EntrySchool);
        
        if($InputSchool) {
            $html_output .= '
            <tr>
                <td>'.$i.'</td>
                <td>'.$sheetData[$i][0].'-'.$sheetData[$i][1].'</td>
                <td>'.$sheetData[$i][2].'-'.$sheetData[$i][3].'</td>
                <td>'.$sheetData[$i][4].'</td>
                <td>'.$sheetData[$i][5].'</td>
                <td class="text-center">
                    <small class="text-primary">Insert Berhasil</small>
                </td>
            </tr>';
            $JumlahKodeValid++;
        } else {
            $html_output .= '
            <tr>
                <td>'.$i.'</td>
                <td>'.$sheetData[$i][0].'-'.$sheetData[$i][1].'</td>
                <td>'.$sheetData[$i][2].'-'.$sheetData[$i][3].'</td>
                <td>'.$sheetData[$i][4].'</td>
                <td>'.$sheetData[$i][5].'</td>
                <td class="text-center">
                    <small class="text-danger">Insert Gagal</small>
                </td>
            </tr>';
        }
    }
}

// Hapus data session jika sudah selesai semua batch
if($current_batch == $total_batches) {
    unset($_SESSION['import_data_' . $file_token]);
    unset($_SESSION['import_total_rows_' . $file_token]);
}

// Tambahkan info batch
$html_output .= '
<tr>
    <td colspan="6" class="text-center">
        <small class="text-info">Batch ' . $current_batch . ' dari ' . $total_batches . ' selesai. ' . $JumlahKodeValid . ' data berhasil diproses.</small>
    </td>
</tr>';

echo json_encode([
    'status' => 'success',
    'html' => $html_output,
    'batch' => $current_batch,
    'total_batches' => $total_batches
]);
?>