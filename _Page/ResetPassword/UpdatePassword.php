<?php
    //Validasi Email Dan Kode Pemulihan
    if(empty($_GET['email'])||empty($_GET['code'])){
        echo '
            <div class="card">
                <div class="row mb-3">
                    <div class="col-12 text-center">
                        <h2 class="card-title text-danger">Halaman Yang Anda Tuju Tidak Ditemukan</h2>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12 text-center">
                        <img src="assets/img/not_found.avif" widht="100%" alt="Page Not Found">
                    </div>
                </div>
            </div>
        ';
    }else{
        $email  = $_GET['email'];
        $code   = $_GET['code'];

        //Creat Captcha
        $captcha    = GenerateCaptcha(5);

        // Tetapkan waktu sekarang dan waktu expired
        $timestamp_now = date('Y-m-d H:i:s'); // Format untuk kolom TIMESTAMP
        $timestamp_expired = date('Y-m-d H:i:s', time() + 3600); // Tambahkan 60 detik untuk waktu expired

        // Hapus captcha yang sudah expired
        $deleteExpiredCaptchas = $Conn->prepare("DELETE FROM captcha WHERE datetime_expired < ?");
        $captchaCategory = 'Captcha';
        $deleteExpiredCaptchas->bind_param("s", $timestamp_now);
        $deleteExpiredCaptchas->execute();
        
        // Simpan Captcha Di Database
        $query = "INSERT INTO captcha (
            captcha, 
            datetime_creat, 
            datetime_expired
        ) VALUES (?, ?, ?)";

        // Persiapkan statement
        $stmt = $Conn->prepare($query);
        if ($stmt) {
            // Bind parameter
            $stmt->bind_param(
                "sss",
                $captcha,
                $timestamp_now,
                $timestamp_expired
            );

            // Eksekusi statement
            if ($stmt->execute()) {
                
                //Jika Berhasil Tampilkan Form
                echo '
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <div class="row mb-3">
                                    <div class="col-12 text-center">
                                        <a href="" class="text-dark">
                                            <h2 class="card-title">Buat Password Baru</h2>
                                        </a>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 text-center">
                                        <img src="assets/img/recovery.png" width="100%" alt="account Recovery">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="alert alert-info">
                                            <small>
                                                <ul>
                                                    <li>
                                                        Silahkan masukan password baru yang terdiri dari 6 - 20 karakter.
                                                    </li>
                                                    <li>
                                                        Gunakan gabungan karakter angka (0-9) dan huruf (a-z) untuk meningkatkan keamanan.
                                                    </li>
                                                </ul>
                                            </small>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <form action="javascript:void(0);" class="row g-3" id="ProsesUpdatePassword" autocomplete="off">
                                    <input type="hidden" name="captcha" value="'.$captcha.'">
                                    <input type="hidden" name="email" value="'.$email.'">
                                    <input type="hidden" name="code" value="'.$code.'">
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <label for="password" class="form-label">
                                                <small>Buat Password Baru</small>
                                            </label>
                                            <div class="input-group has-validation">
                                                <span class="input-group-text" id="inputGroupPrepend">
                                                    <i class="bi bi-key"></i>
                                                </span>
                                                <input type="password" name="password1" class="form-control" id="password1" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <label for="password" class="form-label">
                                                <small>Ulangi Password</small>
                                            </label>
                                            <div class="input-group has-validation">
                                                <span class="input-group-text" id="inputGroupPrepend">
                                                    <i class="bi bi-key"></i>
                                                </span>
                                                <input type="password" name="password2" class="form-control" id="password2" required>
                                            </div>
                                            <small class="credit">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="Tampilkan" id="TampilkanPassword2" name="TampilkanPassword2">
                                                    <label class="form-check-label" for="TampilkanPassword2">
                                                        Tampilkan Password
                                                    </label>
                                                </div>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-12" id="NotifikasiUpdatePassword"></div>
                                        <div class="col-12">
                                            <button class="btn btn-lg btn-primary w-100" type="submit" id="TombolUpdatePassword">
                                                <i class="bi bi-save"></i> Simpan Password
                                            </button>
                                        </div>
                                        <div class="col-12">
                                            <p class="small mb-0">Kembali ke halaman <a href="Login.php">Login</a></p>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                ';

            } else {
                echo '
                    <div class="card-body">
                        <div class="alert alert-danger">Terjadi Kesalahan Pada Saat menyimpan Captcha ke database.</div>
                    </div>
                ';
            }

            // Tutup statement
            $stmt->close();
        } else {
            echo '
                <div class="card-body">
                    <div class="alert alert-danger">Terjadi Kesalahan (Gagal mempersiapkan query)</div>
                </div>
            ';
        }
        // Tutup koneksi database
        $Conn->close();
    }
?>

