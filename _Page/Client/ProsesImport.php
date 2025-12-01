<?php
// ======================================================
// KONEKSI & DEPENDENSI
// ======================================================
include "../../_Config/Connection.php";
include "../../_Config/GlobalFunction.php";
include "../../_Config/Session.php";
include "../../_Config/SettingEmail.php";
include "../../_Config/SettingGeneral.php";

date_default_timezone_set('Asia/Jakarta');
$now = date('Y-m-d H:i:s');

// ======================================================
// VALIDASI REQUEST
// ======================================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo 'Invalid request';
    exit;
}

// ======================================================
// VALIDASI FILE CSV
// ======================================================
if (!isset($_FILES['file_client']) || $_FILES['file_client']['error'] !== UPLOAD_ERR_OK) {
    echo 'File CSV tidak ditemukan';
    exit;
}

$ext = strtolower(pathinfo($_FILES['file_client']['name'], PATHINFO_EXTENSION));
if ($ext !== 'csv') {
    echo 'Hanya file CSV yang diijinkan';
    exit;
}

if ($_FILES['file_client']['size'] > (2 * 1024 * 1024)) {
    echo 'Ukuran file maksimal 2 MB';
    exit;
}

// ======================================================
// BACA FILE CSV
// ======================================================
$csvFile = $_FILES['file_client']['tmp_name'];
$handle  = fopen($csvFile, 'r');

if ($handle === false) {
    echo 'Gagal membuka file CSV';
    exit;
}

// ======================================================
// DETEKSI DELIMITER OTOMATIS
// ======================================================
$firstLine = fgets($handle);
rewind($handle);
$delimiter = (strpos($firstLine, ';') !== false) ? ';' : ',';   // otomatis ; atau ,

// ======================================================
// BACA HEADER
// ======================================================
$original_header = fgetcsv($handle, 0, $delimiter);
if ($original_header === false) {
    echo 'Header CSV tidak valid';
    exit;
}

// Hilangkan BOM (jika ada)
$original_header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $original_header[0]);

// Normalisasi nama header menjadi lowercase + underscore
$normalized_header = array_map(function ($h) {
    return strtolower(trim(str_replace([' ', '-', '.', '/', '\\'], '_', $h)));
}, $original_header);

// ======================================================
// BACA ISI CSV → FORMAT ARRAY ASSOCIATIVE
// ======================================================
$data = [];

while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {

    // Abaikan baris kosong
    if (count(array_filter($row)) == 0) continue;

    // Abaikan jika jumlah kolom beda
    if (count($row) != count($normalized_header)) continue;

    // Gabungkan header + data
    $data[] = array_combine($normalized_header, $row);
}

fclose($handle);

