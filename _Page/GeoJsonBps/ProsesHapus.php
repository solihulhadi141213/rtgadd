<?php
    //Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    //Validasi Session Akses
    if(empty($SessionIdAccess)){
        echo '
            <div class="alert alert-danger">
                <small>Sesi akses sudah berakhir. Silahkan <b>Login</b> ulang!</small>
            </div>
        ';
        exit;
    }

    //tangkap id_geo_region
    if(empty($_POST['id_geo_region'])){
        echo '
            <div class="alert alert-danger">
                <small>ID wilayah harus diisi terlebih dulu!</small>
            </div>
        ';
        exit;
    }

    $id_geo_region=$_POST['id_geo_region'];

    //Buka Data Lama
    $level_region       = GetDetailData($Conn, 'geo_region','id_geo_region', $id_geo_region, 'level_region');
    $province_code      = GetDetailData($Conn, 'geo_region','id_geo_region', $id_geo_region, 'province_code');
    $district_code      = GetDetailData($Conn, 'geo_region','id_geo_region', $id_geo_region, 'district_code');

    //Penanganan data untuk categori=='Province'
    if($level_region=="Province"){
        $HapusData = mysqli_query($Conn, "DELETE FROM geo_region WHERE province_code='$province_code'") or die(mysqli_error($Conn));
        if ($HapusData) {
            echo '
                <div class="alert alert-success">
                    <small>Hapus data wilayah <b id="NotifikasisHapusBerhasil">Berhasil</b></small>
                </div>
            ';
        }else{
            echo '
                <div class="alert alert-danger">
                    <small>Terjadi kesalahan pada saat menghapus data!</small>
                </div>
            ';
        }

    }else{
        //Penanganan data untuk categori=='District'
        $HapusData = mysqli_query($Conn, "DELETE FROM geo_region WHERE id_geo_region='$id_geo_region'") or die(mysqli_error($Conn));
        if ($HapusData) {
            
            //Mencari id_geo_region_provinsi
            $Qry = $Conn->prepare("SELECT * FROM geo_region WHERE province_code = ?");
            $Qry->bind_param("s", $province_code);
            if (!$Qry->execute()) {
                $error=$Conn->error;
                echo '
                    <div class="alert alert-danger">
                        <small>Terjadi kesalahan pada saat membuka data dari database!<br>Keterangan : '.$error.'</small>
                    </div>
                ';
                exit;
            }
            $Result = $Qry->get_result();
            $Data = $Result->fetch_assoc();
            $Qry->close();

            //Buat Variabel 
            $id_geo_region_provinsi = $Data['id_geo_region']; 

            //Tampilkan Pada Notifikasi
            echo '
                <div class="alert alert-success">
                    <small>Hapus data wilayah <b id="NotifikasisHapusBerhasil">Berhasil</b></small>
                </div>
                <input type="hidden" id="id_geo_region_put_edit" value="'.$id_geo_region_provinsi.'">
            ';
        }else{
            echo '
                <div class="alert alert-danger">
                    <small>Terjadi kesalahan pada saat menghapus data!</small>
                </div>
            ';
        }
    }

?>