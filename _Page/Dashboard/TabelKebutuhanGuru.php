<?php
    //koneksi dan session
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    //Zona Waktu
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

    //Default variabel filter
    $keyword_by = !empty($_POST['keyword_by']) ? $_POST['keyword_by'] : "";
    $keyword    = !empty($_POST['keyword']) ? $_POST['keyword'] : "";
    $batas      = !empty($_POST['batas']) ? $_POST['batas'] : 10;
    $ShortBy    = !empty($_POST['ShortBy']) ? $_POST['ShortBy'] : "DESC";
    $OrderBy    = !empty($_POST['OrderBy']) ? $_POST['OrderBy'] : "gr.id_geo_region";

    //Atur Page
    if(!empty($_POST['page'])){
        $page = $_POST['page'];
        $posisi = ($page - 1) * $batas;
    }else{
        $page = 1;
        $posisi = 0;
    }

    /**
     * ======================
     * QUERY UTAMA DENGAN JOIN
     * ======================
     */
    $baseQuery = "
        FROM geo_region gr
        LEFT JOIN region r ON r.province_code = gr.province_code AND r.category='District'
        LEFT JOIN school s ON s.id_region = r.id_region
        LEFT JOIN position_school ps ON ps.id_school = s.id_school
        WHERE gr.level_region='Province'
    ";

    //Kolom yang boleh di-search/diurutkan
    $allowedSearch = ['province_code','province_name','total_abk','total_asn','total_kurang_guru'];
    $allowedOrder = ['province_code','province_name','total_abk','total_asn','total_kurang_guru','id_geo_region'];

    //Subquery agregasi
    $selectQuery = "
        SELECT 
            gr.id_geo_region,
            gr.province_code,
            gr.province_name,
            COALESCE(SUM(ps.abk),0) AS total_abk,
            COALESCE(SUM(ps.asn),0) AS total_asn,
            COALESCE(SUM(ps.KurangGuru),0) AS total_kurang_guru
        $baseQuery
        GROUP BY gr.id_geo_region, gr.province_code, gr.province_name
    ";

    //Filter pencarian
    $whereFilter = "";
    if(!empty($keyword)){
        if(empty($keyword_by) || !in_array($keyword_by, $allowedSearch)){
            //Default cari di provinsi
            $whereFilter = " HAVING province_code LIKE '%$keyword%' OR province_name LIKE '%$keyword%' ";
        } else {
            //Validasi kolom pencarian
            if(in_array($keyword_by, ['province_code','province_name'])){
                $whereFilter = " HAVING $keyword_by LIKE '%$keyword%' ";
            } else {
                //Untuk kolom agregat
                $whereFilter = " HAVING $keyword_by = '$keyword' ";
            }
        }
    }

    //Hitung total data
    $countQuery = "SELECT COUNT(*) as jml FROM ($selectQuery $whereFilter) as counted_table";
    $resCount = mysqli_query($Conn, $countQuery);
    
    if(!$resCount){
        echo "
            <tr>
                <td colspan='6' class='text-center'>
                    <small class='text-danger'>Error Count Query: " . mysqli_error($Conn) . "</small>
                </td>
            </tr>
        ";
        exit;
    }
    
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

    //Validasi OrderBy
    if(!in_array($OrderBy, $allowedOrder)){
        $OrderBy = "gr.id_geo_region";
    }

    //Query final dengan limit
    $query = "
        $selectQuery
        $whereFilter
        ORDER BY $OrderBy $ShortBy
        LIMIT $posisi,$batas
    ";
    
    $result = mysqli_query($Conn, $query);
    
    if(!$result){
        echo "
            <tr>
                <td colspan='6' class='text-center'>
                    <small class='text-danger'>Error Main Query: " . mysqli_error($Conn) . "</small>
                </td>
            </tr>
        ";
        exit;
    }

    $no = 1 + $posisi;
    while($row = mysqli_fetch_assoc($result)){
        $province_code   = $row['province_code'];
        $province_name   = $row['province_name'];
        $total_abk       = number_format($row['total_abk'],0,',','.');
        $total_asn       = number_format($row['total_asn'],0,',','.');
        $total_kurang    = number_format($row['total_kurang_guru'],0,',','.');

        echo "
            <tr>
                <td><small>$no</small></td>
                <td><small>$province_name</small></td>
                <td><small>$total_abk</small></td>
                <td><small>$total_asn</small></td>
                <td><small>$total_kurang</small></td>
                <td>
                    <button type='button' class='btn btn-sm btn-primary btn-floating' 
                        data-bs-toggle='modal' data-bs-target='#ModalDetailMap' 
                        data-id='$province_code'>
                        <i class='bi bi-chevron-right'></i>
                    </button>
                </td>
            </tr>
        ";
        $no++;
    }
?>
<script>
    var data_count=<?php echo $jml_data; ?>;
    var page_count=<?php echo $JmlHalaman; ?>;
    var curent_page=<?php echo $page; ?>;
    
    $('#data_count').html('Data Count : '+data_count+' Record');
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
