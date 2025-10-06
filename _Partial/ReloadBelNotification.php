<?php
    //Karena Ini Di running Dengan JS maka Panggil Ulang Koneksi
    include "../_Config/Connection.php";
    include "../_Config/GlobalFunction.php";
    include "../_Config/Session.php";

    //Keterangan Waktu
    date_default_timezone_set("Asia/Jakarta");

    //Menghitung Jumlah Notifikasi
    if(empty($SessionIdAccess)){
        $JumlahNotifikasi = "";
    }else{
        $JumlahNotifikasi = mysqli_num_rows(
            mysqli_query(
                $Conn,
                "SELECT id_help_notification 
                FROM help_notification 
                WHERE id_access='$SessionIdAccess' 
                AND read_status IS NULL"
            )
        );
    }
    
    //Apabila ada notifgikasi
    if(!empty($JumlahNotifikasi)){
        echo '<i class="bi bi-bell"></i>';
        echo '<span class="badge bg-danger rounded-pill badge-number">'.$JumlahNotifikasi.'</span>';
    }else{
        //Apabila Tidak Ada
        echo '<i class="bi bi-bell"></i>';
    }
?>