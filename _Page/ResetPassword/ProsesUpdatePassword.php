<?php
    //Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";

    // Set header agar selalu mengembalikan JSON
    header('Content-Type: application/json');

    // Tambahkan beberapa header keamanan
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');

    //Time Now Tmp
    $now            = date('Y-m-d H:i:s');
    $timestamp_now  = date('Y-m-d H:i:s');

    // Inisialisasi respon default
    $response = [
        'status' => 'error',
        'message' => 'Terjadi kesalahan.'
    ];

    //Tangkap email
    if(empty($_POST['email'])){
        $response['status'] = 'error';
        $response['message'] = 'Email tidak boleh kosong.';
        echo json_encode($response); exit;
    }

    //Tangkap code
    if(empty($_POST['code'])){
        $response['status'] = 'error';
        $response['message'] = 'Kode Pemulihan Akun tidak boleh kosong.';
        echo json_encode($response); exit;
    }

    //Tangkap captcha
    if(empty($_POST['captcha'])){
        $response['status'] = 'error';
        $response['message'] = 'Kode captcha tidak boleh kosong.';
        echo json_encode($response); exit;
    }

    //Tangkap password1
    if(empty($_POST['password1'])){
        $response['status'] = 'error';
        $response['message'] = 'Password tidak boleh kosong.';
        echo json_encode($response); exit;
    }

    //Validasi password1 sama dengan password2
    if($_POST['password1']!==$_POST['password2']){
        $response['status'] = 'error';
        $response['message'] = 'Password yang anda masukan tidak sama.';
        echo json_encode($response); exit;
    }

    //Sanitasi Variabel
    $email      = validateAndSanitizeInput($_POST['email']);
    $captcha    = validateAndSanitizeInput($_POST['captcha']);
    $code       = validateAndSanitizeInput($_POST['code']);
    $password1  = validateAndSanitizeInput($_POST['password1']);
    $password2  = validateAndSanitizeInput($_POST['password2']);

    //Validasi Email
    $id_access = GetDetailData($Conn, 'access', 'access_email', $email, 'id_access');
    if(empty($id_access)){
        $response['status'] = 'error';
        $response['message'] = 'Email Tidak Valid';
        echo json_encode($response); exit;
    }

    // Validasi panjang password
    if(strlen($password1) < 6 || strlen($password1) > 20 || !preg_match("/^[a-zA-Z0-9]*$/", $password1)){
        $response['status'] = 'error';
        $response['message'] = 'Password harus 6-20 karakter huruf/angka!';
        echo json_encode($response); exit;
    }

    //Validasi Captcha
    $QryCaptcha = $Conn->prepare("SELECT * FROM captcha  WHERE captcha  = ?");
    $QryCaptcha->bind_param("s", $captcha);
    $QryCaptcha->execute();
    $DataCaptcha = $QryCaptcha->get_result()->fetch_assoc();

    if (!$DataCaptcha) {
        $response['status'] = 'error';
        $response['message'] = 'Captcha tidak valid!';
        echo json_encode($response); exit;
    }
    if($DataCaptcha['datetime_expired'] < $timestamp_now) {
        $response['status'] = 'error';
        $response['message'] = 'Captcha Sudah Expired!';
        echo json_encode($response); exit;
    }

    //Validasi Kode Pemulihan
    $stmt2 = $Conn->prepare("SELECT * FROM access_reset_password WHERE id_access = ?");
    $stmt2->bind_param("i", $id_access);
    $stmt2->execute();
    $DataPemulihan = $stmt2->get_result()->fetch_assoc();

    if(empty($DataPemulihan['recovery_code'])){
        $response['status'] = 'error';
        $response['message'] = 'Kode Pemulihan Akun Untuk ID : '.$id_access.' Tidak Ditemukan';
        echo json_encode($response); exit;
    }

    $recovery_code = $DataPemulihan['recovery_code'];

    //Validasi code (kode pemulihan akun)
    if ($DataPemulihan && password_verify($code, $DataPemulihan['recovery_code'])) {
        // Hash password
        $password = password_hash($password1, PASSWORD_DEFAULT);

        // Update Password dengan prepared statement
        $UpdateAkses = $Conn->prepare("UPDATE access SET access_password = ? WHERE id_access = ?");
        if($UpdateAkses){
            $UpdateAkses->bind_param("si", $password, $id_access);
            if($UpdateAkses->execute()){
                //Hapus data access_reset_password
                $DelReset = $Conn->prepare("DELETE FROM access_reset_password WHERE id_access = ?");
                $DelReset->bind_param("i", $id_access);
                $DelReset->execute();
                $DelReset->close();

                //Hapus data captcha
                $DelCaptcha = $Conn->prepare("DELETE FROM captcha WHERE captcha = ?");
                $DelCaptcha->bind_param("s", $captcha);
                $DelCaptcha->execute();
                $DelCaptcha->close();

                $response['status'] = 'success';
                $response['message'] = 'Password Berhasil Di Update';
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Gagal eksekusi query update password';
            }
            $UpdateAkses->close();
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Gagal menyiapkan query update password';
        }

        echo json_encode($response); exit;
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Kode Pemulihan Akun Tidak Valid : '.$code.' tidak sama '.$recovery_code;
        echo json_encode($response); exit;
    }
?>
