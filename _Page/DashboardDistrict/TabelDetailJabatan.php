<?php
    // Koneksi & dependensi
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    //Validasi Akses
    if(empty($SessionIdAccess)){
        echo '
            <tr>
                <td colspan="11" class="text-center">
                    <small class="text-danger">Sesi Akses Sudah Berakhir! Silahkan Login Ulang!</small>
                </td>
            </tr>
        ';
        exit;
    }

    //Validasi id_region
    if(empty($_POST['id_region'])){
        echo '
            <tr>
                <td colspan="11" class="text-center">
                    <small class="text-danger">Kode Wilayah Tidak Boleh Kosong!</small>
                </td>
            </tr>
        ';
        exit;
    }

    //Validasi id_position
    if(empty($_POST['id_position'])){
        echo '
            <tr>
                <td colspan="11" class="text-center">
                    <small class="text-danger">Kode Jabatan Tidak Boleh Kosong!</small>
                </td>
            </tr>
        ';
        exit;
    }

    //Buat Variabel
    $id_region   = mysqli_real_escape_string($Conn, $_POST['id_region']);
    $id_position = mysqli_real_escape_string($Conn, $_POST['id_position']);

    //Buka nama Provinsi dan Kab/Kota
    $province_name = GetDetailData($Conn, 'region', 'id_region', $id_region, 'province_name');
    $district_name = GetDetailData($Conn, 'region', 'id_region', $id_region, 'district_name');

    //Buka Nama Jabatan
    $position_name = GetDetailData($Conn, 'position', 'id_position', $id_position, 'position_name');

    //batas
    $batas = 10;

    //Atur page
    if(!empty($_POST['page'])){
        $page   = (int) $_POST['page'];
        $posisi = ($page - 1) * $batas;
    }else{
        $page   = 1;
        $posisi = 0;
    }

    //ShortBy
    $ShortBy = !empty($_POST['ShortBy']) ? $_POST['ShortBy'] : "ASC";

    //OrderBy
    $OrderBy = !empty($_POST['OrderBy']) ? $_POST['OrderBy'] : "KurangGuru";

    //================== HITUNG TOTAL DATA ==================//
    $sql_count = "
        SELECT COUNT(*) AS jml
        FROM school s
        INNER JOIN position_school ps ON s.id_school = ps.id_school
        WHERE s.id_region='$id_region' AND ps.id_position='$id_position'
    ";
    $res_count = mysqli_query($Conn, $sql_count);
    $row_count = mysqli_fetch_assoc($res_count);
    $jml_data  = (int)$row_count['jml'];

    //Jika Data Tidak Ada
    if($jml_data == 0){
        echo '
            <tr>
                <td colspan="11" class="text-center">
                    <small class="text-danger">Tidak Ada Data Yang Ditampilkan!</small>
                </td>
            </tr>
        ';
        exit;
    }

    //================== QUERY DATA PAGING ==================//
    $sql_data = "
        SELECT s.school_name, ps.*
        FROM school s
        INNER JOIN position_school ps ON s.id_school = ps.id_school
        WHERE s.id_region='$id_region' AND ps.id_position='$id_position'
        ORDER BY ps.$OrderBy $ShortBy
        LIMIT $posisi, $batas
    ";
    $query_data = mysqli_query($Conn, $sql_data);

    //Tampilkan Data
    $no = $posisi+1;
    while($row = mysqli_fetch_assoc($query_data)){
        echo '
            <tr>
                <td><small>'.$no.'</small></td>
                <td><small>'.$row['school_name'].'</small></td>
                <td><small>'.$row['abk'].'</small></td>
                <td><small>'.$row['asn'].'</small></td>
                <td><small>'.$row['PPPK2024'].'</small></td>
                <td><small>'.$row['NonASN_sblmOkt2022'].'</small></td>
                <td><small>'.$row['NonASN_stlhOkt2022'].'</small></td>
                <td><small>'.$row['JmlGuru'].'</small></td>
                <td><small>'.$row['KurangGuru'].'</small></td>
                <td><small>'.$row['JmlASN'].'</small></td>
                <td><small>'.$row['KrngASN'].'</small></td>
            </tr>
        ';
        $no++;
    }

    //Hitung Jumlah Halaman
    $JmlHalaman = ceil($jml_data/$batas);

    //Ubah nama jabatan, district dan province
    $province_name = ucfirst(strtolower($province_name));
    $district_name = ucfirst(strtolower($district_name));
    $position_name = ucfirst(strtolower($position_name));
?>
<script>
    var province_name = "<?php echo $province_name; ?>";
    var district_name = "<?php echo $district_name; ?>";
    var position_name = "<?php echo $position_name; ?>";
    var data_count    = <?php echo $jml_data; ?>; 
    var page_count    = <?php echo $JmlHalaman; ?>;
    var curent_page   = <?php echo $page; ?>;

    $('#title_position_province_name').html('<b>Untuk Jabatan : </b> '+position_name+' | '+district_name+' - '+province_name+'');
    
    $('#data_count_school').html('Data : ' + data_count + ' Record');
    $('#page_info_school').html('Page ' + curent_page + ' / ' + page_count);

    if(curent_page == 1){
        $('#prev_button_school').prop('disabled', true);
    }else{
        $('#prev_button_school').prop('disabled', false);
    }
    if(page_count <= curent_page){
        $('#next_button_school').prop('disabled', true);
    }else{
        $('#next_button_school').prop('disabled', false);
    }
</script>
