<div class="card-body">
    <div class="row">
        <div class="col-md-8 mb-3">
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <a href="" class="text-dark">
                        <h2 class="card-title">Verifikasi Kode Pemulihan Akun</h2>
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
                            <b>Petunjuk Pemulihan Akun : </b>
                            <ul>
                                <li>
                                    Isi alamat email pada form yang tersedia, kemudian klik pada tombol kirim kode pemulihan akun.
                                </li>
                                <li>
                                    Sistem akan mengirimkan kode pemulihan akun melalui email anda. 
                                </li>
                                <li>
                                    Setelah kode pemulihan di kirim, anda akan diarahkan ke halaman verifikasi kode pemulihan. 
                                </li>
                                <li>
                                    Masukan kode pemulihan akun yang anda peroleh dari <i>inbox</i> tadi pada form yang tersedia.
                                </li>
                                <li>
                                    Jika berhasil, anda bisa memasukan password baru pada halaman selanjutnya.
                                </li>
                            </ul>
                        </small>
                        
                    </div>
                </div>
            </div>
        </div>
        <?php
            if(empty($_GET['email'])){
                $email  = "";
            }else{
                $email  = $_GET['email'];
            }
        ?>
        <div class="col-md-4 mb-3">
            <form action="javascript:void(0);" class="row g-3" id="ProsesVerifikasiPemulihanAkun" autocomplete="off">
                <div class="row mt-3">
                    <div class="col-12">
                        <label for="email" class="form-label">
                            <small>Email Anda</small>
                        </label>
                        <div class="input-group has-validation">
                            <span class="input-group-text" id="inputGroupPrepend">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input type="email" name="email" class="form-control" id="email" value="<?php echo $email; ?>" required>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <small>
                            <label for="kode_pemulihan">Masukan Kode Pemulihan</label>
                        </small>
                        <div class="input-group has-validation">
                            <span class="input-group-text" id="inputGroupPrepend">
                                <i class="bi bi-key"></i>
                            </span>
                            <input type="text" name="kode_pemulihan" class="form-control" id="kode_pemulihan" required>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <?php
                            $random_string = generateRandomString(4);
                            $time_stamp = date('Ymdhis');
                            $version="$random_string-$time_stamp";
                        ?>
                        <img src="_Page/Login/Captcha.php?v=<?php echo $version; ?>" class="mb-2" id="captchaImage" alt="No Image" width="100%" style="border: 1px solid #ddd; margin-right: 10px;"/>
                        <a href="javascript:void(0);" onclick="reloadCaptcha()" title="Buat kode captcha baru">
                            <small>
                                <i class="bi bi-repeat"></i> Muat ulang kode captcha
                            </small>
                        </a>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <small>
                            Masukan karakter <i>Captcha</i>
                        </small>
                        <div class="input-group has-validation">
                            <span class="input-group-text" id="inputGroupPrepend">
                                <i class="bi bi-shield-exclamation"></i>
                            </span>
                            <input type="text" name="captcha" class="form-control" id="captcha" required>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12" id="NotifikasiVerifikasiPemulihanAkun"></div>
                    <div class="col-12">
                        <button class="btn btn-lg btn-primary w-100" type="submit" id="TombolVerifikasiPemulihanAkun">
                            <i class="bi bi-send"></i> Verifikasi Kode
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