<?php
    //Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";

    //Inisiasi Variabel Agar Tidak Error
    $jml_data = 0;
    $JmlHalaman = 0;
    $page_count = 0;
    $curent_page = 0;
    $school_level = 0;
    
    
    //Validasi province_code
    if(empty($_POST['province_code'])){
        echo '
            <tr>
                <td colspan="6" class="text-center">
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
        $batas = $_POST['batas'];
    }else{
        $batas = "5";
    }

    //page
    if(!empty($_POST['page'])){
        $page = $_POST['page'];
        $posisi = ($page - 1) * $batas;
    }else{
        $page = "1";
        $posisi = 0;
    }

    //ShortBy
    if(!empty($_POST['ShortBy'])){
        $ShortBy = $_POST['ShortBy'];
    }else{
        $ShortBy = "DESC";
    }
    
    //OrderBy
    if(!empty($_POST['OrderBy'])){
        $OrderBy = $_POST['OrderBy'];
    }else{
        $OrderBy = "total_kurang_guru"; // Default urut berdasarkan KurangGuru
    }

    //school_level
    if(!empty($_POST['school_level'])){
        $school_level_filter = $_POST['school_level'];
    }else{
        $school_level_filter = "";
    }

    // Kolom yang diizinkan untuk pengurutan
    $allowedOrderColumns = ['position_name', 'total_abk', 'total_asn', 'total_pppk2024', 'total_kurang_guru'];

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
            p.id_position,
            p.position_name,
            COALESCE(SUM(ps.abk), 0) as total_abk,
            COALESCE(SUM(ps.asn), 0) as total_asn,
            COALESCE(SUM(ps.PPPK2024), 0) as total_pppk2024,
            COALESCE(SUM(ps.KurangGuru), 0) as total_kurang_guru
        FROM position p
        LEFT JOIN position_school ps ON ps.id_position = p.id_position
        LEFT JOIN school s ON s.id_school = ps.id_school
        LEFT JOIN region r ON r.id_region = s.id_region
        WHERE r.province_code = '$province_code' 
        AND r.category = 'District'
    ";

    // Filter school_level jika ada
    if(!empty($school_level_filter)){
        $baseQuery .= " AND s.school_level = '$school_level_filter'";
    }

    $baseQuery .= " GROUP BY p.id_position, p.position_name";

    // Hitung total data
    $countQuery = "SELECT COUNT(*) as jml FROM ($baseQuery) as counted";
    $resultCount = mysqli_query($Conn, $countQuery);
    
    if($resultCount){
        $rowCount = mysqli_fetch_assoc($resultCount);
        $jml_data = $rowCount['jml'];
    }

    // Mengatur Halaman
    $JmlHalaman = ceil($jml_data / $batas); 

    // Jika Data Tidak Ada
    if(empty($jml_data)){
        echo '
            <tr>
                <td colspan="6" class="text-center">
                    <small class="text-danger">Data Tidak Ditemukan</small>
                </td>
            </tr>
       ';
        exit;
    }

    // Query utama dengan pengurutan dan limit
    $query = "$baseQuery ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas";
    $result = mysqli_query($Conn, $query);

    // Looping data
    $no = 1 + $posisi;
    while ($data = mysqli_fetch_assoc($result)) {
        $id_position = $data['id_position'];
        $position_name = $data['position_name'];
        $total_abk = number_format($data['total_abk'], 0, ',', '.');
        $total_asn = number_format($data['total_asn'], 0, ',', '.');
        $total_pppk2024 = number_format($data['total_pppk2024'], 0, ',', '.');
        $total_kurang_guru = number_format($data['total_kurang_guru'], 0, ',', '.');

        // Tampilkan Data
        echo '
            <tr>
                <td><small>'.$no.'</small></td>
                <td>
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#ModalDetailKebutuhanGuruByJabatan" data-province_code="'.$province_code .'" data-id_position="'.$id_position .'" data-school_level="'.$school_level_filter .'">
                        <small>'.$position_name.'</small>
                    </a>
                </td>
                <td><small>'.$total_abk.'</small></td>
                <td><small>'.$total_asn.'</small></td>
                <td><small>'.$total_pppk2024.'</small></td>
                <td><small>'.$total_kurang_guru.'</small></td>
            </tr>
        ';
        $no++;
    }

?>
<script>
    //Creat Javascript Variabel
    var data_count = <?php echo $jml_data; ?>; //Jumlah Total Semua Data
    var page_count = <?php echo $JmlHalaman; ?>; //Jumlah Halaman
    var curent_page = <?php echo $page; ?>;   //Posisi Halaman Sekarang
    
    //Put Into Pagging Element
    $('#data_count_2').html('Data : ' + data_count + ' Record');
    $('#page_info_2').html('Page ' + curent_page + ' / ' + page_count + '');
    
    //Set Pagging Button
    if(curent_page == 1){
        $('#prev_button_2').prop('disabled', true);
    }else{
        $('#prev_button_2').prop('disabled', false);
    }
    if(page_count <= curent_page){
        $('#next_button_2').prop('disabled', true);
    }else{
        $('#next_button_2').prop('disabled', false);
    }
</script>