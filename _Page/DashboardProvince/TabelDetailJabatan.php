<?php
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";

    // Validasi
    if(empty($_POST['province_code']) || empty($_POST['id_position']) || empty($_POST['school_level'])){
        echo '
            <tr>
                <td colspan="12" class="text-center">
                    <small class="text-danger">Semua filter wajib diisi!</small>
                </td>
            </tr>
        ';
        exit;
    }

    // Variabel
    $province_code  = mysqli_real_escape_string($Conn, $_POST['province_code']);
    $id_position    = mysqli_real_escape_string($Conn, $_POST['id_position']);
    $school_level   = mysqli_real_escape_string($Conn, $_POST['school_level']);

    $province_name  = GetDetailData($Conn, 'region', 'province_code', $province_code, 'province_name');
    $position_name  = GetDetailData($Conn, 'position', 'id_position', $id_position, 'position_name');

    $batas = 10;
    $page  = !empty($_POST['page']) ? (int)$_POST['page'] : 1;
    $posisi = ($page - 1) * $batas;

    $ShortBy = !empty($_POST['ShortBy']) ? $_POST['ShortBy'] : "DESC";
    $OrderBy = !empty($_POST['OrderBy']) ? $_POST['OrderBy'] : "KurangGuru";

    // ================== HITUNG DATA ================== //
    $sql_count = "
        SELECT COUNT(*) AS jml
        FROM school s
        INNER JOIN position_school ps ON s.id_school = ps.id_school
        INNER JOIN region r ON s.id_region = r.id_region
        WHERE 
            r.province_code = '$province_code' 
            AND ps.id_position = '$id_position'
            AND s.school_level = '$school_level'
    ";
    $res_count = mysqli_query($Conn, $sql_count);
    $jml_data  = (int)mysqli_fetch_assoc($res_count)['jml'];

    if($jml_data == 0){
        echo '
            <tr>
                <td colspan="12" class="text-center">
                    <small class="text-danger">Tidak Ada Data Yang Ditampilkan!</small>
                </td>
            </tr>
        ';
        exit;
    }

    // ================== QUERY DATA ================== //
    $sql_data = "
        SELECT 
            s.school_name, 
            r.district_name,
            ps.abk,
            ps.asn,
            ps.PPPK2024,
            ps.NonASN_sblmOkt2022,
            ps.NonASN_stlhOkt2022,
            ps.JmlGuru,
            ps.KurangGuru,
            ps.JmlASN,
            ps.KrngASN
        FROM school s
        INNER JOIN position_school ps ON s.id_school = ps.id_school
        INNER JOIN region r ON s.id_region = r.id_region
        WHERE 
            r.province_code = '$province_code' 
            AND ps.id_position = '$id_position'
            AND s.school_level = '$school_level'
        ORDER BY ps.$OrderBy $ShortBy
        LIMIT $posisi, $batas
    ";

    $query_data = mysqli_query($Conn, $sql_data);

    $no = $posisi + 1;
    while($row = mysqli_fetch_assoc($query_data)){
        echo '
            <tr>
                <td><small>'.$no.'</small></td>
                <td><small>'.$row['school_name'].'</small></td>
                <td><small>'.$row['district_name'].'</small></td>
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

    $JmlHalaman = ceil($jml_data / $batas);
    $province_name = ucwords(strtolower($province_name));
?>

<script>
    var province_name = "<?php echo $province_name; ?>";
    var position_name = "<?php echo $position_name; ?>";
    var school_level  = "<?php echo $school_level; ?>";
    var data_count    = <?php echo $jml_data; ?>;
    var page_count    = <?php echo $JmlHalaman; ?>;
    var current_page  = <?php echo $page; ?>;

    $('#title_position_province_name').html(
        '<b>Untuk Jabatan : </b> ' + position_name +
        ' | Provinsi ' + province_name +
        '<br><b>Jenjang Pendidikan :</b> ' + school_level
    );

    $('#data_count_school').html('Data : ' + data_count + ' Record');
    $('#page_info_school').html('Page ' + current_page + ' / ' + page_count);

    $('#prev_button_school').prop('disabled', current_page == 1);
    $('#next_button_school').prop('disabled', current_page >= page_count);
</script>
