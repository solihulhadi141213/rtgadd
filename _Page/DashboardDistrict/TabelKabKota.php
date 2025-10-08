<?php
    // Koneksi & dependensi
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    //Validasi Akses
    if(empty($SessionIdAccess)){
        echo '
            <tr>
                <td colspan="4" class="text-center">
                    <small class="text-danger">Sesi Akses Sudah Berakhir! Silahkan Login Ulang!</small>
                </td>
            </tr>
        ';
        exit;
    }

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
        $ShortBy = "ASC";
    }
    
    //OrderBy
    if(!empty($_POST['OrderBy'])){
        $OrderBy = $_POST['OrderBy'];
    }else{
        $OrderBy = "province_code";
    }

    //keyword
    if(!empty($_POST['keyword'])){
        $keyword = $_POST['keyword'];
    }else{
        $keyword = "";
    }

    //Hitung Jumlah Data
    if(empty($keyword)){
        $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_geo_region FROM geo_region "));
    }else{
        $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT id_geo_region FROM geo_region  WHERE province_name like '%$keyword%' OR district_name like '%$keyword%'"));
    }

    //Hitung Jumlah Halaman
    $JmlHalaman = ceil($jml_data/$batas); 

    //Jika Data Tidak Ada
    if(empty($jml_data)){
        echo '
            <tr>
                <td colspan="4" class="text-center">
                    <small class="text-danger">Tidak Ada Data Yang Ditampilkan!</small>
                </td>
            </tr>
        ';
        exit;
    }

    //Looping Data
    $no=1+$posisi;
    if(empty($keyword)){
        $query = mysqli_query($Conn, "SELECT*FROM geo_region ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
    }else{
        $query = mysqli_query($Conn, "SELECT*FROM geo_region WHERE province_name like '%$keyword%' OR district_name like '%$keyword%' ORDER BY $OrderBy $ShortBy LIMIT $posisi, $batas");
    }
    while ($data = mysqli_fetch_array($query)) {
        $id_geo_region = $data['id_geo_region'];
        $province_name = $data['province_name'];
        $district_name = $data['district_name'];
        $district_code = $data['district_code'];

        //Tampilkan Data
        echo '
            <tr>
                <td><small>'.$no.'</small></td>
                <td><small>'.$province_name.'</small></td>
                <td><small>'.$district_name.'</small></td>
                <td>
                    <button type="button" class="btn btn-sm btn-primary btn-floating" data-bs-toggle="modal" data-bs-target="#ModalDetailKabKot" data-id="'.$district_code.'" title="Lihat Detail Kab/Kota">
                        <i class="bi bi-chevron-right"></i>
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
    $('#data_count_kabkot').html('Data : ' + data_count + ' Record');
    $('#page_info_kabkot').html('Page ' + curent_page + ' / ' + page_count + '');
    
    //Set Pagging Button
    if(curent_page == 1){
        $('#prev_button_kabkot').prop('disabled', true);
    }else{
        $('#prev_button_kabkot').prop('disabled', false);
    }
    if(page_count <= curent_page){
        $('#next_button_kabkot').prop('disabled', true);
    }else{
        $('#next_button_kabkot').prop('disabled', false);
    }
</script>