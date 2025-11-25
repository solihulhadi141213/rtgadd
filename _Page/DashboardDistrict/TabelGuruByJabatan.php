<?php
    // Koneksi & dependensi
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    //Validasi Akses
    if(empty($SessionIdAccess)){
        echo '
            <tr>
                <td colspan="7" class="text-center">
                    <small class="text-danger">Sesi Akses Sudah Berakhir! Silahkan Login Ulang!</small>
                </td>
            </tr>
        ';
        exit;
    }

    //Validasi district_code
    if(empty($_POST['district_code'])){
        echo '
            <tr>
                <td colspan="7" class="text-center">
                    <small class="text-danger">Kode Kabupaten/Kota Tidak Boleh Kosong!</small>
                </td>
            </tr>
        ';
        exit;
    }

    //Buat Variabel
    $district_code  = $_POST['district_code'];
    $id_region      = GetDetailData($Conn, 'region', 'district_code', $district_code, 'id_region');

    //batas
    if(!empty($_POST['batas'])){
        $batas = $_POST['batas'];
    }else{
        $batas = "5";
    }

    //Atur page
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
        $OrderBy = "total_kurang_guru";
    }

    //school_level
    if(!empty($_POST['school_level'])){
        $school_level = $_POST['school_level'];
    }else{
        $school_level = "";
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
            p.position_code,
            p.position_name,
            COALESCE(SUM(ps.abk), 0) as total_abk,
            COALESCE(SUM(ps.asn), 0) as total_asn,
            COALESCE(SUM(ps.PPPK2024), 0) as total_pppk2024,
            COALESCE(SUM(ps.KurangGuru), 0) as total_kurang_guru
        FROM position p
        LEFT JOIN position_school ps ON ps.id_position = p.id_position
        LEFT JOIN school s ON s.id_school = ps.id_school
        WHERE s.id_region = '$id_region'
    ";

    // Filter school_level jika ada
    if(!empty($school_level)){
        $baseQuery .= " AND s.school_level = '$school_level'";
    }

    $baseQuery .= " GROUP BY p.id_position, p.position_code, p.position_name";

    // Hitung total data
    $countQuery = "SELECT COUNT(*) as jml FROM ($baseQuery) as counted";
    $resultCount = mysqli_query($Conn, $countQuery);
    
    if($resultCount){
        $rowCount = mysqli_fetch_assoc($resultCount);
        $jml_data = $rowCount['jml'];
    } else {
        $jml_data = 0;
    }

    // Mengatur Halaman
    $JmlHalaman = ceil($jml_data / $batas); 

    // Jika Data Tidak Ada
    if(empty($jml_data)){
        echo '
            <tr>
                <td colspan="7" class="text-center">
                    <small class="text-danger">Tidak Ada Data Posisi/Jabatan Untuk Kabupaten/Kota Ini</small>
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
        $id_position        = $data['id_position'];
        $position_name      = $data['position_name'];
        $total_abk          = number_format($data['total_abk'], 0, ',', '.');
        $total_asn          = number_format($data['total_asn'], 0, ',', '.');
        $total_pppk2024     = number_format($data['total_pppk2024'], 0, ',', '.');
        $total_kurang_guru  = number_format($data['total_kurang_guru'], 0, ',', '.');

        // Tampilkan Data
        echo '
            <tr>
                <td><small>'.$no.'</small></td>
                <td><small>'.$position_name.'</small></td>
                <td><small>'.$total_abk.'</small></td>
                <td><small>'.$total_asn.'</small></td>
                <td><small>'.$total_pppk2024.'</small></td>
                <td><small>'.$total_kurang_guru.'</small></td>
                <td>
                    <button type="button" class="btn btn-sm btn-primary btn-floating" data-bs-toggle="modal" data-bs-target="#ModalDetailJabatan" data-id_region="'.$id_region.'" data-id_position="'.$id_position.'" data-school_level="'.$school_level.'">
                        <i class="bi bi-arrow-up-right"></i>
                    </button>
                </td>
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
    $('#data_count').html('Data : ' + data_count + ' Record');
    $('#page_info').html('Page ' + curent_page + ' / ' + page_count + '');
    
    //Set Pagging Button
    if(curent_page == 1){
        $('#prev_button').prop('disabled', true);
    }else{
        $('#prev_button').prop('disabled', false);
    }
    if(page_count <= curent_page){
        $('#next_button').prop('disabled', true);
    }else{
        $('#next_button').prop('disabled', false);
    }
</script>