<?php
    //Karena Ini Di running Dengan JS maka Panggil Ulang Koneksi
    include "../_Config/Connection.php";
    include "../_Config/GlobalFunction.php";
    include "../_Config/Session.php";

    //Fungsi Durasi
    function timeAgo($datetime_creat){
        $now = new DateTime(); // waktu sekarang
        $created = new DateTime($datetime_creat);
        $diff = $now->diff($created);

        // Total detik
        $seconds = time() - strtotime($datetime_creat);

        if ($seconds < 60) {
            return "Barusan";
        } elseif ($seconds < 3600) {
            return $diff->i . " Menit Yang Lalu";
        } elseif ($seconds < 86400) {
            return $diff->h . " Jam Yang Lalu";
        } elseif ($seconds < 604800) {
            return $diff->d . " Hari Yang Lalu";
        } elseif ($seconds < 2592000) {
            $weeks = floor($diff->days / 7);
            return $weeks . " Minggu Yang Lalu";
        } elseif ($seconds < 31536000) {
            return $diff->m . " Bulan Yang Lalu";
        } else {
            return $diff->y . " Tahun Yang Lalu";
        }
    }

    //Validasi Sesi Akses
    if(empty($SessionIdAccess)){
        echo '
            <li class="dropdown-header">
                <div class="alert alert-danger">
                    <small>No Access</small>
                </div>
            </li>
        ';
        exit;
    }

    //Jika Tidak Ada Notifikasi
    $jumlah_notifikasi = mysqli_num_rows(
        mysqli_query(
            $Conn,
            "SELECT id_help_notification 
            FROM help_notification 
            WHERE id_access='$SessionIdAccess' 
            AND read_status IS NULL"
        )
    );
    if(empty($jumlah_notifikasi)){
        echo '
            <li class="dropdown-header">
                <i class="bi bi-check-circle text-success"></i> Anda Sudah Membaca Semua Notifikasi
            </li>
        ';
        exit;
    }

    //Jika Ada Notifikasi
    $qry_notifikasi = mysqli_query($Conn, "SELECT * FROM help_notification WHERE id_access='$SessionIdAccess' AND read_status IS NULL");
    while ($data_notifikasi = mysqli_fetch_array($qry_notifikasi)) {
        $id_help_notification   = $data_notifikasi['id_help_notification'];
        $id_help                = $data_notifikasi['id_help'];

        //Buka Rincian Dokumentasi
        $judul  = GetDetailData($Conn, 'help', 'id_help', $id_help, 'judul');
        $author  = GetDetailData($Conn, 'help', 'id_help', $id_help, 'author');
        $datetime_creat  = GetDetailData($Conn, 'help', 'id_help', $id_help, 'datetime_creat');

        //Label Durasi
        $label_durasi = timeAgo($datetime_creat);
        echo '
            <li class="dropdown-header">
                Anda memiliki '.$jumlah_notifikasi.' Pemberitahuan
            </li>
        ';
        echo '
            <li class="notification-item">
                <i class="bi bi-info-circle text-primary"></i>
                <div>
                    <h4>
                        <a href="index.php?Page=Dokumentasi&Sub=Detail&id='.$id_help.'">'.$judul.'</a>
                    </h4>
                    <p>'.$label_durasi.'</p>
                </div>
            </li>
        ';
    }
?>