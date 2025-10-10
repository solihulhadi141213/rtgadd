<?php
    //Koneksi
    include "../../_Config/Connection.php";

    if(empty($_POST['school_level'])){
        $school_level="";
    }else{
         $school_level=$_POST['school_level'];
    }
    echo '<option value="">Semua Jenjang</option>';
    $query = mysqli_query($Conn, "SELECT DISTINCT school_level FROM school ");
    while ($data = mysqli_fetch_array($query)) {
        $school_level_list = $data['school_level'];
        if($school_level_list==$school_level){
            echo '<option selected value="'.$school_level_list.'">'.$school_level_list.'</option>';
        }else{
            echo '<option value="'.$school_level_list.'">'.$school_level_list.'</option>';
        }
        
    }
?>