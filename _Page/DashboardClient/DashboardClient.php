<?php
    //Routing Dashboard Berdasarkan Level Akses Client

    //Tangkap level dan id_region client
    $level_access_lient     = GetDetailData($Conn, 'access_client', 'id_access', $SessionIdAccess, 'level');
    $id_region_access_lient = GetDetailData($Conn, 'access_client', 'id_access', $SessionIdAccess, 'level');

    if($level_access_lient=="National"){
        include "_Page/DashboardClient/National.php";
    }
    if($level_access_lient=="Province"){
        include "_Page/DashboardClient/Province.php";
    }
    if($level_access_lient=="Province"){
        include "_Page/DashboardClient/District.php";
    }
?>