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
    $email = isset($_POST["email"]) ? filter_var(validateAndSanitizeInputNew($_POST["email"]), FILTER_VALIDATE_EMAIL) : null;
    $captcha = isset($_POST["captcha"]) ? validateAndSanitizeInputNew($_POST["captcha"]) : null;

    if (!$email) {
        $response['message'] = 'Email tidak valid atau kosong.';
    } elseif (empty($captcha)) {
        $response['message'] = 'Captcha tidak boleh kosong.';
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

                //Buat 10 Digit Kode Pemulihan Akun
                $recovery_code = GenerateCaptcha(6);

                //Hash
                $recovery_code_hash = password_hash($recovery_code, PASSWORD_DEFAULT);

                // Atur waktu Expired
                $expired_seconds = 60 * 30; // 0,5 hour
                $date_expired = date('Y-m-d H:i:s', strtotime($timestamp_now) + $expired_seconds);

                //Hapus Data Lama
                $HapusDataLama = mysqli_query($Conn, "DELETE FROM access_reset_password WHERE id_access='$id_access'") or die(mysqli_error($Conn));
                if(!$HapusDataLama){
                    $response['message']    = 'Terjadi kesalahan pada saat menghapus data lama';
                }else{

                    //Simpan Ke Database
                    $insertTokenStmt = $Conn->prepare("INSERT INTO  access_reset_password (id_access, recovery_code, datetime_creat, datetime_expired) VALUES (?, ?, ?, ?)");
                    $insertTokenStmt->bind_param("isss", $id_access, $recovery_code_hash, $timestamp_now, $date_expired);
                    if ($insertTokenStmt->execute()) {
                        
                        //Jika Berhasil, Persiapkan Mengirim Email
                        $nama_tujuan    = $access_name;
                        $email_tujuan   = $email;
                        $subjek         = "Account recovery - $app_title";
                        $pesan          = '
                        Kepada YTH. <b>'.$access_name.'</b> <br> 
                        Kami menerima permintaan untuk melakukan pemulihan akun Anda.<br> 
                        Gunakan kode berikut untuk melanjutkan proses: <br> 
                        <h2>'.$recovery_code.'</h2><br>
                        <p>Demi keamanan, jangan bagikan kode ini kepada siapa pun. Kode hanya berlaku selama 30 menit.</p>
                        ';

                        $kirim_email=SendEmail($nama_tujuan,$email_tujuan,$subjek,$pesan,$email_gateway,$password_gateway,$url_provider,$nama_pengirim,$port_gateway,$url_service);

                        //Tampilkan Response
                        $response['status']     = 'success';
                        $response['message']    = 'Kode Berhasil Dikirim.';
                        $response['email']      = $email;
                    }else{
                        $response['message'] = 'Terjadi kesalahan pada saat menyimpan data account recovery';
                    }
                }
            } else {
                $response['message'] = 'Kombinasi email dan password tidak valid.';
            }
        }
    }

    // Output respon sebagai JSON
    echo json_encode($response);
?>
