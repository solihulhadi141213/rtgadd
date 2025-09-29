<?php
    //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";
    date_default_timezone_set("Asia/Jakarta");

    //Inisiasi Variabel Agar Tidak Error
    $jml_data=0;
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
        $batas="10"; //batas default
    }

    //ShortBy
    if(!empty($_POST['ShortBy'])){
        $ShortBy=$_POST['ShortBy'];
    }else{
        $ShortBy="DESC"; //ShortBy default
    }

    //OrderBy
    if(!empty($_POST['OrderBy'])){
        $OrderBy=$_POST['OrderBy'];
    }else{
        $OrderBy="id_school"; //OrderBy default
    }

    //Whitelist kolom yang diizinkan
    $allowed_columns = [
        'id_school','npsn','school_name',
        'province_code','province_name','district_code','district_name'
    ];
    if(!in_array($OrderBy,$allowed_columns)){ $OrderBy='id_school'; }
    if(!in_array($keyword_by,$allowed_columns)){ $keyword_by=''; }

    //Atur Page
    if(!empty($_POST['page'])){
        $page=$_POST['page'];
        $posisi = ( $page - 1 ) * $batas;
    }else{
        $page="1";
        $posisi = 0;
    }

    // MENGHITUNG JUMLAH DATA SEKOLAH
    if(empty($keyword_by)){
        if(empty($keyword)){
            $sqlCount = "
                SELECT COUNT(*) as jml 
                FROM school 
                LEFT JOIN region ON school.id_region=region.id_region
            ";
        }else{
            $sqlCount = "
                SELECT COUNT(*) as jml 
                FROM school 
                LEFT JOIN region ON school.id_region=region.id_region
                WHERE npsn LIKE '%$keyword%' 
                   OR school_name LIKE '%$keyword%'
                   OR province_code LIKE '%$keyword%' 
                   OR province_name LIKE '%$keyword%'
                   OR district_code LIKE '%$keyword%' 
                   OR district_name LIKE '%$keyword%'
            ";
        }
    }else{
        if(empty($keyword)){
            $sqlCount = "
                SELECT COUNT(*) as jml 
                FROM school 
                LEFT JOIN region ON school.id_region=region.id_region
            ";
        }else{
            $sqlCount = "
                SELECT COUNT(*) as jml 
                FROM school 
                LEFT JOIN region ON school.id_region=region.id_region
                WHERE $keyword_by LIKE '%$keyword%'
            ";
        }
    }
    $resCount = mysqli_query($Conn, $sqlCount);
    $rowCount = mysqli_fetch_assoc($resCount);
    $jml_data = $rowCount['jml'];

    //Menghitung Jumlah Halaman
    $JmlHalaman = ceil($jml_data/$batas); 

    //Jika Data Kosong
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

    //LOOPING UNTUK MENAMPILKAN DATA
    //Nomor Baris
    $no = 1+$posisi;
    
    //Query Berdasarkan filter
    if(empty($keyword_by)){
        if(empty($keyword)){
            $sql = "
                SELECT school.*, region.province_code, region.province_name, region.district_code, region.district_name
                FROM school 
                LEFT JOIN region ON school.id_region=region.id_region
                ORDER BY $OrderBy $ShortBy 
                LIMIT $posisi, $batas
            ";
        }else{
            $sql = "
                SELECT school.*, region.province_code, region.province_name, region.district_code, region.district_name
                FROM school 
                LEFT JOIN region ON school.id_region=region.id_region
                WHERE npsn LIKE '%$keyword%' 
                   OR school_name LIKE '%$keyword%'
                   OR province_code LIKE '%$keyword%' 
                   OR province_name LIKE '%$keyword%'
                   OR district_code LIKE '%$keyword%' 
                   OR district_name LIKE '%$keyword%'
                ORDER BY $OrderBy $ShortBy 
                LIMIT $posisi, $batas
            ";
        }
    }else{
        if(empty($keyword)){
            $sql = "
                SELECT school.*, region.province_code, region.province_name, region.district_code, region.district_name
                FROM school 
                LEFT JOIN region ON school.id_region=region.id_region
                ORDER BY $OrderBy $ShortBy 
                LIMIT $posisi, $batas
            ";
        }else{
            $sql = "
                SELECT school.*, region.province_code, region.province_name, region.district_code, region.district_name
                FROM school 
                LEFT JOIN region ON school.id_region=region.id_region
                WHERE $keyword_by LIKE '%$keyword%'
                ORDER BY $OrderBy $ShortBy 
                LIMIT $posisi, $batas
            ";
        }
    }
    $query = mysqli_query($Conn, $sql);

    while ($data = mysqli_fetch_array($query)) {
        $id_school      = $data['id_school'];
        $id_region      = $data['id_region'];
        $npsn           = $data['npsn'];
        $school_name    = $data['school_name'];
        $province       = $data['province_name'];
        $district_name  = $data['district_name'];
        
        echo '
            <tr>
                <td><small>'.$no.'</small></td>
                <td><small>'.$province.'</small></td>
                <td><small>'.$district_name.'</small></td>
                <td><small>'.$npsn.'</small></td>
                <td><small>'.$school_name.'</small></td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline-dark btn-floating"  data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="">
                        <li class="dropdown-header text-start">
                            <h6>Option</h6>
                        </li>
                        <li>
                            <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ModalDetail" data-id="'.$id_school .'">
                                <i class="bi bi-info-circle"></i> Detail
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ModalEdit" data-id="'.$id_school .'">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ModalHapus" data-id="'.$id_school .'">
                                <i class="bi bi-x"></i> Hapus
                            </a>
                        </li>
                    </ul>
                </td>
            </tr>
        ';
        $no++;
    }
?>
<script>
    //Creat Javascript Variabel
    var data_count=<?php echo $jml_data; ?>;
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
