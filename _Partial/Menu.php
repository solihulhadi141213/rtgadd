<?php
    if(empty($_GET['Page'])){
        $PageMenu="";
    }else{
        $PageMenu=$_GET['Page'];
    }
    if(empty($_GET['Sub'])){
        $SubMenu="";
    }else{
        $SubMenu=$_GET['Sub'];
    }

    //Apakah User Seorang Admin
    $access_client = GetDetailData($Conn, 'access', 'id_access', $SessionIdAccess, 'access_client');
    if(empty($access_client)){

        //Jika Seorang Admin
        include "_Partial/MenuAdmin.php";
    }else{
        //Jika bukan admin
        include "_Partial/MenuClient.php";
    }
    
?>