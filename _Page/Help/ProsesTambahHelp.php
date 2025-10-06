<?php
    //Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    //Zona Waktu
    date_default_timezone_set('Asia/Jakarta');

    //Validasi Akses Login
    if(empty($SessionIdAccess)){
        echo '<span class="text-danger">Sesi Akses Sudah Berakhir, Silahkkan Login Ulang</span>';
    }else{
        $now=date('Y-m-d H:i:s');
        //Tangkap data
        if(empty($_POST['judul'])){
            echo '<span class="text-danger">Judul Konten Bantuan Tidak Boleh Kosong!</span>';
        }else{
            if(empty($_POST['kategori'])){
                echo '<span class="text-danger">Kategori Bantuan Tidak Boleh Kosong</span>';
            }else{
                if(empty($_POST['status'])){
                    echo '<span class="text-danger">Status Bantuan Tidak Boleh Kosong</span>';
                }else{
                    if(empty($_POST['deskripsi'])){
                        echo '<span class="text-danger">Isi Konten Bantuan Tidak Boleh Kosong</span>';
                    }else{
                        //Buat Variabel
                        $judul=$_POST['judul'];
                        $kategori=$_POST['kategori'];
                        $status=$_POST['status'];
                        $deskripsi=$_POST['deskripsi'];
                        //Bersihkan Variabel
                        $judul=validateAndSanitizeInput($judul);
                        $kategori=validateAndSanitizeInput($kategori);
                        $status=validateAndSanitizeInput($status);
                        $deskripsi=validateAndSanitizeInput($deskripsi);
                        $author=GetDetailData($Conn,'access ','id_access',$SessionIdAccess,'access_name');
                        $datetime_creat=date('Y-m-d H:i:s');
                        $datetime_update=date('Y-m-d H:i:s');
                        
                        //Simpan data
                        // Query dengan placeholder
                        $sql = "INSERT INTO help (
                                    author,
                                    judul,
                                    kategori,
                                    deskripsi,
                                    datetime_creat,
                                    datetime_update,
                                    status
                                ) VALUES (?,?,?,?,?,?,?)";

                        // Siapkan statement
                        $stmt = $Conn->prepare($sql);

                        // Bind parameter (semua string → gunakan "s" untuk setiap kolom)
                        $stmt->bind_param(
                            "sssssss", 
                            $author, 
                            $judul, 
                            $kategori, 
                            $deskripsi, 
                            $datetime_creat, 
                            $datetime_update, 
                            $status
                        );

                        // Eksekusi statement
                        if ($stmt->execute()) {

                            //Ambil id_help
                            $last_id = $stmt->insert_id;
                            
                            //Apabila Berhasil Kirim Notifikasi Ke Semua User

                            //Hitung jumlah User
                            $jumlah_user=mysqli_num_rows(mysqli_query($Conn, "SELECT id_access FROM access"));

                            //Looping database access
                            $jumlah_berhasil = 0;
                            $query_access = mysqli_query($Conn, "SELECT id_access FROM access");
                            while ($data_access = mysqli_fetch_array($query_access)) {

                                //Buat Variabel id_access
                                $id_access= $data_access['id_access'];

                                //Insert Data Notifikasi
                                $stmt2 = $Conn->prepare("INSERT INTO help_notification (id_help, id_access) VALUES (?, ?)");
                                if($stmt2){
                                    $stmt2->bind_param("ii", $last_id, $id_access);
                                    if($stmt2->execute()){
                                        $jumlah_berhasil = $jumlah_berhasil+1;
                                    }else{
                                        $jumlah_berhasil = $jumlah_berhasil+0;
                                    }
                                }else{
                                    $jumlah_berhasil = $jumlah_berhasil+0;
                                }
                            }

                            //Apabila Berhasil, Simpan Log
                            if($jumlah_berhasil==$jumlah_user){
                                $kategori_log="Dokumentasi";
                                $deskripsi_log="Tambah Dokumentasi";
                                $InputLog=addLog($Conn,$SessionIdAccess,$now,$kategori_log,$deskripsi_log);
                                if($InputLog=="Success"){
                                    $_SESSION ["NotifikasiSwal"]="Simpan Help Berhasil";
                                    echo '<small class="text-success" id="NotifikasiTambahHelpBerhasil">Success</small>';
                                }else{
                                    echo '<code class="text-danger">Terjadi kesalahan pada saat menyimpan Log</code>';
                                }
                            }else{
                                echo '<small class="text-danger">Terjadi kesalahan pada saat menyimpan notifikasi dokumentasi</small>';
                            }
                        } else {
                            echo '<small class="text-danger">Terjadi kesalahan pada saat menyimpan data pada database<br> Error : '.$stmt->error.'</small>';
                        }

                        // Tutup statement
                        $stmt->close();
                    }
                }
            }
        }
    }
?>