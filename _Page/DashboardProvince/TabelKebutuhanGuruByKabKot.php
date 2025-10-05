<?php
    //Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";
    include "../../_Config/Session.php";

    //Validasi Sesi akses
    if (empty($SessionIdAccess)) {
       echo '
            <tr>
                <td colspan="4" class="text-center">
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
                <td colspan="4" class="text-center">
                    <small class="text-danger">Kode Provinsi Tidak Boleh Kosong</small>
                </td>
            </tr>
       ';
        exit;
    }

    //batas
    if(!empty($_POST['batas'])){
        $batas=$_POST['batas'];
    }else{
        $batas="10";
    }

    //school_level
    if(!empty($_POST['school_level'])){
        $school_level=$_POST['school_level'];
    }else{
        $school_level="";
    }

    //Buat Variabel
    $province_code = $_POST['province_code'];

    //Looping region level District
    $no=1;
    $query = mysqli_query($Conn, "SELECT * FROM region WHERE category='District' AND province_code='$province_code'");
    while ($data = mysqli_fetch_array($query)) {
        $id_region      = $data['id_region'];
        $district_code  = $data['district_code'];
        $district_name  = $data['district_name'];

        //Looping Semua Sekolah dengan DISTINCT jenjang
        $query_school_level = mysqli_query($Conn, "SELECT DISTINCT school_level FROM school WHERE id_region='$id_region'");
        while ($data_school_level = mysqli_fetch_array($query_school_level)) {
            $school_level = $data_school_level['school_level'];
            
            //Hitung Kekurangan Guru
            $kurang_guru=0;

            //List Sekolah di id_region level school_level
            $query_school_list = mysqli_query($Conn, "SELECT id_school FROM school WHERE id_region='$id_region' AND school_level='$school_level'");
            while ($data_school_list = mysqli_fetch_array($query_school_list)) {
                $id_school = $data_school_list['id_school'];
                
                //List position_school
                $query_position_school = mysqli_query($Conn, "SELECT KurangGuru FROM position_school WHERE id_school='$id_school'");
                while ($data_position_school = mysqli_fetch_array($query_position_school)) {
                    $KurangGuru = $data_position_school['KurangGuru'];
                    
                    //Hitung Kurang Guru
                    $kurang_guru=$kurang_guru+$KurangGuru;
                }
            }

            echo '
                <tr>
                    <td><small>'.$no.'</small></td>
                    <td><small>'.$district_name.'</small></td>
                    <td><small>'.$school_level.'</small></td>
                    <td><small>'.$kurang_guru.'</small></td>
                    <td>
                    <div style="background:#e9ecef; border-radius:5px; width:100%; height:18px; position:relative;">
                        <div style="background:#007bff; height:100%; width:'.($kurang_guru).'px; border-radius:5px;"></div>
                            <small style="position:absolute; left:50%; top:50%; transform:translate(-50%,-50%); color:white;">
                                '.$kurang_guru.'
                            </small>
                        </div>
                    </td>
                </tr>
            ';
            $no++;
        }

    }

?>