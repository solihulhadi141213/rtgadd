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
                <td colspan="13" class="text-center">
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
        $OrderBy="id_position_school";
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
    if(empty($keyword_by)){
        if(empty($keyword)){
            $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_position_school  FROM position_school "));
        }else{
            $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_position_school  FROM position_school WHERE id_school like '%$keyword%' OR id_position like '%$keyword%'"));
        }
    }else{
        if(empty($keyword)){
            $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_position_school  FROM position_school "));
        }else{
            $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_position_school  FROM position_school  WHERE $keyword_by like '%$keyword%'"));
        }
    }
        
    //Mengatur Halaman
    $JmlHalaman = ceil($jml_data/$batas); 
    if(empty($jml_data)){
        echo '
            <tr>
                <td colspan="13" class="text-center">
                    <small class="text-danger">Tidak Ada Data Yang Ditampilkan!</small>
                </td>
            </tr>
        ';
        exit;
    }
    $no = 1+$posisi;
    //KONDISI PENGATURAN MASING FILTER
    if(empty($keyword_by)){
        if(empty($keyword)){
            $query = mysqli_query($Conn, "SELECT*FROM position_school  ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
        }else{
            $query = mysqli_query($Conn, "SELECT*FROM position_school  WHERE id_school like '%$keyword%' OR id_position like '%$keyword%' ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
        }
    }else{
        if(empty($keyword)){
            $query = mysqli_query($Conn, "SELECT*FROM position_school  ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
        }else{
            $query = mysqli_query($Conn, "SELECT*FROM position_school  WHERE $keyword_by like '%$keyword%' ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
        }
    }
    while ($data = mysqli_fetch_array($query)) {
        $id_position_school     = $data['id_position_school'];
        $id_school              = $data['id_school'];
        $id_position            = $data['id_position'];
        $abk                    = $data['abk'];
        $asn                    = $data['asn'];
        $PPPK2024               = $data['PPPK2024'];
        $NonASN_sblmOkt2022     = $data['NonASN_sblmOkt2022'];
        $NonASN_stlhOkt2022     = $data['NonASN_stlhOkt2022'];
        $JmlGuru                = $data['JmlGuru'];
        $KurangGuru             = $data['KurangGuru'];
        $JmlASN                 = $data['JmlASN'];
        $KrngASN                = $data['KrngASN'];

        //Buka id_region
        $id_region          = GetDetailData($Conn, 'school', 'id_school', $id_school, 'id_region');

        //Nama Sekolah
        $school_name        = GetDetailData($Conn, 'school', 'id_school', $id_school, 'school_name');

        //Buka Nama Provinsi Dan Kabupaten
        $province_name      = GetDetailData($Conn, 'region', 'id_region', $id_region, 'province_name');
        $district_name      = GetDetailData($Conn, 'region', 'id_region', $id_region, 'district_name');

        //Nama Jabatan
        $position_name      = GetDetailData($Conn, 'position', 'id_position', $id_position, 'position_name');

        echo '
            <tr>
                <td><small>'.$no.'</small></td>
                <td><small>'.$province_name.'</small></td>
                <td><small>'.$district_name.'</small></td>
                <td><small>'.$school_name.'</small></td>
                <td><small>'.$position_name.'</small></td>
                <td><small>'.$abk.'</small></td>
                <td><small>'.$asn.'</small></td>
                <td><small>'.$PPPK2024.'</small></td>
                <td><small>'.$NonASN_sblmOkt2022.'</small></td>
                <td><small>'.$NonASN_stlhOkt2022.'</small></td>
                <td><small>'.$JmlGuru.'</small></td>
                <td><small>'.$KurangGuru.'</small></td>
                <td><small>'.$JmlASN.'</small></td>
                <td><small>'.$KrngASN.'</small></td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-dark btn-floating"  data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="">
                        <li class="dropdown-header text-start">
                            <h6>Option</h6>
                        </li>
                        <li>
                            <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ModalDetail" data-id="'.$id_position_school .'">
                                <i class="bi bi-info-circle"></i> Detail
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ModalEdit" data-id="'.$id_position_school .'">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ModalHapus" data-id="'.$id_position_school .'">
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