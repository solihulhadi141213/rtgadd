<?php
    //Koneksi
    include "../../_Config/Connection.php";

    //Jika Kode Provinsi Ada
    if(!empty($_POST['province_code'])){
        $province_code = $_POST['province_code'];

        //Tampilkan Data Kbupaten
        echo '<option value="">Pilih</option>';
        $query = mysqli_query($Conn, "SELECT district_code, district_name FROM region WHERE category='District' AND province_code='$province_code' ORDER BY district_name");
        while ($data = mysqli_fetch_array($query)) {
            $district_code=$data['district_code'];
            $district_name=$data['district_name'];
            echo '<option value="'.$district_code.'">'.$district_name.'</option>';
        }
    }else{
        //Jika Kode Provinsi Tidak Ada
        echo '<option value="">Pilih</option>';
    }
    
?>