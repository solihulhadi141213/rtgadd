<?php
    if(empty($_GET['id'])){
        echo '
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <h1><i class="bi bi-exclamation-triangle"></i></h1>
                                    <h3>Tidak Ada Dokumentasi Yang Dipilih</h3>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="alert alert alert-danger" role="alert">
                                        Silahkan kembali ke halaman utama untuk memilih konten dokumentasi yang diinginkan.
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <a href="index.php?Page=Dokumentasi" class="btn btn-md btn-secondary btn-rounded">
                                        <i class="bi bi-chevron-left"></i> Kembali
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        ';
    }else{
        $id_help = $_GET['id'];
        
        //Buka Data Help
        $Qry = $Conn->prepare("SELECT * FROM help WHERE id_help = ?");
        $Qry->bind_param("i", $id_help);
        if (!$Qry->execute()) {
            $error=$Conn->error;
            echo '
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <h1><i class="bi bi-exclamation-triangle"></i></h1>
                                        <h3>Terjadi Kesalahan Pada Saat Membuka Dokumentasi</h3>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="alert alert alert-danger" role="alert">'.$error.'</div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <a href="index.php?Page=Dokumentasi" class="btn btn-md btn-secondary btn-rounded">
                                            <i class="bi bi-chevron-left"></i> Kembali
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            ';
        }else{
            $Result = $Qry->get_result();
            $Data = $Result->fetch_assoc();
            $Qry->close();

            //Buat Variabel
            $author = $Data['author'];
            $judul = $Data['judul'];
            $kategori = $Data['kategori'];
            $deskripsi = $Data['deskripsi'];
            $datetime_creat = $Data['datetime_creat'];
            $datetime_update = $Data['datetime_update'];
            $status = $Data['status'];

            //Ubah Ke Element HTML
            $deskripsi = html_entity_decode($deskripsi);

            //Cek Apakah Ada Status Notifikasinya
            $QryStatusNotifikasi = $Conn->prepare("SELECT * FROM help_notification WHERE id_help = ? AND id_access = ?");
            $QryStatusNotifikasi->bind_param("ii", $id_help, $SessionIdAccess);
            if (!$QryStatusNotifikasi->execute()) {
                $error_status_notifikasi=$Conn->error;
                $id_help_notification="";
            }else{
                $ResultStatusNotifikasi = $QryStatusNotifikasi->get_result();
                $DataStatusNotifikasi = $ResultStatusNotifikasi->fetch_assoc();
                $QryStatusNotifikasi->close();

                //Buat Variabel
                if($DataStatusNotifikasi){
                    $id_help_notification = $DataStatusNotifikasi['id_help_notification'];
                }else{
                    $id_help_notification="";
                }
                
            }

            //Update Bahwa Dokumentasi ini Sudah Dibaca
            if(!empty($id_help_notification)){

                //Jika Sudah ADa Maka Update
                $UpdateHelpNotification = mysqli_query($Conn,"UPDATE help_notification SET read_status='1' WHERE id_help_notification='$id_help_notification'") or die(mysqli_error($Conn)); 
                if($UpdateHelpNotification){
                    $label_status_baca = '<span class="badge bg-success"><i class="bi bi-check"></i> Sudah Dibaca</span>';
                }else{
                    $label_status_baca = '<span class="badge bg-danger"><i class="bi bi-exclamation-triangle"></i> Terjadi Kesalahan</span>';
                }
            }else{
                //Jika Belum Ada Maka Insert
                $read_status = 1;
                $stmt2 = $Conn->prepare("INSERT INTO help_notification (id_help, id_access, read_status) VALUES (?, ?, ?)");

                if($stmt2){
                    $stmt2->bind_param("iii", $id_help, $SessionIdAccess, $read_status);
                    if($stmt2->execute()){
                        $label_status_baca = '<span class="badge bg-success"><i class="bi bi-check"></i> Sudah Dibaca</span>';
                    }else{
                        // Debugging error dari execute
                        $label_status_baca = '<span class="badge bg-danger"><i class="bi bi-exclamation-triangle"></i> Error Execute: '.$stmt2->error.'</span>';
                    }
                    $stmt2->close();
                }else{
                    // Debugging error dari prepare
                    $label_status_baca = '<span class="badge bg-danger"><i class="bi bi-exclamation-triangle"></i> Error Prepare: '.$Conn->error.'</span>';
                }

            }

            //Menampilkan Data
            echo '
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-10">
                                        <b class="card-title">'.$judul.'</b><br>
                                        <small>Kategori : <i>'.$kategori.'</i></small>
                                    </div>
                                    <div class="col-2 text-end">
                                        <a href="index.php?Page=Dokumentasi" class="btn btn-sm btn-secondary btn-floating">
                                            <i class="bi bi-chevron-left"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-12">'.$deskripsi.'</div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-8">
                                        <small>
                                            Oleh : '.$author.' | <i>'.date('d F Y H:i', strtotime($datetime_creat)).'</i>
                                        </small>
                                    </div>
                                    <div class="col-4 text-end">
                                        '.$label_status_baca.'
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            ';
        }
    }
?>