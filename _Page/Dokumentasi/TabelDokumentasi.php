<?php
    //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    date_default_timezone_set("Asia/Jakarta");
    $JmlHalaman=0;
    $page=0;
    //Validasi Akses
    if(empty($SessionIdAccess)){
        echo '
            <tr>
                <td colspan="6" class="text-center">
                    <small class="text-danger">Sesi Akses Sudah Berakhir! Silahkan Login Ulang!</small>
                </td>
            </tr>
        ';
        exit;
    }
    //Keyword_by
    if(!empty($_POST['keyword_by'])){
        $keyword_by=$_POST['keyword_by'];
    }else{
        $keyword_by="";
    }
    //keyword
    if(!empty($_POST['keyword'])){
        $keyword=$_POST['keyword'];
    }else{
        $keyword="";
    }
    //batas
    if(!empty($_POST['batas'])){
        $batas=$_POST['batas'];
    }else{
        $batas="10";
    }
    //ShortBy
    if(!empty($_POST['ShortBy'])){
        $ShortBy=$_POST['ShortBy'];
    }else{
        $ShortBy="DESC";
    }
    //OrderBy
    if(!empty($_POST['OrderBy'])){
        $OrderBy=$_POST['OrderBy'];
    }else{
        $OrderBy="id_help";
    }
    //Atur Page
    if(!empty($_POST['page'])){
        $page=$_POST['page'];
        $posisi = ( $page - 1 ) * $batas;
    }else{
        $page="1";
        $posisi = 0;
    }

    //Hitung Jumlah Data
    // --- Hitung Jumlah Data ---
    $baseQuery = "
        FROM help h
        LEFT JOIN help_notification hn 
            ON h.id_help = hn.id_help 
            AND hn.id_access = '$SessionIdAccess'
    ";

    // Hitung jumlah data
    if(empty($keyword_by)){
        if(empty($keyword)){
            $sqlCount = "SELECT COUNT(h.id_help) as jml ".$baseQuery;
        }else{
            $sqlCount = "SELECT COUNT(h.id_help) as jml ".$baseQuery."
                        WHERE h.author LIKE '%$keyword%' 
                            OR h.judul LIKE '%$keyword%' 
                            OR h.kategori LIKE '%$keyword%' 
                            OR h.deskripsi LIKE '%$keyword%' 
                            OR h.datetime_creat LIKE '%$keyword%' 
                            OR h.status LIKE '%$keyword%' 
                            OR hn.read_status LIKE '%$keyword%'";
        }
    }else{
        if(empty($keyword)){
            $sqlCount = "SELECT COUNT(h.id_help) as jml ".$baseQuery;
        }else{
            // keyword_by bisa "read_status" atau kolom lain
            if($keyword_by=="read_status"){
                $sqlCount = "SELECT COUNT(h.id_help) as jml ".$baseQuery."
                            WHERE hn.read_status LIKE '%$keyword%'";
            }else{
                $sqlCount = "SELECT COUNT(h.id_help) as jml ".$baseQuery."
                            WHERE h.$keyword_by LIKE '%$keyword%'";
            }
        }
    }

    $resCount = mysqli_query($Conn, $sqlCount);
    $rowCount = mysqli_fetch_assoc($resCount);
    $jml_data = $rowCount['jml'];
    $JmlHalaman = ceil($jml_data/$batas);
        
    
    if(empty($jml_data)){
        echo '
            <tr>
                <td colspan="6" class="text-center">
                    <small class="text-danger">Tidak Ada Data Yang Ditampilkan!</small>
                </td>
            </tr>
        ';
        exit;
    }
    $no = 1+$posisi;
    //KONDISI PENGATURAN MASING FILTER
    // --- Query Data Utama ---
    if(empty($keyword_by)){
        if(empty($keyword)){
            $sqlData = "SELECT h.*, hn.read_status as notif_read_status ".$baseQuery."
                        ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas";
        }else{
            $sqlData = "SELECT h.*, hn.read_status as notif_read_status ".$baseQuery."
                        WHERE h.author LIKE '%$keyword%' 
                        OR h.judul LIKE '%$keyword%' 
                        OR h.kategori LIKE '%$keyword%' 
                        OR h.deskripsi LIKE '%$keyword%' 
                        OR h.datetime_creat LIKE '%$keyword%' 
                        OR h.status LIKE '%$keyword%' 
                        OR hn.read_status LIKE '%$keyword%'
                        ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas";
        }
    }else{
        if(empty($keyword)){
            $sqlData = "SELECT h.*, hn.read_status as notif_read_status ".$baseQuery."
                        ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas";
        }else{
            if($keyword_by=="read_status"){
                $sqlData = "SELECT h.*, hn.read_status as notif_read_status ".$baseQuery."
                            WHERE hn.read_status LIKE '%$keyword%'
                            ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas";
            }else{
                $sqlData = "SELECT h.*, hn.read_status as notif_read_status ".$baseQuery."
                            WHERE h.$keyword_by LIKE '%$keyword%'
                            ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas";
            }
        }
    }

    $query = mysqli_query($Conn, $sqlData);
    
    while ($data = mysqli_fetch_array($query)) {
        $id_help        = $data['id_help'];
        $judul          = $data['judul'];
        $kategori       = $data['kategori'];
        $datetime_creat = $data['datetime_creat'];
        $status_dibaca  = $data['notif_read_status'];

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

        if(!empty($id_help_notification)){
            if($status_dibaca==1){
                $label_status = '<span class="badge bg-success"><i class="bi bi-check"></i> Sudah Dibaca</span>';
            }else{
                $label_status = '<span class="badge bg-danger"><i class="bi bi-exclamation-circle"></i> Belum Dibaca</span>';
            }
        }else{
            $label_status = '<span class="badge bg-danger"><i class="bi bi-exclamation-circle"></i> Belum Dibaca</span>';
        }

        echo "
            <tr>
                <td><small>$no</small></td>
                <td>
                    <a href='index.php?Page=Dokumentasi&Sub=Detail&id=$id_help'>
                        <small>$judul</small>
                    </a>
                </td>
                <td><small>$kategori</small></td>
                <td><small>".date('d F Y H:i',strtotime($datetime_creat))."</small></td>
                <td><small>$label_status</small></td>
                <td>
                    <a href='index.php?Page=Dokumentasi&Sub=Detail&id=$id_help' class='btn btn-sm btn-primary btn-floating'>
                        <i class='bi bi-chevron-right'></i>
                    </a>
                </td>
            </tr>
        ";
        $no++;
    }
?>
<script>
    //Creat Javascript Variabel
    var data_count=<?php echo "$jml_data"; ?>;
    var page_count=<?php echo $JmlHalaman; ?>;
    var curent_page=<?php echo $page; ?>;
    
    //Put Into Pagging Element
    $('#data_count').html('Data : '+data_count+' Record');
    $('#page_info').html('Page '+curent_page+' / '+page_count+'');
    
    //Set Pagging Button
    if(curent_page==1){
        $('#prev_button').prop('disabled', true);
    }else{
        $('#prev_button').prop('disabled', false);
    }
    if(page_count<=curent_page){
        $('#next_button').prop('disabled', true);
    }else{
        $('#next_button').prop('disabled', false);
    }
</script>