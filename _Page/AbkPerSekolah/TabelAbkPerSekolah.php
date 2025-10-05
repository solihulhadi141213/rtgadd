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

    // Ambil parameter
    $keyword_by = !empty($_POST['keyword_by']) ? $_POST['keyword_by'] : "";
    $keyword    = !empty($_POST['keyword']) ? $_POST['keyword'] : "";
    $batas      = !empty($_POST['batas']) ? $_POST['batas'] : 10;
    $ShortBy    = !empty($_POST['ShortBy']) ? $_POST['ShortBy'] : "DESC";
    $OrderBy    = !empty($_POST['OrderBy']) ? $_POST['OrderBy'] : "id_position_school";
    $page       = !empty($_POST['page']) ? $_POST['page'] : 1;
    $posisi     = ($page-1) * $batas;

    // Mapping kolom yang boleh dipakai
    $allowed_columns = [
        "id_position_school" => "ps.id_position_school",
        "province_code"      => "r.province_code",
        "province_name"      => "r.province_name",
        "district_code"      => "r.district_code",
        "district_name"      => "r.district_name",
        "npsn"               => "s.npsn",
        "school_name"        => "s.school_name",
        "position_code"      => "p.position_code",
        "position_name"      => "p.position_name",
        "abk"                => "ps.abk",
        "asn"                => "ps.asn",
        "PPPK2024"           => "ps.PPPK2024",
        "NonASN_sblmOkt2022" => "ps.NonASN_sblmOkt2022",
        "NonASN_stlhOkt2022" => "ps.NonASN_stlhOkt2022",
        "JmlGuru"            => "ps.JmlGuru",
        "KurangGuru"         => "ps.KurangGuru",
        "JmlASN"             => "ps.JmlASN",
        "KrngASN"            => "ps.KrngASN"
    ];

    // Validasi agar OrderBy dan keyword_by aman
    $OrderBy = isset($allowed_columns[$OrderBy]) ? $allowed_columns[$OrderBy] : "ps.id_position_school";
    $keyword_by_sql = isset($allowed_columns[$keyword_by]) ? $allowed_columns[$keyword_by] : "";

    // Query dasar dengan JOIN
    $sql_base = "
        FROM position_school ps
        LEFT JOIN school s ON ps.id_school=s.id_school
        LEFT JOIN region r ON s.id_region=r.id_region
        LEFT JOIN position p ON ps.id_position=p.id_position
    ";

    // Hitung jumlah data
    if(!empty($keyword)){
        if(!empty($keyword_by_sql)){
            $where = "WHERE $keyword_by_sql LIKE '%$keyword%'";
        } else {
            // pencarian ke banyak kolom
            $where = "WHERE 
                r.province_code LIKE '%$keyword%' OR
                r.province_name LIKE '%$keyword%' OR
                r.district_code LIKE '%$keyword%' OR
                r.district_name LIKE '%$keyword%' OR
                s.npsn LIKE '%$keyword%' OR
                s.school_name LIKE '%$keyword%' OR
                p.position_code LIKE '%$keyword%' OR
                p.position_name LIKE '%$keyword%'";
        }
    } else {
        $where = "";
    }

    $q_count = mysqli_query($Conn, "SELECT COUNT(ps.id_position_school) as jml $sql_base $where");
    $d_count = mysqli_fetch_assoc($q_count);
    $jml_data = $d_count['jml'];

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

    // Query data utama
    $sql = "
        SELECT 
            ps.*, 
            s.school_name, s.npsn, 
            r.province_name, r.district_name,
            p.position_name
        $sql_base 
        $where 
        ORDER BY $OrderBy $ShortBy 
        LIMIT $posisi,$batas
    ";
    $query = mysqli_query($Conn, $sql);
    $no = 1+$posisi;

    while ($data = mysqli_fetch_array($query)) {
        echo '
            <tr>
                <td><small>'.$no.'</small></td>
                <td><small>'.$data['province_name'].'</small></td>
                <td><small>'.$data['district_name'].'</small></td>
                <td><small>'.$data['school_name'].'</small></td>
                <td><small>'.$data['position_name'].'</small></td>
                <td><small>'.$data['abk'].'</small></td>
                <td><small>'.$data['asn'].'</small></td>
                <td><small>'.$data['PPPK2024'].'</small></td>
                <td><small>'.$data['NonASN_sblmOkt2022'].'</small></td>
                <td><small>'.$data['NonASN_stlhOkt2022'].'</small></td>
                <td><small>'.$data['JmlGuru'].'</small></td>
                <td><small>'.$data['KurangGuru'].'</small></td>
                <td><small>'.$data['JmlASN'].'</small></td>
                <td><small>'.$data['KrngASN'].'</small></td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-dark btn-floating"  data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                        <li class="dropdown-header text-start">
                            <h6>Option</h6>
                        </li>
                        <li>
                            <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ModalDetail" data-id="'.$data['id_position_school'].'">
                                <i class="bi bi-info-circle"></i> Detail
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ModalEdit" data-id="'.$data['id_position_school'].'">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ModalHapus" data-id="'.$data['id_position_school'].'">
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
    var data_count=<?php echo $jml_data; ?>;
    var page_count=<?php echo $JmlHalaman; ?>;
    var curent_page=<?php echo $page; ?>;
    
    $('#data_count').html('Data : '+data_count+' Record');
    $('#page_info').html('Page '+curent_page+' / '+page_count+'');
    
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
