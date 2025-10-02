<?php
    //Koneksi
    include "../../_Config/Connection.php";
    include "../../_Config/GlobalFunction.php";

    //Jika Kode Kab/Kota Ada
    if(!empty($_POST['district_code'])){
        $district_code = $_POST['district_code'];
        
        //Buka id_region
        $id_region = GetDetailData($Conn, 'region', 'district_code', $district_code, 'id_region');
        if(empty($id_region)){
            echo '<option value="">No School Data For '.$district_code.'</option>';
        }else{
            //Tampilkan Data Kbupaten
            echo '<option value="">Pilih</option>';
            $query = mysqli_query($Conn, "SELECT npsn, school_name FROM school WHERE id_region='$id_region' ORDER BY school_name");
            while ($data = mysqli_fetch_array($query)) {
                $npsn=$data['npsn'];
                $school_name=$data['school_name'];
                echo '<option value="'.$npsn.'">'.$school_name.'</option>';
            }
        }
        
    }else{
        //Jika Kode Kab/Kota Tidak Ada
        echo '<option value="">Pilih</option>';
    }
    
?>