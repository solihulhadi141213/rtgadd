<?php
    // Koneksi & dependensi
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

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

    //Validasi district_code
    if(empty($_POST['district_code'])){
        echo '
            <tr>
                <td colspan="6" class="text-center">
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
        $batas=$_POST['batas'];
    }else{
        $batas="5";
    }

    //Atur page
    if(!empty($_POST['page'])){
        $page=$_POST['page'];
        $posisi = ( $page - 1 ) * $batas;
    }else{
        $page="1";
        $posisi = 0;
    }

    //school_level
    if(!empty($_POST['school_level'])){
        $school_level=$_POST['school_level'];
    }else{
        $school_level="";
    }

    //Looping Position
    $no=1;
    $jumlah_data=1;
    $query_position = mysqli_query($Conn, "SELECT DISTINCT position_code FROM position");
    while ($data_position = mysqli_fetch_array($query_position)) {
        $position_code  = $data_position['position_code'];
        
        //Buka position_name
        $position_name  = GetDetailData($Conn, 'position', 'position_code', $position_code, 'position_name');
        $id_position    = GetDetailData($Conn, 'position', 'position_code', $position_code, 'id_position');

        //Menghitung abkk asn dll
        $abk        = 0;
        $asn        = 0;
        $PPPK2024   = 0;
        $KurangGuru = 0;
       
        //Looping daftar sekolah berdasarkan id_region
        $query_school = mysqli_query($Conn, "SELECT id_school FROM school WHERE id_region='$id_region'");
        while ($data_school = mysqli_fetch_array($query_school)) {
            $id_school      = $data_school['id_school'];

            //Looping position_school berdasarkan id_school dan id_position
            $query_position_school  = mysqli_query($Conn, "SELECT * FROM position_school WHERE id_school='$id_school' AND id_position='$id_position'");
            while ($data_position_school = mysqli_fetch_array($query_position_school)) {
                $abk_list           = $data_position_school['abk'];
                $asn_list           = $data_position_school['asn'];
                $PPPK2024_list      = $data_position_school['PPPK2024'];
                $KurangGuru_list    = $data_position_school['KurangGuru'];

                //Hitung Akumulasi
                $abk        = $abk+$abk_list;
                $asn        = $asn+$asn_list;
                $PPPK2024   = $PPPK2024+$PPPK2024_list;
                $KurangGuru = $KurangGuru+$KurangGuru_list;
            }
        }

        //Menampilkan Data
        echo '
            <tr>
                <td><small>'.$no.'</small></td>
                <td><small>'.$position_name.'</small></td>
                <td><small>'.$abk.'</small></td>
                <td><small>'.$asn.'</small></td>
                <td><small>'.$PPPK2024.'</small></td>
                <td><small>'.$KurangGuru.'</small></td>
            </tr>
        ';
        $no++;
        $jumlah_data=$jumlah_data+1;
    }
?>

<script>
    //Creat Javascript Variabel
    var jumlah_data=<?php echo $jumlah_data; ?>;
    
    //Put Into data_count Element
    $('#data_count').html('Count : '+jumlah_data+' Record');
    
</script>