<?php
    if(empty($_GET['Page'])){
        //Routing Dashboard berdasarkan Level akses

        //Tangkap 'access_client'
        $access_client = GetDetailData($Conn, 'access', 'id_access', $SessionIdAccess, 'access_client');
        if(empty($access_client)){

            //Jika Kosong maka dia Seorang Admin
            include "_Page/Dashboard/Dashboard.php";
        }else{
            //Jika bukan admin
            include "_Page/DashboardClient/DashboardClient.php";
        }
        
    }else{
        $Page=$_GET['Page'];
        //Index Halaman
        $page_arry=[
            "MyProfile"             =>  "_Page/MyProfile/MyProfile.php",
            "DashboardProvince"     =>  "_Page/DashboardProvince/DashboardProvince.php",
            "DashboardDistrict"     =>  "_Page/DashboardDistrict/DashboardDistrict.php",
            "AksesFitur"            =>  "_Page/AksesFitur/AksesFitur.php",
            "AksesEntitas"          =>  "_Page/AksesEntitas/AksesEntitas.php",
            "Akses"                 =>  "_Page/Akses/Akses.php",
            "Client"                =>  "_Page/Client/Client.php",
            "SettingGeneral"        =>  "_Page/SettingGeneral/SettingGeneral.php",
            "SettingEmail"          =>  "_Page/SettingEmail/SettingEmail.php",
            "Wilayah"               =>  "_Page/Wilayah/Wilayah.php",
            "Sekolah"               =>  "_Page/Sekolah/Sekolah.php",
            "Jabatan"               =>  "_Page/Jabatan/Jabatan.php",
            "Instansi"              =>  "_Page/Instansi/Instansi.php",
            "JabatanPerWilayah"     =>  "_Page/JabatanPerWilayah/JabatanPerWilayah.php",
            "JabatanPerInstansi"    =>  "_Page/JabatanPerInstansi/JabatanPerInstansi.php",
            "AbkPerSekolah"         =>  "_Page/AbkPerSekolah/AbkPerSekolah.php",
            "CornJob"               =>  "_Page/CornJob/CornJob.php",
            "GeoJson"               =>  "_Page/GeoJson/GeoJson.php",
            "GeoJsonBps"            =>  "_Page/GeoJsonBps/GeoJsonBps.php",
            "MapViewer"             =>  "_Page/MapViewer/MapViewer.php",
            "Aktivitas"             =>  "_Page/Aktivitas/Aktivitas.php",
            "Help"                  =>  "_Page/Help/Help.php",
        ];

        //Tangkap 'Page'
        $Page = !empty($_GET['Page']) ? $_GET['Page'] : "";

        //Kondisi Pada masing-masing Page
        if (array_key_exists($Page, $page_arry)) { 
            include $page_arry[$Page]; 
        } else { 
            include "_Page/Error/PageNotFound.php";
        }
    }
?>