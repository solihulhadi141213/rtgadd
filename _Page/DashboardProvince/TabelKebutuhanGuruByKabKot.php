<?php
    //Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    //Validasi Sesi akses
    if (empty($SessionIdAccess)) {
       echo '
            <tr>
                <td colspan="5" class="text-center">
                    <small class="text-danger">Sesi Akses Sudah Berakhir! Silahkan Login Ulang!</small>
                </td>
            </tr>
       ';
        exit;
    }

    //Validasi province_code
    if(empty($_POST['province_code'])){
        echo '
            <tr>
                <td colspan="5" class="text-center">
                    <small class="text-danger">Kode Provinsi Tidak Boleh Kosong</small>
                </td>
            </tr>
       ';
        exit;
    }
    //Buat Variabel
    $province_code = $_POST['province_code'];

    //batas
    if(!empty($_POST['batas'])){
        $batas=$_POST['batas'];
    }else{
        $batas="5";
    }

    //page
    if(!empty($_POST['page'])){
        $page=$_POST['page'];
        $posisi = ( $page - 1 ) * $batas;
    }else{
        $page="1";
        $posisi = 0;
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
        $OrderBy="total_kurang_guru"; // Default urut berdasarkan kurang_guru
    }

    //school_level
    if(!empty($_POST['school_level'])){
        $school_level_filter=$_POST['school_level'];
    }else{
        $school_level_filter="";
    }

    // Kolom yang diizinkan untuk pengurutan
    $allowedOrderColumns = [
        'id_geo_region', 'district_code', 'district_name', 
        'jumlah_sekolah', 'total_kurang_guru'
    ];

    // Validasi OrderBy
    if(!in_array($OrderBy, $allowedOrderColumns)){
        $OrderBy = "total_kurang_guru";
    }

    /**
     * ===========================================
     * QUERY UTAMA DENGAN JOIN DAN AGREGASI
     * ===========================================
     */
    $baseQuery = "
        SELECT 
            gr.id_geo_region,
            gr.district_code,
            gr.district_name,
            r.id_region,
            COUNT(DISTINCT s.id_school) as jumlah_sekolah,
            COALESCE(SUM(ps.KurangGuru), 0) as total_kurang_guru
        FROM geo_region gr
        LEFT JOIN region r ON r.district_code = gr.district_code AND r.category='District'
        LEFT JOIN school s ON s.id_region = r.id_region
        LEFT JOIN position_school ps ON ps.id_school = s.id_school
        WHERE gr.level_region='District' 
        AND gr.province_code='$province_code'
    ";

    // Filter school_level jika ada
    if(!empty($school_level_filter)){
        $baseQuery .= " AND s.school_level='$school_level_filter'";
    }

    $baseQuery .= " GROUP BY gr.id_geo_region, gr.district_code, gr.district_name, r.id_region";

    // Hitung total data
    $countQuery = "SELECT COUNT(*) as jml FROM ($baseQuery) as counted";
    $resultCount = mysqli_query($Conn, $countQuery);
    $rowCount = mysqli_fetch_assoc($resultCount);
    $jml_data = $rowCount['jml'];

    // Mengatur Halaman
    $JmlHalaman = ceil($jml_data/$batas); 

    // Jika Data Tidak Ada
    if(empty($jml_data)){
        echo '
            <tr>
                <td colspan="5" class="text-center">
                    <small class="text-danger">Tidak Ada Kab/Kota Yang Terdaftar Untuk Provinsi Ini</small>
                </td>
            </tr>
       ';
        exit;
    }

    // Menghitung Kebutuhan Guru Total (Provinsi) - Cara yang lebih efisien
    $queryTotalKurangGuru = "
        SELECT COALESCE(SUM(ps.KurangGuru), 0) as total_kurang_guru_provinsi
        FROM geo_region gr
        LEFT JOIN region r ON r.district_code = gr.district_code AND r.category='District'
        LEFT JOIN school s ON s.id_region = r.id_region
        LEFT JOIN position_school ps ON ps.id_school = s.id_school
        WHERE gr.level_region='District' 
        AND gr.province_code='$province_code'
    ";
    
    if(!empty($school_level_filter)){
        $queryTotalKurangGuru .= " AND s.school_level='$school_level_filter'";
    }
    
    $resultTotal = mysqli_query($Conn, $queryTotalKurangGuru);
    $dataTotal = mysqli_fetch_assoc($resultTotal);
    $kurang_guru_total = $dataTotal['total_kurang_guru_provinsi'];

    // Query utama dengan pengurutan
    $query = "$baseQuery ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas";
    $result = mysqli_query($Conn, $query);

    // Looping data
    $no = 1 + $posisi;
    while ($data = mysqli_fetch_assoc($result)) {
        $id_region  = $data['id_region'];
        $id_geo_region  = $data['id_geo_region'];
        $district_code  = $data['district_code'];
        $district_name  = $data['district_name'];
        $jumlah_sekolah = $data['jumlah_sekolah'];
        $kurang_guru    = $data['total_kurang_guru'];

        
        // Hitung Persentase Kurang Guru
        $persen_kurang_guru = 0;
        if($kurang_guru_total > 0){
            $persen_kurang_guru = round(($kurang_guru / $kurang_guru_total) * 100);
        }

        // Batasi persentase maksimal 100%
        if($persen_kurang_guru > 100){
            $persen_kurang_guru = 100;
        }

        echo '
            <tr>
                <td><small>'.$no.'</small></td>
                <td>
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#ModalDetailKabKot" data-id="'.$id_region.'">
                        <small class="text text-decoration-underline">'.$district_name.'</small>
                    </a>
                </td>
                <td><small>'.$jumlah_sekolah.'</small></td>
                <td><small>'.$kurang_guru.'</small></td>
                <td>
                    <div style="background:#e9ecef; border-radius:5px; width:100%; height:18px; position:relative;">
                        <div style="background:#007bff; height:100%; width:'.$persen_kurang_guru.'%; border-radius:5px;"></div>
                        <small style="position:absolute; left:50%; top:50%; transform:translate(-50%,-50%); color:dark;">
                            '.$persen_kurang_guru.'%
                        </small>
                    </div>
                </td>
            </tr>
        ';
        $no++;
    }

?>
<script>
    //Creat Javascript Variabel
    var data_count=<?php echo "$jml_data"; ?>;
    var page_count=<?php echo $JmlHalaman; ?>;
    var curent_page=<?php echo $page; ?>;
    var school_level ="<?php echo $school_level_filter; ?>";

    $("#TitleKebutuhanGuruByKabKot").html('Loading...');
    if(school_level==""){
        $("#TitleKebutuhanGuruByKabKot").html('(Semua Jenjang)');
    }else{
        $("#TitleKebutuhanGuruByKabKot").html('(Untuk Jenjang '+school_level+')');
    }
    
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