// ======================================================
// LOOP SETIAP BARIS CSV MENJADI VARIABEL
// ======================================================
foreach ($data as $row) {

    $province_code = trim($row['province_code'] ?? '');
    $district_code = trim($row['district_code'] ?? '');
    $access_name   = trim($row['access_name'] ?? '');
    $access_email  = trim($row['access_email'] ?? '');
    $level         = trim($row['level'] ?? '');
    $keterangan    = trim($row['keterangan'] ?? '');

    //Buat password
    $password       = generateRandomString(7);
    $password_hash  = password_hash($password, PASSWORD_DEFAULT);

    //bangun variabel dasar
    $id_access_group    = null; // access_group
    $access_client      = 1;    // access_client
    $access_contact     = "";
    $access_foto        ="";

    //Validasi Email Duplikat
    $validasi_email_duplikat = mysqli_num_rows(mysqli_query($Conn, "SELECT id_access FROM access WHERE access_email='$access_email'"));
    if(!empty($validasi_email_duplikat)){
       $response_baris = "Email $access_email sudah terdaftar";
    }else{
        if(empty($level)){
            $response_baris = "Level Tidak Boleh Kosong";
        }else{
            if(empty($access_email)){
                $response_baris = "Email Tidak Boleh Kosong";
            }else{
                if(empty($access_name)){
                    $response_baris = "Nama Tidak Boleh Kosong";
                }else{
                    
                    //Cari id_region berdasarkan level
                    if($level=="National"){
                        $id_region = null;
                        $proses_cari_region ='success';
                    }else{
                        if($level=="Province"){
                            $QryRegion = mysqli_query($Conn,"SELECT * FROM region WHERE category='Province' AND province_code='$province_code'")or die(mysqli_error($Conn));
                            $DataRegion = mysqli_fetch_array($QryRegion);
                            $id_region = $DataRegion['id_region'];
                            $proses_cari_region ='success';
                        }else{
                            if($level=="District"){
                                $QryRegion = mysqli_query($Conn,"SELECT * FROM region WHERE category='District' AND district_code='$district_code'")or die(mysqli_error($Conn));
                                $DataRegion = mysqli_fetch_array($QryRegion);
                                $id_region = $DataRegion['id_region'];
                                $proses_cari_region ='success';
                            }else{
                                $id_region = null;
                                $proses_cari_region ='Gagal Mencari ID Region';
                            }
                        }
                    }
                    if($proses_cari_region!=='success'){
                        $response_baris = $proses_cari_region;
                    }else{
                        // === Gunakan Transaction untuk rollback jika gagal ===
                        $Conn->begin_transaction();

                        try {
                            //Insert ke table access
                            $stmt = $Conn->prepare("INSERT INTO access (id_access_group, access_name, access_email, access_contact, access_password, access_foto, access_client) 
                                                    VALUES (?, ?, ?, ?, ?, ?, ?)");
                            $stmt->bind_param("issssss", $id_access_group, $access_name, $access_email, $access_contact, $password_hash, $access_foto, $access_client);

                            if(!$stmt->execute()){
                                if($path != "" && file_exists($path)) unlink($path);
                                throw new Exception("Gagal menyimpan data akses: ".$stmt->error);
                            }

                            $id_access = $stmt->insert_id; 
                            $stmt->close();

                            //Insert ke table access_client
                            $stmt2 = $Conn->prepare("INSERT INTO access_client (id_access, id_region, level, keterangan) VALUES (?, ?, ?, ?)");
                            $stmt2->bind_param("iiss", $id_access, $id_region, $level, $keterangan);

                            if(!$stmt2->execute()){
                                if($path != "" && file_exists($path)) unlink($path);
                                throw new Exception("Gagal menyimpan data access_client: ".$stmt2->error);
                            }

                            $stmt2->close();

                            //Jika semua berhasil → Commit
                            $Conn->commit();

                            //Kirim email jika 'Ya'
                            $nama_tujuan    = $access_name;
                            $email_tujuan   = $access_email;
                            $subjek         = "Credential Login - $app_title";
                            $pesan          = '
                            Kepada YTH. <b>'.$access_name.'</b> <br> 
                            Berikut ini kami sampaikan credential akses ke aplikasi <b>'.$app_title.'</b> untuk dapat melakukan login dan mengubah password standar yang sudah ada.
                            <p>
                                <b>Email : </b> '.$access_email.'<br>
                                <b>Password : </b> '.$password.'<br>
                                <b>URL Aplikasi : </b> '.$app_base_url.'<br>
                            </p>
                            ';

                            $kirim_email=SendEmail($nama_tujuan,$email_tujuan,$subjek,$pesan,$email_gateway,$password_gateway,$url_provider,$nama_pengirim,$port_gateway,$url_service);
                            $response_baris = "User atas nama $access_name (berhasil)";

                        } catch (Exception $e) {
                            $Conn->rollback();
                            $response_baris = $e->getMessage();
                        }
                    }
                }
            }
        }
    }

    echo '
        <div class="row mb-2">
            <div class="col-12">
                '.$password.' - '.$response_baris.'
            </div>
        </div>
    ';
}
?>
