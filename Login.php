<!DOCTYPE html>
<html lang="en">
    <head>
        <?php
            //Koneksi
            session_start();
            include "_Config/Connection.php";
            include "_Config/GlobalFunction.php";
            include "_Config/SettingGeneral.php";
            include "_Partial/Head.php";
        ?>
    </head>
    <body>
        <main class="landing_background">
            <div class="container">
                <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-12 col-md-12 d-flex flex-column align-items-center justify-content-center">
                                <div class="card login_card mb-3">
                                    <?php
                                        if(empty($_GET['Page'])){
                                            include "_Page/Login/Login.php";
                                        }else{
                                            $Page=$_GET['Page'];
                                            if($Page=="LupaPassword"){
                                                include "_Page/ResetPassword/FormLupaPassword.php";
                                            }else{
                                                if($Page=="UbahPassword"){
                                                    include "_Page/ResetPassword/FormUbahPassword.php";
                                                }else{
                                                    if($Page=="VerifikasiKode"){
                                                        include "_Page/ResetPassword/VerifikasiKode.php";
                                                    }else{
                                                         if($Page=="UpdatePassword"){
                                                            include "_Page/ResetPassword/UpdatePassword.php";
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    ?>
                                </div>
                                <div class="credits text-center">
                                    <small>
                                        <div class="copyright text-white">
                                            &copy; Copyright <strong><span><?php echo "$app_title"; ?></span></strong>. All Rights Reserved <?php echo "$app_year"; ?>
                                        </div>
                                        <div class="credits text-white">
                                            Designed by <span class="text text-decoration-underline"><?php echo "$app_author"; ?></span>
                                        </div>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
    </main>
        <?php
            include "_Partial/FooterJs.php";
        ?>
        <script>

            //Fungsi Reload Captcha
            function reloadCaptcha() {
                document.getElementById('captchaImage').src = '_Page/Login/Captcha.php?' + new Date().getTime();
            }
            //Kondisi saat tampilkan password
            $('#TampilkanPassword2').click(function(){
                if($(this).is(':checked')){
                    $('#password').attr('type','text');
                    $('#password1').attr('type','text');
                    $('#password2').attr('type','text');
                }else{
                    $('#password').attr('type','password');
                    $('#password1').attr('type','password');
                    $('#password2').attr('type','password');
                }
            });

            //Submit Login
            $('#ProsesLogin').submit(function(){
                var ProsesLogin = $('#ProsesLogin').serialize();
                var Loading='<div class="spinner-border text-info" role="status"><span class="visually-hidden">Loading...</span></div>';
                $('#TombolLogin').html(Loading);
                $.ajax({
                    type 	    : 'POST',
                    url 	    : '_Page/Login/ProsesLogin.php',
                    data 	    :  ProsesLogin,
                    dataType    : 'json',
                    success     : function(response){
                        $('#TombolLogin').html('Login');
                        if (response.status === 'success') {
                            // Redirect jika login berhasil
                            window.location.href = 'index.php';
                        } else {
                            // Tampilkan notifikasi error jika gagal
                            $('#NotifikasiLogin').html('<div class="alert alert-danger">' + response.message + '</div>');
                        }
                    }
                });
            });

            //Kondisi saat tampilkan password
            $('.form-check-input').click(function(){
                if($(this).is(':checked')){
                    $('#PasswordBaru1').attr('type','text');
                    $('#PasswordBaru2').attr('type','text');
                }else{
                    $('#PasswordBaru1').attr('type','password');
                    $('#PasswordBaru2').attr('type','password');
                }
            });

            //Submit ProsesLupaPassword
            $('#ProsesLupaPassword').submit(function(){

                //Tangkap Data Dir Form
                var ProsesLupaPassword = $('#ProsesLupaPassword').serialize();

                //Loading Tombol
                var Loading='<div class="spinner-border text-info" role="status"><span class="visually-hidden">Loading...</span></div>';
                $('#TombolLupaPassword').html(Loading);

                //Kirim Data Melalui AJAX
                $.ajax({
                    type 	    : 'POST',
                    url 	    : '_Page/ResetPassword/ProsesLupaPassword.php',
                    data 	    :  ProsesLupaPassword,
                    dataType    : 'json',
                    success     : function(response){

                        //Pulihkan Tombol
                        $('#TombolLupaPassword').html('<i class="bi bi-send"></i> Kirim Kode');

                        //Jika Berhasil
                        if (response.status === 'success') {

                            //Tangkap Email
                            var my_email = response.email;

                            // Redirect jika Proses lupa password berhasil
                            window.location.href = 'Login.php?Page=VerifikasiKode&email='+my_email+'';
                        } else {
                            // Tampilkan notifikasi error jika gagal
                            $('#NotifikasiLupaPassword').html('<div class="alert alert-danger">' + response.message + '</div>');
                        }
                    }
                });
            });

            //Submit ProsesVerifikasiPemulihanAkun
            $('#ProsesVerifikasiPemulihanAkun').submit(function(){

                //Tangkap Data Dir Form
                var ProsesVerifikasiPemulihanAkun = $('#ProsesVerifikasiPemulihanAkun').serialize();

                //Loading Tombol
                var Loading='<div class="spinner-border text-info" role="status"><span class="visually-hidden">Loading...</span></div>';
                $('#TombolVerifikasiPemulihanAkun').html(Loading);

                //Kirim Data Melalui AJAX
                $.ajax({
                    type 	    : 'POST',
                    url 	    : '_Page/ResetPassword/ProsesVerifikasiPemulihanAkun.php',
                    data 	    :  ProsesVerifikasiPemulihanAkun,
                    dataType    : 'json',
                    success     : function(response){

                        //Pulihkan Tombol
                        $('#TombolVerifikasiPemulihanAkun').html('<i class="bi bi-send"></i> Verifikasi Kode');

                        //Jika Berhasil
                        if (response.status === 'success') {

                            //Tangkap Email
                            var my_email = response.email;
                            var my_code = response.kode_pemulihan;

                            // Redirect jika Proses lupa password berhasil
                            window.location.href = 'Login.php?Page=UpdatePassword&email='+my_email+'&code='+my_code+'';
                        } else {
                            // Tampilkan notifikasi error jika gagal
                            $('#NotifikasiVerifikasiPemulihanAkun').html('<div class="alert alert-danger">' + response.message + '</div>');
                        }
                    }
                });
            });

            //Submit ProsesUpdatePassword
            $('#ProsesUpdatePassword').submit(function(){

                //Tangkap Data Dir Form
                var ProsesUpdatePassword = $('#ProsesUpdatePassword').serialize();

                //Loading Tombol
                var Loading='<div class="spinner-border text-info" role="status"><span class="visually-hidden">Loading...</span></div>';
                $('#TombolUpdatePassword').html(Loading);

                //Kirim Data Melalui AJAX
                $.ajax({
                    type 	    : 'POST',
                    url 	    : '_Page/ResetPassword/ProsesUpdatePassword.php',
                    data 	    :  ProsesUpdatePassword,
                    dataType    : 'json',
                    success     : function(response){

                        //Pulihkan Tombol
                        $('#TombolUpdatePassword').html('<i class="bi bi-save"></i> Simpan Password');

                        //Jika Berhasil
                        if (response.status === 'success') {

                            Swal.fire({
                                title: 'Success!',
                                text: 'Password Baru Berhasil Disimpan',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = 'Login.php';
                                }
                            });
                        } else {
                            // Tampilkan notifikasi error jika gagal
                            $('#NotifikasiUpdatePassword').html('<div class="alert alert-danger">' + response.message + '</div>');
                        }
                    }
                });
            });

            // Jalankan reloadCaptcha setiap 1 menit (60.000 ms)
            setInterval(reloadCaptcha, 60000); // 60000 ms = 1 menit
        </script>
    </body>
</html>