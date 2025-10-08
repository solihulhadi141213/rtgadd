<?php
    session_start();
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/SettingEmail.php";
    include "../../_Config/SettingGeneral.php";
    
    // Set header agar selalu mengembalikan JSON
    header('Content-Type: application/json');

    // Tambahkan beberapa header keamanan
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');

    // Timestamp sekarang
    $timestamp_now = date('Y-m-d H:i:s');

    // Fungsi untuk membuat token
    function generateTokenNew($length) {
        return bin2hex(random_bytes($length / 2));
    }

    // Fungsi untuk memvalidasi input
    function validateAndSanitizeInputNew($data) {
        return htmlspecialchars(stripslashes(trim($data)));
    }

    // Inisialisasi respon default
    $response = [
        'status' => 'error',
        'message' => 'Terjadi kesalahan.'
    ];

    // Validasi input Tidak Boleh Kosong
    $email          = isset($_POST["email"]) ? filter_var(validateAndSanitizeInputNew($_POST["email"]), FILTER_VALIDATE_EMAIL) : null;
    $captcha        = isset($_POST["captcha"]) ? validateAndSanitizeInputNew($_POST["captcha"]) : null;

    if(empty($_POST["kode_pemulihan"])){
        $response['message'] = 'Kode Pemulihan tidak boleh kosong.';
    }else{
        $kode_pemulihan = $_POST["kode_pemulihan"];
    }
    

    if (!$email) {
        $response['message'] = 'Email tidak valid atau kosong.';
    } elseif (empty($captcha)) {
        $response['message'] = 'Captcha tidak boleh kosong.';
    } elseif (empty($kode_pemulihan)) {
        $response['message'] = 'Kode Pemulihan tidak boleh kosong.';
    } else {
        
        // Validasi Captcha
        $QryCaptcha = $Conn->prepare("SELECT * FROM captcha  WHERE captcha  = ?");
        $QryCaptcha->bind_param("s", $captcha);
        $QryCaptcha->execute();
        $DataCaptcha = $QryCaptcha->get_result()->fetch_assoc();

        if (!$DataCaptcha) {
            $response['message'] = 'Captcha tidak valid.';
        } elseif ($DataCaptcha['datetime_expired'] < $timestamp_now) {
            $response['message'] = 'Captcha expired.';
        } else {

            // Validasi Email
            $status_akses = 1;
            $stmt = $Conn->prepare("SELECT * FROM access  WHERE access_email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $DataAkses = $stmt->get_result()->fetch_assoc();
            if (!empty($DataAkses["id_access"])) {
                $id_access      = $DataAkses["id_access"];
                $access_name    = $DataAkses["access_name"];

                //Validasi Kode Pemulihan
                $stmt2 = $Conn->prepare("SELECT * FROM access_reset_password WHERE id_access = ?");
                $stmt2->bind_param("i", $id_access);
                $stmt2->execute();
                $DataPemulihan = $stmt2->get_result()->fetch_assoc();

                //Validasi kode_pemulihan
                if ($DataPemulihan && password_verify($kode_pemulihan, $DataPemulihan['recovery_code'])) {

                    //Waktu Expired
                    $datetime_expired = $DataPemulihan['datetime_expired'];

                    //Validasi Waktu Expired
                    if ($datetime_expired < $timestamp_now) {
                        $response['message'] = 'Kode pemulihan akun yang anda gunakan sudah Expired';
                    }else{
                        //Jika Berhasil Kirimkan Response
                        $response['status']             = 'success';
                        $response['message']            = 'Kode Yang Anda Masukan Valid';
                        $response['email']              = $email;
                        $response['kode_pemulihan']     = $kode_pemulihan;
                    }

                }else{
                     $response['message'] = 'Kode Pemulihan Akun Yang Anda Masukan Tidak Valid: <b>'.$kode_pemulihan.'</b>';
                }

            } else {
                $response['message'] = 'Email Tidak Valid.';
            }
        }
    }

    // Output respon sebagai JSON
    echo json_encode($response);
?>
